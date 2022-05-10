<?php
/**
 * MTS Simple Booking データベースアクセスモジュール
 *
 * @Filename	mtssb-booking.php
 * @Date		2012-05-01
 * @Author		S.Hayashi
 *
 * Updated to 1.34.0 on 2020-08-18
 * Updated to 1.31.1 on 2020-07-25
 * Updated to 1.31.0 on 2019-05-17
 * Updated to 1.27.0 on 2017-08-02
 * Updated to 1.25.0 on 2016-10-27
 * Updated to 1.24.0 on 2016-07-26
 * Updated to 1.23.0 on 2016-01-15
 * Updated to 1.21.0 on 2014-12-27
 * Updated to 1.19.0 on 2014-10-28
 * Updated to 1.17.0 on 2014-08-08
 * Updated to 1.16.0 on 2014-06-09
 * Updated to 1.15.0 on 2014-01-30 MTSSB_Booking,MTS_WPDate
 * Updated to 1.14.0 on 2014-01-16
 * Updated to 1.13.0 on 2014-01-03
 * Updated to 1.11.0 on 2013-11-21 MTS_WPDate
 * Updated to 1.9.0 on 2013-07-18
 * Updated to 1.8.1 on 2013-07-08
 * Updated to 1.8.0 on 2013-05-25
 * Updated to 1.7.0 on 2013-05-11
 * Updated to 1.6.5 on 2013-04-29
 * Updated to 1.6.0 on 2013-03-20
 * Updated to 1.4.5 on 2013-02-21
 * Updated to 1.4.0 on 2013-01-28
 * Updated to 1.3.0 on 2012-12-30
 * Updated to 1.2.0 on 2012-12-26
 * Updated to 1.1.5 on 2012-12-03
 * Updated to 1.1.0 on 2012-10-03
 */
if (!class_exists('MTSBB_Option')) {
    require_once(dirname(__FILE__) . '/mtssb-options.php');
}
if (!class_exists('MTS_Bill')) {
    require_once('lib/MTSBill.php');
}
if (!class_exists('MTS_WPTime')) {
    require_once('lib/MTSWPTime.php');
}

class MTSSB_Booking
{
    const VERSION = '1.25';
	const BOOKING_TABLE = 'mtssb_booking';
	const TABLE_VERSION = '1.1';

	// table.user_idの設定値
	const USER_ADJUSTED = -1;
	const SERIES_BOOKING = -2;

	// 予約データの処理ステータスビット情報(confirmedカラム)
	const NOT_CONFIRM = 0;
	const CONFIRMED = 1;
	const AWAKED = 2;

	/**
	 * Common private valiable
	 */
	protected $domain;
	protected $plugin_url;

	// Table names
	protected $tblBooking;

	// オプション形式を操作する元のオブジェクト
	protected $option = null;

	// 内部処理用のデータ構造(予約編集データ格納)
	protected $booking = array();

    // 料金明細オブジェクト
    protected $bill = null;

	// DB保存データ形式として格納
	private $record = array();

	/**
	 * Constructor
	 */
	public function __construct()
    {
		global $wpdb, $mts_simple_booking;

		$this->domain = MTS_Simple_Booking::DOMAIN;
		$this->plugin_url = $mts_simple_booking->plugin_url;

		$this->tblBooking = $wpdb->prefix . self::BOOKING_TABLE;

		$this->_install_table();

		// オプション
		$this->option = new MTSSB_Option();
	}

    /**
     * 予約品目と日時から予約状況を検索して戻す
     */
    public function getBookingStatus($articleId, $daytime)
    {
        // 予約品目の指定日時の予約状況
        $oStatus = (object) array(
            'articleId' => 0,
            'schedule' => array(),
            'count' => array(),
        );

        $oStatus->articleId = $articleId;

        // 予約スケジュールデータを取得する
        $dayTime = strtotime(date_i18n('Y-n-j', $daytime));
        $keyName = MTS_Simple_Booking::SCHEDULE_NAME . date_i18n('Ym', $dayTime);
        $monSchedule = get_post_meta($articleId, $keyName, true);

        $day = date_i18n('d', $dayTime);
        if (isset($monSchedule[$day])) {
            $oStatus->schedule = $monSchedule[$day];
        }

        // 指定日の予約件数データを取得する
        $dayCount = $this->get_reserved_day_count($dayTime);

        if ($dayCount) {
            foreach ($dayCount as $bookingTime => $timeCount) {
                if (isset($timeCount[$articleId])) {
                    $oStatus->count[$bookingTime] = $timeCount[$articleId];
                }
            }
        }

        return $oStatus;
	}

	/**
	 * 重複予約データを検索する
	 */
	public function findMultipleBooking($articleId, $bookingTime, $name='', $email='', $tel='')
    {
        global $wpdb;

        $condition = '';
        if ($name) {
            $condition .= sprintf(" AND client LIKE '%%%s%%'", addslashes($name));
        }
        if ($email) {
            $condition .= sprintf(" AND client LIKE '%%%s%%'", addslashes($email));
        }
        if ($tel) {
            $condition .= sprintf(" AND client LIKE '%%%s%%'", addslashes($tel));
        }

        $sql = $wpdb->prepare(
            "SELECT count(*) FROM {$this->tblBooking} "
            . "WHERE article_id=%d AND booking_time=%d", $articleId, $bookingTime) . $condition;

        $number = $wpdb->get_col($sql);

        return intval($number[0]);
    }

	/**
	 * 事前メール対象予約データを検索する
	 *
	 * @artice_id
	 * @nearTime
	 * @farTime
	 */
	public function findAwaking($articleId, $nearTime, $farTime)
	{
		global $wpdb;

		$sql = $wpdb->prepare(
			"SELECT booking_id FROM {$this->tblBooking} "
			. "WHERE parent_id=0 AND article_id=%s AND booking_time>%s AND booking_time<=%s AND confirmed&%s=0 "
			. "ORDER BY booking_id", $articleId, $nearTime, $farTime, self::AWAKED);

		$ids = $wpdb->get_results($sql, ARRAY_A);

		return $ids;
	}

	/**
	 * 予約IDとメールアドレスから予約データを検索する
	 *
	 * @reserve_id	booking_id下3桁
	 * @email		メールアドレス
	 * @time		予約日時以上(検索条件追加)
	 */
	public function find_by_reserveid($reserve_id, $email, $time=0)
	{
		global $wpdb;

		$sql = $wpdb->prepare("
			SELECT * FROM {$this->tblBooking}
			WHERE mod(booking_id, 1000)=%d AND client like %s AND booking_time>=%d
			ORDER BY booking_time DESC;",
			$reserve_id, "%$email%", $time);

		$records = $wpdb->get_results($sql, ARRAY_A);

		if (empty($records)) {
			return false;
		}

		// 取得データをbookingデータに変換する
		$bookings = array();
		foreach ($records as $record) {
			$booking = $this->new_booking($record['booking_time'], $record['article_id']);

			$record['options'] = unserialize($record['options']);
			$record['client'] = unserialize($record['client']);

			$bookings[$record['booking_id']] = $this->array_merge_default($booking, $record);
		}

		return $bookings;
	}

	/**
	 * 予約項目別月間予約件数データを取得
	 *
	 * @stay		Y-m-d
	 * $return		array(unixtime => array(article_id => array(rcount, rnumber)));
	 */
	public function get_reserved_count($year, $month) {
		global $wpdb;

		$sql = "
			SELECT (booking_time - booking_time % 86400) AS booking_daytime,article_id,count(booking_time) AS rcount,sum(number) AS rnumber
			FROM $this->tblBooking
			WHERE booking_time>=" . mktime(0, 0, 0, $month, 1, $year) . " AND booking_time<" . mktime(0, 0, 0, $month + 1, 1, $year) . "
			GROUP BY booking_daytime,article_id
			ORDER BY booking_daytime,article_id";

		$booking = $wpdb->get_results($sql, ARRAY_A);

		$reserved = array();
		foreach ($booking as $daybook) {
			$reserved[$daybook['booking_daytime']][$daybook['article_id']] = array('count' => $daybook['rcount'], 'number' => $daybook['rnumber']);
		}

		return $reserved;
	}

	/**
	 * 指定日付の予約件数データを取得
	 *
	 * @daytime		unix time
	 * $return		array(unixtime => array(article_id => array(rcount, rnumber)));
	 */
	public function get_reserved_day_count($daytime) {
		global $wpdb;

		$bookings = $wpdb->get_results($wpdb->prepare("
			SELECT booking_time,article_id,count(booking_time) AS rcount,sum(number) AS rnumber
			FROM $this->tblBooking
			WHERE booking_time>=%d AND booking_time<%d
			GROUP BY booking_time,article_id
			ORDER BY booking_time,article_id", $daytime, $daytime + 86400), ARRAY_A);

		$reserved = array();
		foreach ($bookings as $booking) {
			$reserved[$booking['booking_time']][$booking['article_id']] = array('count' => $booking['rcount'], 'number' => $booking['rnumber']);
		}

		return $reserved;
	}

	/**
	 * 全予約数を戻す(booking_list用)
	 *
	 * @conditions
	 */
	public function get_booking_count($conditions = '') {
		global $wpdb;

		$number = $wpdb->get_col(
			"SELECT count(*) FROM $this->tblBooking WHERE user_id>=0"
			 . (!empty($conditions) ? " AND {$conditions}" : '')
		);

		return intval($number[0]);
	}

	/**
	 * 指定月間の予約数を戻す(booking_list用)
	 *
	 */
	public function get_booking_count_monthly($year, $month) {
		global $wpdb;

		$number = $wpdb->get_col($wpdb->prepare("
			SELECT count(*) FROM $this->tblBooking
			WHERE booking_time>=%d AND booking_time<%d AND user_id>=0",
			 mktime(0, 0, 0, $month, 1, $year), mktime(0, 0, 0, $month + 1, 1, $year)));

		return intval($number[0]);
	}

    /**
     * 特定ユーザーの予約数を戻す(users_page用)
     *
     */
    public function get_booking_users_count($userId=0)
    {
        global $wpdb;

        $user_id = intval($userId);

        if ($user_id <= 0) {
            return false;
        }

        $number = $wpdb->get_var($wpdb->prepare("
			SELECT count(*) FROM $this->tblBooking
			WHERE user_id=%s", $user_id));

        return $number;
    }

    /**
     * 特定ユーザーの予約データを戻す
     *
     */
    public function get_users_booking($userId, $offset, $limit, $order=array())
    {
        // 予約利用日降順
        if (empty($order)) {
            $order = array(
                'key' => 'booking_time',
                'direction' => 'desc',
            );
        }

        // ユーザー指定
        $condition = sprintf('user_id=%d', $userId);

        // 予約データの取得
        $lists = $this->get_booking_list($offset, $limit, $order, $condition);

        // 連続予約の時間割を取得する
        foreach ($lists as $booking_id => &$booking) {
            $booking['series'] = $this->_get_series_data($booking_id);
        }

        return $lists;
    }

	/**
	 * 指定日の予約データを取得する
	 *
	 * @daytime
	 */
	public function get_booking_of_theday($daytime, $article_ids='') {
		global $wpdb;

		$conditions = '1=1';

		if (!empty($article_id)) {
			$conditions = "article_id in ($article_ids)";
		}

        $data = $wpdb->get_results($wpdb->prepare("
			SELECT booking_id,booking_time,confirmed,parent_id,article_id,user_id,number,options,client,created
			FROM $this->tblBooking
			WHERE booking_time>=%d AND booking_time<%d AND %s
			ORDER BY article_id ASC, booking_time ASC", $daytime, $daytime + 86400, $conditions), ARRAY_A);

		foreach ($data as $key => $booking) {
			$data[$key]['options'] = unserialize($booking['options']);
			$data[$key]['client'] = unserialize($booking['client']);
		}

		return $data;
	}

	/**
	 * 指定日時の予約データを取得する
	 *
	 * @daytime
	 */
	public function get_booking_of_thetime($thetime, $article_ids='') {
		global $wpdb;

		$conditions = '1=1';

		if (!empty($article_ids)) {
            $conditions = "b1.article_id in ($article_ids)";
		}

        $data = $wpdb->get_results($wpdb->prepare("
            SELECT b1.booking_id,b1.booking_time,b1.confirmed,b1.parent_id,b1.article_id,b1.user_id,b1.number,b1.options,
                b1.client,b1.note,b1.created,b2.client AS parent
            FROM $this->tblBooking AS b1
            LEFT JOIN $this->tblBooking AS b2 ON b1.parent_id=b2.booking_id
            WHERE b1.booking_time=%d AND $conditions
            ORDER BY b1.article_id ASC, b1.booking_id ASC", $thetime), ARRAY_A);

		foreach ($data as $key => $booking) {
			$data[$key]['options'] = unserialize($booking['options']);
			$data[$key]['client'] = unserialize($booking['client']);
            $data[$key]['parent'] = unserialize($booking['parent']);
		}

		return $data;
	}

	/**
	 * 予約データを取得する
	 *
	 * @offset
	 * @limit
	 * @order
	 * @article_id
	 */
	public function get_booking_list($offset, $limit, $order, $conditions='') {
		global $wpdb;

		$sql = $wpdb->prepare("
			SELECT booking_id,booking_time,confirmed,parent_id,article_id,user_id,number,options,client,note,created,
				Post.post_title AS article_name
			FROM $this->tblBooking
			JOIN {$wpdb->posts} AS Post ON article_id=Post.ID
			WHERE user_id>=0" . ($conditions  ? " AND $conditions" : '') . "
			ORDER BY {$order['key']} {$order['direction']}
			LIMIT %d, %d", $offset, $limit);

		$data = $wpdb->get_results($sql, ARRAY_A);

		$booking_data = array();
		foreach ($data as $key => $booking) {
			$data[$key]['options'] = unserialize($booking['options']);
			$data[$key]['client'] = unserialize($booking['client']);
			$booking_data[$booking['booking_id']] = $data[$key];
		}

		return $booking_data;
	}

	/**
	 * 連続予約コマ数を戻す(booking_list用)
	 *
	 * @booking_ids		予約IDの配列
	 */
	public function get_booking_series_count($booking_ids = array()) {
		global $wpdb;

		$series = array();

		if (is_array($booking_ids)) {
			$csvids = implode(',', $booking_ids);

			$sql = "
				SELECT b1.booking_id,count(b2.parent_id) AS series
				FROM $this->tblBooking AS b1
				LEFT JOIN $this->tblBooking AS b2 ON b1.booking_id=b2.parent_id"
				. (empty($csvids) ? '' : " WHERE b1.booking_id in($csvids)")
				. " GROUP BY b2.parent_id, b1.booking_id";

			$series_data = $wpdb->get_results($sql, ARRAY_A);

			$series = array();
			foreach ($series_data as $number) {
				$series[$number['booking_id']] = $number['series'];
			}
		}

		return $series;
	}

	/**
	 * 予約データの読み込み
	 *
	 * @booking_id
	 * @return $bookingタイプ
	 */
	public function get_booking($booking_id) {
		global $wpdb;

		$record = $wpdb->get_row($wpdb->prepare("
			SELECT * FROM {$this->tblBooking}
			WHERE booking_id=%d", intval($booking_id)), ARRAY_A);

		if (empty($record)) {
			return false;
		}

		$booked = $record;
		$booking = $this->new_booking($booked['booking_time'], $booked['article_id']);

		$booked['options'] = unserialize($record['options']);
        $booked['client'] = unserialize($record['client']);

		// オプションデータをオプションオブジェクトにセットする
		$booking = $this->array_merge_default($booking, $booked);

		return $booking;
	}

	/**
	 * 予約の調整データを取得する
	 *
	 * @article_id
	 * @booking_time
	 */
	public function get_adjustment($article_id, $booking_time) {
		global $wpdb;

		$data = $wpdb->get_results($wpdb->prepare("
			SELECT *
			FROM $this->tblBooking
			WHERE article_id=%d AND booking_time=%d AND user_id=%d
			ORDER BY booking_id DESC", $article_id, $booking_time, self::USER_ADJUSTED), ARRAY_A);

		return $data;

	}

	/**
	 * 予約の調整処理
	 *
	 * @article_id
	 * @restriction		capacity or quantity
	 * @booking_time
	 * @number
	 */
	public function adjust_booking($article_id, $restriction, $booking_time, $number) {
		// 調整データを取り出す
		$adjustments = $this->get_adjustment($article_id, $booking_time);

		// 調整数が0で調整データあれば調整データを削除する
		if ($number <= 0) {
			if (!empty($adjustments)) {
				foreach ($adjustments as $booking) {
					if ($this->del_booking($booking['booking_id']) === false) {
						return false;
					}
				}
			}
			return true;
		}

		// 予約タイプが収容人数の調整
		if ($restriction == 'capacity') {
			if (empty($adjustments)) {
				$this->booking = $this->_new_adjustment($booking_time, $article_id);
				$this->booking['number'] = $number;
				if ($this->add_booking() === false) {
					return false;
				}
			} else {
				$this->booking = $adjustments[0];
				if ($this->booking['number'] != $number) {
					$this->booking['number'] = $number;
					if ($this->save_booking() === false) {
						return false;
					}
				}
			}
		}

		// 予約タイプが予約件数の調整
		else {
			$count = count($adjustments);
			// 予約調整データが多い場合は削除する
			if ($number < $count) {
				foreach ($adjustments as $key => &$booking) {
					if ($key < $count - $number) {
						if ($this->del_booking($booking['booking_id']) === false) {
							return false;
						}
					}
				}
			}
			// 予約調整データが少ない場合は追加する
			else if ($count < $number) {
				for ($i = $number - $count; 0 < $i; $i--) {
					$this->booking = $this->_new_adjustment($booking_time, $article_id);
					if ($this->add_booking() === false) {
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * 調整データを取得する
	 *
	 */
	protected function _new_adjustment($booking_time, $article_id) {
		$booking = $this->new_booking($booking_time, $article_id);
		$booking['user_id'] = self::USER_ADJUSTED;
		$booking['confirmed'] = self::CONFIRMED;
		$booking['client'] = null;
		return $booking;
	}

    /**
     * 予約確認済みマークをセットする
     *
     */
    public function setConfirmed($booking_id)
    {
        global $wpdb;

		$sql = $wpdb->prepare("UPDATE {$this->tblBooking} SET confirmed=confirmed|%d WHERE booking_id=%d",
			self::CONFIRMED, $booking_id);

		$ret = $wpdb->query($sql);

        return $ret;
    }

	/**
	 * 事前メール送信済みマークをセットする
	 *
	 */
	public function setAwaked($booking_id)
	{
		global $wpdb;

		$sql = $wpdb->prepare("UPDATE {$this->tblBooking} SET confirmed=confirmed|%d WHERE booking_id=%d",
			self::AWAKED, $booking_id);

		$ret = $wpdb->query($sql);

		return $ret;
	}

	/**
	 * オプション追加の追加時間割コマ数を求める
	 *
	 * @options		$this->booking['options']
	 */
	protected function _get_series_number()
	{
		if (empty($this->booking['options'])) {
			return 0;
		}

		$options = $this->booking['options'];

		// 連続コマ数を求める
		$series_number = 0;
		foreach ($options as $option) {
			switch ($option->getType()) {
				case 'radio':
				case 'select':
					// 選択された項目の追加時間コマ数を読込み追加する
					$number = $option->getTimetable($option->getValue());
					if (0 < $number) {
						$series_number += $number;
					}
					break;
				case 'check':
					// 選択された項目を取得し追加時間コマ数を読込み追加する
					foreach ($option->getValue(true) as $fieldkey) {
						$number = $option->getTimetable($fieldkey);
						if (0 < $number) {
							$series_number += $number;
						}
					}
					break;
				default:
					break;
			}
		}

		return $series_number <= 0 ? 0 : $series_number;
	}

	/**
	 * 予約データの新規追加(時間割連続予約機能)
	 *
	 */
	public function add_series_booking()
	{
		// 予約データの追加
		$parent_id = $this->add_booking();
		if ($parent_id <= 0) {
			throw new Exception('ADDING_BOOKING_FAILED');
		}

		// オプション追加の時間割追加コマ数を取得、追加時間がなければリターンする
		$series_number = $this->_get_series_number();

		if ($series_number <= 0) {
			return $parent_id;
		}

		// 予約品目データの取得
		$article_id = $this->booking['article_id'];
		$article = MTSSB_Article::get_the_article($article_id);

		// 新しい書込みデータの準備
		$series = $this->new_booking();
		$series['article_id'] = $article_id;
		$series['user_id'] = self::SERIES_BOOKING;
		$series['number'] = $this->booking['number'];
		$series['parent_id'] = $parent_id;
		$series['options'] = $series['client'] = null;

		$booking_time = $this->booking['booking_time'];
		$temp = $this->booking;
		$this->booking = $series;

		// 予約日のunix time
		$booking_daytime = strtotime(date_i18n('Y-m-d', $booking_time));

		// 予約時間割の連続取得登録
		foreach ($article['timetable'] as $time) {
			$series_time = $booking_daytime + $time;

			// 予約時間以降の時間割を予約登録する
			if ($booking_time < $series_time) {
				$this->booking['booking_id'] = 0;
				$this->booking['booking_time'] = $series_time;

				// 予約データの登録
				$booking_id = $this->add_booking();
				if (empty($booking_id)) {
					$this->booking = $temp;
					throw new Exception('ADDING_BOOKING_FAILE');
				}

				// 予約コマ数がなければ予約登録を終了する
				if (--$series_number <= 0) {
					break;
				}
			}
		}

		$this->booking = $temp;
		return $parent_id;
	}

	/**
	 * 予約データの新規追加
	 *
	 */
	public function add_booking() {
		global $wpdb;

		$this->_recordData();
		$this->record['created'] = current_time('mysql');

		$result = $wpdb->insert($this->tblBooking, $this->record,
			array('%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s'));

		if (!$result) {
			return false;
		}

		$insert_id = $this->booking['booking_id'] = $wpdb->insert_id;
		return $insert_id;
	}

	/**
	 * 予約時間割連続データの取得
	 *
	 */
	protected function _get_series_data($parent_id=0)
	{
		global $wpdb;

		$series = array();
		if (0 < $parent_id) {
			$series = $wpdb->get_results($wpdb->prepare("
				SELECT * FROM {$this->tblBooking}
				WHERE parent_id=%d
				ORDER BY booking_time", $parent_id), ARRAY_A);
		}

		return $series;
	}

	/**
	 * 予約データの保存(時間割連続予約機能)
	 *
	 */
	public function save_series_booking()
	{
		// 編集データの保存
		$parent_id = $this->save_booking();

		// データ内容が同じ場合はfalseで戻るので以下をエラー処理をコメントアウトする
		//if (empty($parent_id)) {
		//	throw new Exception('SAVING_BOOKING_FAILED');
		//}

		$parent_id = $this->booking['booking_id'];

		// オプション追加の時間割追加コマ数を取得
		$series_number = $this->_get_series_number();

		// 旧連続予約データを取得する
		$series_data = $this->_get_series_data($this->booking['booking_id']);

		// 追加時間がなく　かつ　登録されている連続データがなければリターンする
		if ($series_number <= 0 && count($series_data) <= 0) {
			return $parent_id;
		}

		// 連続予約の処理
		if (0 < $series_number) {
			// 予約品目データの取得
			$article_id = $this->booking['article_id'];
			$article = MTSSB_Article::get_the_article($article_id);

			// 新しい書込みデータの準備
			$series = $this->new_booking();
			$series['article_id'] = $article_id;
			$series['user_id'] = self::SERIES_BOOKING;
			$series['number'] = $this->booking['number'];
			$series['parent_id'] = $parent_id;
			$series['options'] = $series['client'] = null;

			$booking_time = $this->booking['booking_time'];
			$temp = $this->booking;

			// 予約日のunix time
			$booking_daytime = strtotime(date_i18n('Y-m-d', $booking_time));

			// 予約日の予約時間を全て確認する
			foreach ($article['timetable'] as $time) {
				// 予約時間
				$series_time = $booking_daytime + $time;

				// 予約時間以降の時間割を予約登録する
				if ($booking_time < $series_time) {
					// 既存連続予約データ配列から１件取り出す
					$series_booking = array_shift($series_data);

					// 未登録なら新規追加する
					if (empty($series_booking)) {
						$this->booking = $series;
						$this->booking['booking_id'] = 0;
						$this->booking['booking_time'] = $series_time;

						$booking_id = $this->add_booking();

						// 新規登録エラー
						if (empty($booking_id)) {
							$this->booking = $temp;
							throw new Exception('ADDING_BOOKING_FAILE');
						}
					}

					// 既存データの内容を書き換える
					else {
						// 予約日時が異なれば書き換える
						$this->booking = $series_booking;
						if ($this->booking['booking_time'] != $series_time || $this->booking['number'] != $temp['number']) {
							$this->booking['booking_time'] = $series_time;
							$this->booking['number'] = $temp['number'];

							// 編集データの保存
							$booking_id = $this->save_booking();

							// 保存エラー
							if ($booking_id <= 0) {
								$this->booking = $temp;
								throw new Exception('SAVING_BOOKING_FAILED');
							}
						}
					}

					// 予約コマ数がなければ終了する
					if (--$series_number <= 0) {
						break;
					}
				}
			}

			$this->booking = $temp;
		}

		// 不要になった予約連続データを削除する
		if (!empty($series_data)) {
			foreach ($series_data as $booking) {
				$this->del_booking($booking['booking_id']);
			}
		}

		return $parent_id;
	}

	/**
	 * Save booking data
	 *
	 */
	public function save_booking() {
		global $wpdb;

		$this->_recordData();

		$where = array('booking_id' => $this->record['booking_id']);
		unset($this->record['booking_id']);

		$result = $wpdb->update($this->tblBooking, $this->record, $where,
			array('%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s'), array('%d'));

		if (!$result) {
			return false;
		}

		return $this->booking['booking_id'];
	}

	/**
	 * Delete booking data
	 *
	 */
	public function del_booking($id=0) {
		global $wpdb;

		$booking_id = intval($id);
		if ($booking_id <= 0) {
			return false;
		}

		$condition = sprintf('booking_id=%d OR parent_id=%d', $booking_id, $booking_id);

		$result = $wpdb->query("
			DELETE FROM {$this->tblBooking}
			WHERE " . $condition);

		return $result;
	}

	/**
	 * 入力データを正規化し、bookingオブジェクトデータにして戻す
	 *
	 * @post		管理画面 入力postデータ
	 * @timeflg		true:管理画面 false:フロントフォーム
	 * @rate		人数カウントレートの配列
	 *
	 * @return		bookingデータを戻す
	 */
	public function normalize_booking($post, $count=array())
    {
        $post = stripslashes_deep($post);

		// 予約日時の計算
		if (isset($post['booking_time'])) {
			$booking_time = intval($post['booking_time']);
		} else {
			// 管理画面からの予約
			$booking_time = mktime(0, 0, 0, $post['month'], $post['day'], $post['year']) + intval($post['timetable']);
		}

		$booking = $this->new_booking($booking_time, intval($post['article_id']));

		// 入力データの正規化
		if (isset($post['booking_id'])) {
			$booking['booking_id'] = intval($post['booking_id']);
		}

		if (isset($post['user_id'])) {
			$booking['user_id'] = intval($post['user_id']);
		}

		if (isset($post['number'])) {
			$booking['number'] = intval(trim(mb_convert_kana($post['number'], 'as')));
		}

		if (isset($post['confirmed'])) {
			$booking['confirmed'] = intval($post['confirmed']);
		}

		// オプションデータ
		if (isset($post['options'])) {
			foreach ($booking['options'] as $option) {
				$keyname = $option->getKeyname();
				if (isset($post['options'][$keyname])) {
					$val = $option->normalize($post['options'][$keyname]);
					$option->setValue($val);
				}
			}
		}

		// クライアントデータ
		$client = &$booking['client'];
		foreach ($post['client'] as $keyname => $val) {
			switch ($keyname) {
				case 'company' :
				case 'email' :
				case 'postcode' :
				case 'address1' :
				case 'address2' :
				case 'tel' :
					$client[$keyname] = trim(mb_convert_kana($val, 'as'));
					break;
				case 'adult' :
				case 'child' :
				case 'baby' :
				case 'car' :
				case 'newuse' :
					$client[$keyname] = intval(trim(mb_convert_kana($val, 'as')));
					break;
				//case 'name' :
                case 'sei' :
                case 'mei' :
					$client[$keyname] = trim(mb_convert_kana($val, 's'));
					break;
				//case 'furigana' :
                case 'sei_kana' :
                case 'mei_kana' :
					$client[$keyname] = trim(mb_convert_kana($val, 'asKCV'));
					break;
				case 'birthday' :
                    //$oDate = date_create(sprintf('%d-%d-%d', intval($val['year']), intval($val['month']), intval($val['day'])));
                    //$client['birthday'] = $oDate->format('Y-m-d');
                    //var_dump($client['birthday']);
                    //var_dump(sprintf('%04d-%02d-%02d', intval($val['year']), intval($val['month']), intval($val['day'])));
                    $client['birthday'] = sprintf('%d-%d-%d', intval($val['year']), intval($val['month']), intval($val['day']));
					break;
				case 'gender' :
					$client['gender'] = ($val != 'male' && $val != 'female') ? '' : $val;
					break;
				case 'transaction_id' :
					$client['transaction_id'] = trim(mb_convert_kana($val, 'as'));
					break;
				default:
					break;
			}
		}

        // 姓名を名前へコピーする
        $client['name'] = $client['sei'] . (empty($client['sei']) ? '' : ' ') . $client['mei'];
		$client['furigana'] = $client['sei_kana'] . (empty($client['sei_kana']) ? '' : ' ') . $client['mei_kana'];

		// ブラウザ、IPアドレスの設定
		$client['user_agent'] = trim($_SERVER['HTTP_USER_AGENT']);
		$client['remote_addr'] = trim($_SERVER['REMOTE_ADDR']);

		// 人数計算
		if (!empty($count)) {
			$booking['number'] = intval($client['adult']) * $count['adult']
			 + intval($client['child']) * $count['child']
			 + intval($client['baby']) * $count['baby'];
		}

		// メモ書き500文字以内
		$booking['note'] = mb_substr(trim($post['note']), 0, 500);

		return $booking;
	}

	/**
	 * bookingデータをテーブルに登録するデータ形式に変換する
	 *
	 *
	 */
	 protected function _recordData() {

		$record = array();

		$record['booking_id'] = $this->booking['booking_id'];
		$record['booking_time'] = $this->booking['booking_time'];
		$record['confirmed'] = $this->booking['confirmed'];
		$record['parent_id'] = $this->booking['parent_id'];
		$record['article_id'] = $this->booking['article_id'];
		$record['user_id'] = $this->booking['user_id'];
		$record['number'] = $this->booking['number'];
		if ($this->booking['user_id'] == self::USER_ADJUSTED) {
			$record['options'] = null;
			$record['client'] = null;
		} else if (!is_null($this->booking['client'])) {
			$record['options'] = serialize(MTSSB_Option::recordSet($this->booking['options']));
			$client = $this->booking['client'];
			$client['birthday'] = $this->booking['client']['birthday'];
			$record['client'] = serialize($client);
		}
		$record['note'] = $this->booking['note'];

		$this->record = $record;
	}

	/**
	 * 新しい予約
	 *
	 */
	public function new_booking($daytime=0, $article_id=0)
	{
		$new = array(
			'booking_id' => 0,
			'booking_time' => $daytime == 0 ? mktime(0, 0, 0, date_i18n('n'), date_i18n('j'), date_i18n('Y')) : $daytime,
			'article_id' => $article_id,
			'user_id' => 0,
			'number' => 0,
			'confirmed' => 0,
			'parent_id' => 0,
			'options' => array(), //$this->new_options(),
			'client' => array(
				'company' => apply_filters('mtssb_booking_new_company', ''),
				'sei' => '',
				'mei' => '',
				'sei_kana' => '',
				'mei_kana' => '',
				'name' => '',
				'furigana' => '',
				'birthday' => '0-0-0',
				'gender' => '',
				'email' => '',
				'postcode' => '',
				'address1' => '',
				'address2' => '',
				'tel' => '',
				'newuse' => apply_filters('mtssb_booking_new_newuse', 0),
				'adult' => apply_filters('mtssb_booking_new_adult', 1),
				'child' => apply_filters('mtssb_booking_new_child', 0),
				'baby' => apply_filters('mtssb_booking_new_baby', 0),
				'car' => 0,
				'transaction_id' => '',
				'user_agent' => '',
				'remote_addr' => '',
			),
			'note' => '',
			'created' => '',
		);

		// オプションオブジェクトをセットする
		if (0 < $article_id) {
			$article = MTSSB_Article::get_the_article($article_id);
			if ($article['addition']->isOption()) {
				$new['options'] = $this->option->loadOption($article['addition']->option_name);
			}
		}

		return $new;
	}

	/**
	 * 空のオプションデータを戻す
	 *
	 */
	public function new_options() {

		// 初期化されたオプションデータセットを取得する
		return $this->option->optionSet();
	}

	/**
	 * 初期値がセットされた配列にマージする
	 *
	 * @default		初期値がセットされた配列(初期$bookingデータ opitonsがオブジェクト)
	 * $ary			マージする配列(DBから読み込んだデータ optionsが配列)
	 */
	protected function array_merge_default(&$default=array(), $ary=array()) {

		// 登録データを操作データに変換する
		foreach ($default as $key => &$val) {
			if (isset($ary[$key])) {
				// オプションオブジェクトへの変換
				if ($key == 'options') {
                    $this->_set_options($val, $ary['options']);
                // clientデータをセットする
                } elseif ($key == 'client') {
                    $this->_set_client($val, $ary['client']);
				// 配列データはマージする
				} else if (is_array($default[$key])) {
					$intersect = array_intersect_key($ary[$key], $default[$key]);
					$default[$key] = array_merge($default[$key], $intersect);
				// オブジェクト、配列以外はそのまま
				} else {
					$default[$key] = $ary[$key];
				}
			}

		}

		return $default;
	}

    // Clientデータをセットする
    private function _set_client(&$client, $data)
    {
        foreach ($client as $key => $val) {
            if (isset($data[$key])) {
                $client[$key] = $data[$key];
            }
        }

        if (empty($client['sei']) && empty($client['mei']) && !empty($client['name'])) {
            $name = explode(' ', $client['name']);
            $client['sei'] = empty($name[0]) ? '' : $name[0];
            $client['mei'] = empty($name[1]) ? '' : $name[1];
        }

        if (empty($client['sei_kana']) && empty($client['mei_kana']) && !empty($client['furigana'])) {
            $kana = explode(' ', $client['furigana']);
            $client['sei_kana'] = empty($kana[0]) ? '' : $kana[0];
            $client['mei_kana'] = empty($kana[1]) ? '' : $kana[1];
        }
    }

	/**
	 * オプションデータをオプションオブジェクトにセットする
	 *
	 * @optiona		bookingデータのoptions(オブジェクトの配列)
	 * @arrayo		読込んだデータ配列
	 */
	private function _set_options($optiona, $arrayo) {
		$aoptions = array();

		// 1.0初期タイプ
		if (!is_array($arrayo)) {
			return;
		}

		// 1.1より前のデータがあれば1.1構造に変更する
		foreach ($arrayo as $keyname => $val) {
			if (is_array($val)) {
				$aoptions[$val['name']] = $val['number'];
			} else {
				$aoptions[$keyname] = $val;
			}
		}

		// オブジェクトにセットする
		foreach ($optiona as $option) {
			$keyname = $option->getKeyname();
			if (isset($aoptions[$keyname])) {
				$option->setValue($aoptions[$keyname]);
			}
		}
	}

	/**
	 * Database table installation
	 *
	 */
	private function _install_table() {
		global $wpdb;

		$option_name = $this->domain . '_table_version';
		$version = get_option($option_name);

		if (empty($version) || $version != self::TABLE_VERSION) {
			require_once(ABSPATH . "wp-admin/includes/upgrade.php");

            $iCollate = $tCollate = '';
            if (0 < strlen($wpdb->collate)) {
                $iCollate = sprintf(' collate %s', $wpdb->collate);
                $tCollate = sprintf(' collate=%s', $wpdb->collate);
            }

			// Booking table
			$sql = "CREATE TABLE " . $this->tblBooking . " (
				booking_id int(11) unsigned NOT NULL AUTO_INCREMENT,
				booking_time int(11) unsigned DEFAULT '0',
				confirmed tinyint(1) unsigned DEFAULT '0',
				parent_id int(11) DEFAULT '0',
				article_id int(11) DEFAULT '0',
				user_id int(11) DEFAULT '0',
				number int(10) DEFAULT '0',
				options text{$iCollate},
				client text{$iCollate},
				note text{$iCollate},
				created datetime DEFAULT NULL,
				PRIMARY KEY  (booking_id),
				KEY booking_time (booking_time)) DEFAULT CHARSET={$wpdb->charset}{$tCollate};";
			dbDelta($sql);

			$this->_update_data($version);

			// Update table version
			update_option($option_name, self::TABLE_VERSION);
		}
	}

	/**
	 * その他データのアップデート
	 *
	 */
	private function _update_data($tbl_version) {
		global $wpdb;

		// スケジュールデータをoptionsからpostsへ移動する
		if ($tbl_version == '1.0' && '1.2' < self::VERSION) {
			// 予約品目を読込む
			$articles = MTSSB_Article::get_all_articles();

			// 対象年月のスケジュールデータの読み込み
			$sql = "SELECT *
					FROM {$wpdb->options}
					WHERE option_name REGEXP '{$this->domain}_[[:digit:]]{6}'
					ORDER BY option_name";
			$schedules = $wpdb->get_results($sql, ARRAY_A);

			// スケジュールデータのunserialize
			foreach ($schedules as &$schedule) {
				$schedule['option_value'] = unserialize($schedule['option_value']);
			}

			// 品目毎にスケジュールデータを移動する
			foreach ($articles as $article_id => $article) {
				foreach ($schedules as &$schedule) {
					$aid = 'A' . $article_id;
					// 当該品目のスケジュールデータがあればコピーする
					if (isset($schedule['option_value'][$aid])) {
						$data = $schedule['option_value'][$aid];
						$key_name = MTS_Simple_Booking::SCHEDULE_NAME . substr($schedule['option_name'], 19);
						update_post_meta($article_id, $key_name, $data);
					}
				}
			}

			// optionsのスケジュールデータを削除する
			//foreach ($schedules as &$schedule) {
			//	delete_option($schedule['option_name']);
			//}
		}
	}

	/**
	 * 予約オブジェクトデータの参照を戻す
	 *
	 */
	public function getBooking() {
		return $this->booking;
	}

	/**
	 * 予約オブジェクトデータをセットする
	 * PayPal処理でキャンセルから戻ってきたときにセット
	 *
	 */
	public function setBooking($booking) {
		$this->booking = $booking;
	}

	/**
	 * PayPal決済後のTransaction_idの設定
	 *
	 */
	public function setTransactionID($tid) {
		$this->booking['client']['transaction_id'] = $tid;
	}

	/**
	 * 入力年齢制限
	 */
	protected function _check_age_limit($oCalendar, $birthday) {
		$ages = apply_filters('mtssb_booking_age_limit', array(
			'lower' => 1,
			'upper' => 90,
		));

        $old = sprintf('%04d%02d%02d', $oCalendar->thisYear - $ages['upper'], $oCalendar->thisMonth, $oCalendar->thisDay);
        $young = sprintf('%04d%02d%02d', $oCalendar->thisYear - $ages['lower'], $oCalendar->thisMonth, $oCalendar->thisDay);

        $ymd = explode('-', $birthday);
        if (intval($ymd[0]) === 0 || intval($ymd[1]) === 0 || intval($ymd[2]) === 0) {
            return false;
        }

        $bd = sprintf('%04d%02d%02d', $ymd[0], $ymd[1], $ymd[2]);

        if ($old < $bd && $bd <= $young) {
            return true;
        }

        return false;
	}

	/**
	 * 予約データの料金を計算し料金データを戻す
	 *
	 */
	public function make_bill() {
		$article_id = $this->booking['article_id'];
		$article = MTSSB_Article::get_the_article($article_id);

		if (empty($article)) {
			return null;
		}

		// 施設情報の読み込み
		$shop = get_option($this->domain . '_premise');
		$charge = get_option($this->domain . '_charge');

		$client = $this->booking['client'];

		// 勘定書データの作成
		$bill = new MTS_Bill;
		$bill->article_name = $article['name'];
		$bill->customer_name = $client['name'];
		$bill->shop_name = $shop['name'];

		// 人数の設定
		$bill->number->adult =  $client['adult'];
		$bill->number->child = $client['child'];
		$bill->number->baby = $client['baby'];

		// 通貨単位
		$bill->currency_code = $charge['currency_code'];

		// 基本料金
		$bill->basic_charge = $article['price']->booking;

		// 単価
		$bill->amount->adult = $article['price']->adult;
		$bill->amount->child = $article['price']->child;
		$bill->amount->baby = $article['price']->baby;

		// 消費税方式・税率
		$bill->tax_type = isset($charge['tax_notation']) ? $charge['tax_notation'] : 0;
		$bill->tax = isset($charge['consumption_tax']) ? $charge['consumption_tax'] : 0;

		// オプション料金 (予約品目にオプション使用が設定されている場合)
		$option_items = array();
		if ($article['addition']) {
			foreach ($this->booking['options'] as $oOption) {
				$val = $oOption->getValue();						// オプション入力値
				$number = $bill->get_number($oOption->whose);		// オプション料金対象
				if (!empty($val) && $number != 0) {
					$label = $oOption->getLabel();					// 日本語オプションラベル名
					$price = $oOption->getPrice();					// オプション単価
					$option_item = array();
					switch ($oOption->getType()) {
						case 'number':
							// 全員が指定された場合は入力数を注文数とする
                            if ($oOption->whose == 'all') {
                                $number = $val;
                            // それ以外は人数と入力数を比較し小さい方を注文数とする
                            } else {
                                $number = $val < $number ? $val : $number;
                            }
							if ($price != 0) {
								$option_items[] = array(
									'name' => $label,		// 品名
									'number' => $number,	// 数量
									'price' => $price,		// 単価
								);
							}
							break;
						case 'text':
							if ($price != 0) {
								$option_items[] = array(
									'name' => $label,
									'number' => $number,
									'price' => $price,
								);
							}
							break;
						case 'radio':
						case 'select':
							$fields = $oOption->getField();
							// 料金設定されている場合は明細に追加する
							if (!empty($fields[$val]['price'])) {
								$option_items[] = array(
									'name' => ($label . ' ' . $fields[$val]['label']),
									'number' => $number,
									'price' => $fields[$val]['price'],
								);
							}
							break;
						case 'check':
							$checks = explode(',', $val);
							$fields = $oOption->getField();
							// 選択肢が料金設定されている場合は明細に追加する
							foreach ($checks as $check) {
								if (array_key_exists($check, $fields) && !empty($fields[$check]['price'])) {
									$option_items[] = array(
										'name' => ($label . ' ' . $fields[$check]['label']),
										'number' => $number,
										'price' => $fields[$check]['price'],
									);
								}
							}
							break;
						//case 'date':	日付オプションは料金計算対象外とする
						default:
							break;
					}

				}
			}
		}
		$bill->option_items = $option_items;

        return $this->bill = $bill;
	}

}
