<?php
/**
 * MTS 日時アクセスモジュール
 *
 * @filename    MTSWPDate.php
 * @author		S.Hayashi
 * @code		2012-12-04
 *
 * Separated from 1.34.0 on 2020-08-18
 * Updated to 1.15.0 on 2014-01-30
 * Updated to 1.11.0 on 2013-11-21
 */
class MTS_WPDate {

    private		$utime = '';
    private		$adate = array('year' => 0, 'month' => 0, 'day' => 0);

    public function __construct() {

    }

    /**
     * Unix Timeをセットしてオブジェクトを戻す
     *
     * $utm
     */
    public function set_time($utm) {
        $this->utime = $utm;

        return $this;
    }

    /**
     * 日付がセットされているか確認する
     *
     */
    public function isSetDate() {
        if ($this->adate['year'] == 0) {
            return false;
        }
        return true;
    }

    /**
     * 日付文字列をセットする
     *
     * @dstr	'Y-n-j'
     */
    public function set_date($dstr)
    {
        $dd = explode('-', $dstr);
        if (count($dd) < 3) {
            return false;
        }

        if (empty($dd[0]) || empty($dd[1]) || empty($dd[2])) {
            $this->utime = '';
            $this->adate = array('year' => 0, 'month' => 0, 'day' => 0);
            return false;
        }

        if (!checkdate($dd[1], $dd[2], $dd[0])) {
            return false;
        }

        $this->year = intval($dd[0]);
        $this->month = intval($dd[1]);
        $this->day = intval($dd[2]);

        return true;
    }

    /**
     * 配列日付をセットする
     *
     * @ainp	array('year', 'month', 'day')
     */
    public function normalize($ainp) {
        return $this->set_date(implode('-', $ainp));
    }

    /**
     * 日付を区切子付きで戻す
     *
     * @sep		'-' or 'j'
     */
    public function get_date($sep='-') {
        if ($this->adate['year'] == 0) {
            return '';
        }

        if ($sep == 'j') {
            return $this->year . '年' . $this->month . '月' . $this->day . '日';
        }

        return $this->year . $sep . $this->month . $sep . $this->day;
    }

    /**
     * 設定された年月日によりUnix timeを戻す
     *
     */
    public function get_time()
    {
        return strtotime(sprintf('%d-%d-%d',
            ($this->year ? $this->year : date_i18n('Y')),
            ($this->month ? $this->month : '01'),
            ($this->day ? $this->day : '01')));
    }

    /**
     * 年月日をプロパティから読み出す
     *
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->adate)) {
            return $this->adate[$key];
        }

        return false;
    }

    /**
     * 年月日をプロパティにセットする
     *
     */
    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->adate)) {
            $this->adate[$key] = is_int($value) ? $value : intval($value);
            return $this->adate[$key];
        }

        return false;
    }

    /**
     * 年月日入力フォーム出力
     *
     * @keyname		id名
     * @name		name名
     * @yearf		カレント年からの未来年
     * @yearb		カレント年からの過去年
     * @space		true or false(セレクト上段に空白有無)
     */
    public function date_form($keyname, $name, $yearf=1, $yearb=1, $space=false)
    {
        $year = $month = $day= '';

        if ($this->utime <= 0) {
            $year = $this->adate['year'];
            $month = $this->adate['month'];
            $day = $this->adate['day'];
        } elseif (is_numeric($this->utime)) {
            $year = intval(date_i18n('Y', $this->utime));
            $month = intval(date_i18n('n', $this->utime));
            $day = intval(date_i18n('j', $this->utime));
        }

        $today = explode('-', date_i18n('Y-n-j'));

        ob_start();
        ?>
        <span class="date-form">
			<select id="<?php echo $keyname ?>_year" class="booking-date" name="<?php echo $name ?>[year]">
				<option value=""></option>
				<?php for ($yy = $today[0] + $yearf; $today[0] - $yearb < $yy; $yy--) : ?><option value="<?php echo $yy ?>"<?php echo $yy === $year ? ' selected="selected"' : '' ?>><?php echo $yy ?></option><?php endfor; ?>
			</select>年
		</span>
        <span class="date-form">
			<select id="<?php echo $keyname ?>_month" class="booking-date" name="<?php echo $name ?>[month]">
				<option value=""></option>
				<?php for ($mm = 1; $mm <= 12; $mm++) : ?><option value="<?php echo $mm ?>"<?php echo $month === $mm ? ' selected="selected"' : '' ?>><?php echo $mm ?></option><?php endfor; ?>
			</select>月
		</span>
        <span class="date-form">
			<select id="<?php echo $keyname ?>_day" class="booking-date" name="<?php echo $name ?>[day]">
				<option value=""></option>
				<?php for ($dd = 1; $dd <= 31; $dd++) : ?><option value="<?php echo $dd ?>"<?php echo $day === $dd ? ' selected="selected"' : '' ?>><?php echo $dd ?></option><?php endfor; ?>
			</select>日
		</span>

        <?php
        return ob_get_clean();
    }

    /**
     * 年月日入力hiddenフォーム出力
     *
     * @name		name名
     */
    public function date_form_hidden($name)
    {
        extract($this->adate);

        if ($this->utime != 0) {
            $year = date_i18n('Y', $this->utime);
            $month = date_i18n('n', $this->utime);
            $day = date_i18n('j', $this->utime);

        } elseif ($year == 0 || $month == 0 || $day == 0) {
            $year = $month = $day = '';
        }

        ob_start();
        ?>
        <input type="hidden" name="<?php echo $name ?>[year]" value="<?php echo $year ?>" />
        <input type="hidden" name="<?php echo $name ?>[month]" value="<?php echo $month ?>" />
        <input type="hidden" name="<?php echo $name ?>[day]" value="<?php echo $day ?>" />

        <?php
        return ob_get_clean();
    }
}
