<?php
/**
 * MTS Simple Booking ユーザーモデル
 *
 * @filename	mtssb-user.php
 * @author		S.Hayashi
 * @code		2020-07-31 Ver.1.34.0
 */
class MTSSB_User
{
	const USER_ROLE = MTS_Simple_Booking::USER_ROLE;

    const USER_META = 'mtssb_entry';

    const USERNAME_MIN = 6;
    const USERNAME_MAX = 32;

    const SESSION_EXPIRATION = 1 * 3600;

    public static $gender = ['male' => '男性', 'female' => '女性'];
    
    public $oWPUser = null;

    public $errflg = false;
    public $err = [];

    private $data = [
        'username' => '',
        'password' => '',
        'email' => '',
        'email2' => '',
        'sei' => '',
        'mei' => '',
        'sei_kana' => '',
        'mei_kana' => '',
        'company' => '',
        'postcode' => '',
        'address1' => '',
        'address2' => '',
        'tel' => '',
        'birthday' => '',
        'gender' => '',
        'agreement' => 0,
        'updated' => '',
    ];

    private $domain;

	public function __construct()
    {
		$this->domain = MTS_Simple_Booking::DOMAIN;

        // 顧客ロールの追加・確認
        if (!get_role(self::USER_ROLE)) {
            add_role(self::USER_ROLE, __('Customer', $this->domain), array(
                'read' => true,
            ));
        }
	}

    /**
     * ユーザー情報を取得する
     */
    public function getUser($oWPUser=null)
    {
        if ($oWPUser) {
            $this->oWPUser = $oWPUser;
        }

        if ($this->oWPUser->ID <= 0) {
            return false;
        }

        // ユーザー情報を取得する
        if ($this->oWPUser->has_prop('mtssb_entry')) {
            $this->_setEntry();
        }

        // MTSユーザー管理プラグインのデータを取得する
        elseif ($this->oWPUser->has_prop('mtscu_company')) {
            $this->_setMTSCU();
        }

        // 共通データをコピーする
        $this->username = $this->oWPUser->user_nicename;
        $this->email = $this->oWPUser->user_email;
        $this->sei = $this->oWPUser->last_name;
        $this->mei = $this->oWPUser->first_name;

        return $this->oWPUser->ID;
    }

    // entryメタデータをコピーする
    private function _setEntry()
    {
        $entry = $this->oWPUser->get('mtssb_entry');

        foreach ($entry as $item => $val) {
            $this->$item = $val;
        }
    }

    // mtscuメタデータをコピーする
    private function _setMTSCU()
    {
        $this->company = $this->oWPUser->get('mtscu_company');

        $furigana = $this->oWPUser->get('mtscu_furigana');
        $kana = mb_split('\s', $furigana);
        if (isset($kana[0])) {
            $this->sei_kana = array_shift($kana);
            $this->mei_kana = implode(' ', $kana);
        }

        $this->postcode = $this->oWPUser->get('mtscu_postcode');
        $this->address1 = $this->oWPUser->get('mtscu_address1');
        $this->address2 = $this->oWPUser->get('mtscu_address2');
        $this->tel = $this->oWPUser->get('mtscu_tel');
    }

    /**
     * 新規ユーザーを登録する
     */
    public function addUser()
    {
        // パスワード
        $this->password = wp_generate_password(12, false);

        $userdata = array(
            'user_login' => $this->username,
            'user_pass' => $this->password,
            'user_nicename' => $this->username,
            'user_email' => $this->email,
            'display_name' => sprintf('%s %s', $this->sei, $this->mei),
            'nickname' => $this->username,
            'first_name' => $this->mei,
            'last_name' => $this->sei,
            'role' => self::USER_ROLE,
        );

        // ユーザー新規追加
        $userId = wp_insert_user($userdata);
        if (is_wp_error($userId)) {
            return false;
        }

        // 新規登録ユーザーを読込む
        $this->oWPUser = get_userdata($userId);

        // ユーザー情報を保存する
        $this->saveEntry($userId);

        return true;
    }

	/**
	 * ユーザー情報を保存する
	 */
	public function saveEntry($userId)
    {
        $this->updated = current_time('mysql');

        update_user_meta($userId, self::USER_META, $this->data);
    }
    
    /**
     * セッションデータを保存する
     */
    public function saveSession()
    {
        // セッションIDを取得する
        $sessionId = MTS_Simple_Booking::getSessionId();

        if (empty($sessionId)) {
            return false;
        }
    
        // セッションデータを読み込む
        $data = get_transient(MTS_Simple_Booking::SESSION_NAME . $sessionId);
        if (isset($data['mtssb_user']) && $data['mtssb_user'] == $this->data) {
            return true;
        }

        $data['mtssb_user'] = $this->data;
    
        // セッションデータを保存する
        return set_transient(MTS_Simple_Booking::SESSION_NAME . $sessionId, $data, self::SESSION_EXPIRATION);
    }
    
    /**
     * セッションデータを読み込む
     */
    public function readSession()
    {
        // セッションIDを取得する
        $sessionId = MTS_Simple_Booking::getSessionId();
    
        // セッションデータを読み込む
        $data = get_transient(MTS_Simple_Booking::SESSION_NAME . $sessionId);

        if (!isset($data['mtssb_user'])) {
            return false;
        }

        // セッションデータをプロパティにセットする
        foreach ($this->data as $property => $val) {
            if (isset($data['mtssb_user'][$property])) {
                $this->data[$property] = $data['mtssb_user'][$property];
            }
        }

        return true;
    }
    
    /**
     * セッションデータを削除する
     */
    public function deleteSession()
    {
        // セッションIDを取得する
        $sessionId = MTS_Simple_Booking::getSessionId();
    
        // セッションデータを読み込む
        $data = get_transient(MTS_Simple_Booking::SESSION_NAME . $sessionId);
        if (isset($data['mtssb_user'])) {
            unset($data['mtssb_user']);
        }
    
        if (empty($data)) {
            delete_transient(MTS_Simple_Booking::SESSION_NAME . $sessionId);
        } else {
            set_transient(MTS_Simple_Booking::SESSION_NAME . $sessionId, $data);
        }
    }

    /**
     * フォーム入力データの正規化
     */
    public function normalize($post)
    {
        // 入力を取得する
        if (isset($post['username'])) {
            $this->data['username'] = mb_substr($post['username'], 0, 255);
        }
        if (isset($post['email'])) {
            $this->data['email'] = trim(mb_convert_kana($post['email'], 'as'));
        }
        if (isset($post['email2'])) {
            $this->data['email2'] = trim(mb_convert_kana($post['email2'], 'as'));
        }
        if (isset($post['sei'])) {
            $this->data['sei'] = trim(mb_convert_kana(mb_substr($post['sei'], 0, 255), 's'));
            $this->data['mei'] = trim(mb_convert_kana(mb_substr($post['mei'], 0, 255), 's'));
        }
        if (isset($post['sei_kana'])) {
            $this->data['sei_kana'] = trim(mb_convert_kana(mb_substr($post['sei_kana'], 0, 255), 'asKCV'));
            $this->data['mei_kana'] = trim(mb_convert_kana(mb_substr($post['mei_kana'], 0, 255), 'asKCV'));
        }
        if (isset($post['tel'])) {
            $this->data['tel'] = mb_substr(trim(mb_convert_kana($post['tel'], 'as')), 0, 32);
        }
        if (isset($post['company'])) {
            $this->data['company'] = mb_substr(trim(mb_convert_kana($post['company'], 's')), 0, 255);
        }
        if (isset($post['postcode'])) {
            $this->data['postcode'] = mb_substr(trim(mb_convert_kana($post['postcode'], 'as')), 0, 8);
        }
        if (isset($post['address1'])) {
            $this->data['address1'] = mb_substr(trim(mb_convert_kana($post['address1'], 'as')), 0, 255);
            $this->data['address2'] = mb_substr(trim(mb_convert_kana($post['address2'], 'as')), 0, 255);
        }
        if (isset($post['birthday'])) {
            $this->data['birthday'] = sprintf('%04d-%02d-%02d',
                intval($post['birthday']['year']), intval($post['birthday']['month']), intval($post['birthday']['day']));
        }
        if (isset($post['gender'])) {
            $this->data['gender'] = trim(mb_convert_kana($post['gender'], 'as'));
        }
        if (isset($post['agreement'])) {
            $this->data['agreement'] = intval($post['agreement']);
        }

    }
    
    /**
     * 新規ユーザー登録のユーザー名を確認する
     */
    public function checkNewUsername()
    {
        $errCode = '';

        // 入力チェック
        if (empty($this->data['username'])) {
            $errCode = 'REQUIRED';
        }

        else {
            // 文字数のチェック
            $length = strlen($this->data['username']);
            if ($length < self::USERNAME_MIN || self::USERNAME_MAX < $length) {
                $errCode = 'INVALID_LENGTH';
            }

            // ユーザー名の文字チェック
            elseif (!preg_match('/^[\w@_-]+\z/', $this->data['username'])) {
                $errCode = 'INVALID_CHARACTER';
            }

            // 既存ユーザー名のチェック
            elseif (username_exists($this->data['username'])) {
                $errCode = 'USED_ALREADY';
            }
        }

        if ($errCode) {
            return $this->_setError('username', $errCode);
        }
        
        return true;
    }
    
    /**
     * 新規ユーザー登録のメールアドレスを確認する
     */
    public function checkEmail()
    {
        $errCode = '';

        // 入力チェック
        if (empty($this->data['email'])) {
            $errCode = 'REQUIRED';
        }

        // 既存メールアドレスか確認する
        elseif (email_exists($this->data['email'])) {
            $errCode = 'USED_ALREADY';
        }

        // E Mailの確からしさ
        elseif (!preg_match("/^[0-9a-z_\.\-]+@[0-9a-z_\-\.]+\z/i", $this->data['email'])) {
            $errCode = 'INVALID_EMAIL';
        }

        if (!empty($errCode)) {
            $this->_setError('email', $errCode);
        }

        // 再入力データと比較する
        if ($this->data['email'] != $this->data['email2']) {
            return $this->_setError('email2', 'DIFFERENT_EMAIL');
        }

        return empty($errCode) ? true : false;
    }
    
    /**
     * 入力項目の入力チェックをする
     */
    public function checkEntry($columns, $requires, $newuse=true)
    {
        $ret = true;

        foreach ($columns as $column) {
            if ($requires[$column] == 1) {
                // ユーザー登録の場合はnewuse
                if ($column !== 'newuse' || $newuse) {
                    $ret &= $this->_checkRequire($column);
                }
            }

            if (!empty($this->data[$column])) {
                switch ($column) {
                    case 'tel':
                        $ret &= $this->_checkTel();
                        break;
                    case 'postcode':
                        $ret &= $this->_checkPostcode();
                        break;
                    default:
                        break;
                }
            }
        }
        
        return $ret;
    }
    
    // 必須入力項目チェック
    private function _checkRequire($column)
    {
        if ($column === 'name') {
            if (!empty($this->data['sei']) && !empty($this->data['mei'])) {
                return true;
            }
        }
        
        elseif ($column === 'furigana') {
            if (!empty($this->data['sei_kana']) && !empty($this->data['mei_kana'])) {
                return true;
            }
        }
        
        elseif ($column === 'address') {
            if (!empty($this->data['address1'])) {
                return true;
            }
        }

        elseif ($column === 'birthday') {
            $ymd = explode('-', $this->data['birthday']);
            if ($ymd[0] != 0 && $ymd[1] != 0 && $ymd[2] != 0) {
                return true;
            }
        }
        
        elseif (!empty($this->data[$column])) {
            return true;
        }
        
        return $this->_setError($column, 'REQUIRED');
    }

    // 電話番号のバリデーション
    private function _checkTel()
    {
        if (!preg_match('/^[\d\(\)-]*\z/', $this->data['tel'])) {
            return $this->_setError('tel', 'INVALID_CHARACTER');
        }
        
        return true;
    }
    
    // 郵便番号の正規化
    private function _checkPostcode()
    {
        if (!preg_match('/^[\d-]*\z/', $this->data['postcode'])) {
            return $this->_setError('postcode', 'INVALID_CHARACTER');
        }
        
        return true;
    }

    // エラーコード設定
    private function _setError($column, $errCode)
    {
        $this->err[$column] = $errCode;
        $this->errflg = true;
        
        return false;
    }

    /**
     * メール用の代替変数を戻す
     */
    public function userTempVar()
    {
        // ユーザー登録日
        $registerd = date_i18n('Y年n月j日', strtotime($this->oWPUser->user_registerd));

        $birthday = $gender = '';

        // 生年月日
        if (isset($this->data['birthday'])) {
            $birthday = date_format(date_create($this->birthday), 'Y年n月j日');
        }

        // 性別
        if (isset($client['gender'])) {
            if ($client['gender'] === 'male') {
                $gender = '男性';
            } elseif ($client['gender'] === 'female') {
                $gender = '女性';
            }
        }

        return [
            '%USER_DATE%' => $registerd,
            '%USER_USERNAME%' => $this->username,
            '%USER_PASSWORD%' => $this->password,
            '%USER_EMAIL%' => $this->email,
            '%USER_SEI%' => $this->sei,
            '%USER_MEI%' => $this->mei,
            '%USER_SEI_KANA%' => $this->sei_kana,
            '%USER_MEI_KANA%' => $this->mei_kana,
            '%USER_COMPANY%' => $this->company,
            '%USER_POSTCODE%' => $this->postcode,
            '%USER_ADDRESS1%' => $this->address1,
            '%USER_ADDRESS2%' => $this->address2,
            '%USER_TEL%' => $this->tel,
            '%USER_BIRTHDAY%' => $birthday,
            '%USER_GENDER%' => $gender,
            '%USER_AGREEMENT%' => 0 < $this->agreement ? '同意' : '不同意',
            '%USER_UPDATED%' => $this->updated,
        ];
    }

    /**
     * データを配列で戻す
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * プロパティから読み出す
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        if ($key === 'name') {
            return sprintf('%s %s', $this->data['sei'], $this->data['mei']);
        } elseif ($key === 'furigana') {
            return sprintf('%s %s', $this->data['sei_kana'], $this->data['mei_kana']);
        }
        
        if (isset($this->$key)) {
            return $this->$key;
        }
        
        $trace = debug_backtrace();
        trigger_error(sprintf(
            "Undefined property: '%s&' in %s on line %d, E_USER_NOTICE",
            $key, $trace[0]['file'], $trace[0]['line']
        ));
        
        return null;
    }

    /**
     * プロパティをセットする
     */
    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->data)) {
            $this->data[$key] = $value;
        } else {
            $this->$key = $value;
        }
        
        return $value;
    }
    
    /**
     * プロパティの確認
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * ユーザー項目のエラーメッセージ
     */
    public function columnError($column)
    {
        static $err = [
            'INVALID_LENGTH' => '入力文字の長さが無効です',
            'INVALID_CHARACTER' => '無効な文字が含まれています',
            'INVALID_EMAIL' => 'メールアドレスの形式が正しくありません',
            'DIFFERENT_EMAIL' => '再入力のメールアドレスが一致しません',
            'USED_ALREADY' => '入力データは登録済みです',
            'REQUIRED' => '必須入力項目です',
            //'UNPROCESSED' => '未処理状態です',
        ];

        if (!isset($this->err[$column])) {
            return '';
        }

        $msg = $this->err[$column];

        if (isset($err[$msg])) {
            $msg =  $err[$msg];
        }

        return $msg;
    }

}
