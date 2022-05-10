<?php
/**
 * MTS Simple Booking 顧客管理管理モジュール
 *
 * @filename	mtssb-user-admin.php
 * @date		2012-05-25
 * @author		S.Hayashi
 *
 * Updated to 1.34.0 on 2020-08-05
 * Updated to 1.15.0 on 2014-04-18
 */
class MTSSB_User_Admin
{
	const USER_ROLE = MTS_Simple_Booking::USER_ROLE;

	private $domain;

    private $oUser;

    private $oHelper;

    private $reserve;
    private $premise;

    /**
     * Constructor
     */
	public function __construct()
    {
        global $mts_simple_booking;

		$this->domain = MTS_Simple_Booking::DOMAIN;

        require_once('lib/MtssbFormCommon.php');
        $this->oHelper = new MtssbFormCommon;
    
        // 顧客データのカラム利用設定情報を読込む
        $this->reserve = get_option($this->domain . '_reserve');
        $this->premise = get_option($this->domain . '_premise');
    
        if ($this->premise['zip_search'] == 1) {
            wp_enqueue_script('mts_assistance', $mts_simple_booking->plugin_url . 'js/mts-assistance.js');
        } elseif ($this->premise['zip_search'] == 2) {
            wp_enqueue_script('postcodejp', 'https://postcode-jp.com/js/postcodejp.js');
            wp_enqueue_script('mts_postcodejp', $mts_simple_booking->plugin_url . 'js/mts-postcodejp.js');
        }

        add_action('show_user_profile', [$this, 'additionalFieldsView']);
        add_action('edit_user_profile', [$this, 'additionalFieldsView']);
        add_action('user_new_form', [$this, 'additionalFieldsView']);

        add_action( 'personal_options_update', [$this, 'saveProfileFields']);
        add_action( 'edit_user_profile_update', [$this, 'saveProfileFields']);
	}


	/**
	 * 管理画面プロフィール 連絡先情報置換
	 */
	public function extend_user_contactmethod($user_contactmethods) {

		return apply_filters('mtssb_extend_user_contactmethods', array(
			'mtscu_company' => __('Company', $this->domain),
			'mtscu_furigana' => __('Furigana', $this->domain),
			'mtscu_postcode' => __('Postcode', $this->domain),
			'mtscu_address1' => __('Address1', $this->domain),
			'mtscu_address2' => __('Address2', $this->domain),
			'mtscu_tel' => __('Tel', $this->domain),
		));
	}

	
    /**
     * プロフィール編集のデータ保存
     */
    public function saveProfileFields($userId)
    {
        $oUser = new MTSSB_User;
    
        $post = stripslashes_deep($_POST[MTSSB_User::USER_META]);
        $columns = $this->_columnOrder();
        $requires = $this->reserve['column'];

        $oUser->normalize($post, $columns, $requires);

        $oUser->saveEntry($userId);
    }

    /**
     * プロフィール編集フォーム
     */
    public function additionalFieldsView($oWPUser)
    {
        $this->oUser = new MTSSB_User;

        // user_new_formの場合の引数は 'add-new-user' または 'add-existing-user'
        if (is_object($oWPUser)) {
            $this->oUser->getUser($oWPUser);
        }

        $columns = $this->_columnOrder();
    
        // データチェック
        $this->oUser->checkEntry($columns, $this->reserve['column']);
    
?>
        <h3>予約システム ユーザー情報</h3>

        <p><?php echo $this->_requiredStr(1) ?> は必須入力項目に指定されています。</p>
        <table class="form-table">
            <?php foreach ($columns as $column) {
                $require = $this->reserve['column'][$column];
                // 不要項目はプロフィール入力項目から除外する
                if ($require <= 0) {
                    continue;
                }
                switch ($column) {
                    case 'furigana':
                        $this->_outFurigana($require);
                        break;
                    case 'company':
                        $this->_outCompany($require);
                        break;
                    case 'tel':
                        $this->_outTel($require);
                        break;
                    case 'postcode':
                        $this->_outPostcode($require);
                        break;
                    case 'address':
                        $this->_outAddress($require);
                        break;
                    case 'birthday':
                        $this->_outBirthday($require);
                        break;
                    case 'gender':
                        $this->_outGender($require);
                        break;
                    default:
                        break;
                }
            } ?>
        </table>

<?php
    }

    // 入力項目順番
    private function _columnOrder()
    {
        return explode(',', $this->reserve['column_order']);
    }

    // フリガナ
    private function _outFurigana($require)
    {
?>
        <tr class="user-row kana">
            <th><label for="user-sei_kana">フリガナ<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
                <dl>
                    <dt><label for="user-sei_kana" class="user-name">セイ</label></dt>
                    <dd>
                        <input id="user-sei_kana" class="regular-text" type="text" name="<?php echo MTSSB_User::USER_META ?>[sei_kana]" value="<?php echo $this->oUser->sei_kana ?>"><br />
                    </dd>
                    <dt><label for="user-mei_kana" class="user-name">メイ</label></dt>
                    <dd>
                        <input id="user-mei_kana" class="regular-text" type="text" name="<?php echo MTSSB_User::USER_META ?>[mei_kana]" value="<?php echo $this->oUser->mei_kana ?>">
                    </dd>
                </dl>
                <?php echo $this->_errMessage('furigana'); ?>
            </td>
        </tr>
        
<?php
    }

    // 会社・団体名
    private function _outCompany($require)
    {
?>
        <tr class="user-row company">
            <th><label for="user-company">会社・団体名<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
                <input id="user-company" type="text" class="regular-text" name="<?php echo MTSSB_User::USER_META ?>[company]" value="<?php echo $this->oUser->company ?>">
            </td>
            <?php echo $this->_errMessage('company'); ?>
        </tr>

<?php
    }

    // 電話番号
    private function _outTel($require)
    {
?>
        <tr class="user-row tel">
            <th><label for="user-tel">連絡先TEL<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
                <input id="user-tel" type="text" class="regular-text" name="<?php echo MTSSB_User::USER_META ?>[tel]" value="<?php echo $this->oUser->tel ?>">
                <br>半角数字（例：090-0000-0000)
                <?php echo $this->_errMessage('tel'); ?>
            </td>
        </tr>

<?php
    }

    // 郵便番号
    private function _outPostcode($require)
    {
        global $mts_simple_booking;

        $searchBtn = '';
    
        if (0 < $this->premise['zip_search']) {
            $searchBtn = '<button id="mts-postcode-button" type="button" class="button-secondary" onclick="mts_assistance.findByPostcode'
            . sprintf("('%s', 'user-postcode', 'user-address1')", $this->premise['api_key'])
            . sprintf('" data-api_key="%s" data-postcode="user-postcode" data-address="user-address1">検索</button>', $this->premise['api_key'])
            . sprintf('<span id="#mts-postcode-loading" style="display:none"><img src="%s"></span>', $mts_simple_booking->plugin_url . 'image/ajax-loader.gif');
        }

?>
        <tr class="user-row postcode">
            <th><label for="user-postcode">郵便番号<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
                <input id="user-postcode" type="text" class="small-medium" name="<?php echo MTSSB_User::USER_META ?>[postcode]" value="<?php echo $this->oUser->postcode ?>">
                <?php echo $searchBtn; ?>
                <br>半角数字（例：100-0001)
                <?php echo $this->_errMessage('postcode'); ?>
            </td>
        </tr>

<?php
    }

    // 住所
    private function _outAddress($require)
    {
?>
        <tr class="user-row address">
            <th><label for="user-address1">住　所<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
                <dl>
                    <dt><label for="user-address1" class="user-address-header">住所</label></dt>
                    <dd>
                        <input id="user-address1" class="regular-text" type="text" name="<?php echo MTSSB_User::USER_META ?>[address1]" value="<?php echo $this->oUser->address1 ?>"><br />
                    </dd>
                    <dt><label for="user-address2" class="user-address-header">建物等</label></dt>
                    <dd>
                        <input id="user-address2" class="regular-text" type="text" name="<?php echo MTSSB_User::USER_META ?>[address2]" value="<?php echo $this->oUser->address2 ?>">
                    </dd>
                </dl>
                <?php echo $this->_errMessage('address'); ?>
            </td>
        </tr>

<?php
    }

    // 生年月日
    private function _outBirthday($require)
    {
?>
        <tr class="user-column birthday">
            <th><label for="user-birthday-year">生年月日<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
                <?php
                    echo $this->oHelper->selectBirthday('user-birthday', 'date-box', sprintf('%s[birthday]', MTSSB_User::USER_META), $this->oUser->birthday);
                    echo $this->_errMessage('birthday');
                ?>
            </td>
        </tr>

<?php
    }

    // 性別
    private function _outGender($require)
    {
?>
        <tr class="user-column gender">
            <th><label for="user-gender">性別<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
                <?php
                    echo $this->oHelper->radioButton('user-gender', 'gender-box', sprintf('%s[gender]', MTSSB_User::USER_META), MTSSB_User::$gender, $this->oUser->gender);
                    echo $this->_errMessage('gender');
                ?>
            </td>
        </tr>

<?php
    }
    
    // 必須項目表示
    private function _requiredStr($req=0)
    {
        return $req == 1 ? ' (<span class="required">*</span>)' : '';
    }
    
    // ユーザー項目のエラーメッセージ
    private function _errMessage($column)
    {
        $msg = $this->oUser->columnError($column);

        if (empty($msg)) {
            return '';
        }

        return sprintf('<div class="mts-error">%s</div>', $msg);
    }

}
