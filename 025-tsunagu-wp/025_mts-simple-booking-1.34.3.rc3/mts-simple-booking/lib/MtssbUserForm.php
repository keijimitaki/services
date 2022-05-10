<?php
/**
 * MTS Simple Booking クライアントデータフォーム処理
 *
 * @Filename    MtssbUserForm.php
 * @Date		2014-11-28
 * @Implemented Ver.1.20.0
 * @Author		S.Hayashi
 *
 * Updated to 1.34.0 on 2020-08-10
 * Updated to 1.33.0 on 2020-07-01
 */
if (!class_exists('MtssbFormCommon')) {
    require_once('MtssbFormCommon.php');
}

class MtssbUserForm
{
    const PAGE_NAME = MTSSB_Register::PAGE_NAME;

    const JS = 'js/mtssb-register.js';
    const LOADING = 'image/ajax-loaderf.gif';
    const JS_ASSISTANCE = 'js/mts-assistance.js';
    const JS_POSTCODEJP = 'js/mts-postcodejp.js';
    const POSTCODEJP = 'https://postcode-jp.com/js/postcodejp.js';

    const LINK_ARROW = '<span class="link-arrow">►</span>';

    // コントローラー
    private $ctrl = null;

    private $oHelper = null;

    private $oUser = null;

    // 各種設定
    public $reserve;    // 予約メール
    public $premise;    // 施設情報
    public $miscell;    // その他

    public $errflg = false;
    public $errCode = '';

    public function __construct(MTSSB_Register $controller)
    {
        global $mts_simple_booking;

        $this->ctrl = $controller;

        $this->oHelper = new MtssbFormCommon;

        // 顧客データのカラム利用設定情報を読込む
        $this->reserve = get_option(MTS_Simple_Booking::DOMAIN . '_reserve');
        $this->premise = get_option(MTS_Simple_Booking::DOMAIN . '_premise');
        $this->miscell = get_option(MTS_Simple_Booking::DOMAIN . '_miscellaneous');

        // 郵便番号検索の指定
        $zipSearch = isset($this->premise['zip_search']) ? $this->premise['zip_search'] : 0;

        // 同意チェックJSの挿入
        wp_enqueue_script('mtssb-register', $mts_simple_booking->plugin_url . self::JS, array('jquery'));

        if ($zipSearch == 1) {
            wp_enqueue_script('mts_assistance', $mts_simple_booking->plugin_url . self::JS_ASSISTANCE, array('jquery'));
        } elseif ($zipSearch == 2) {
            wp_enqueue_script('postcodejp', self::POSTCODEJP);
            wp_enqueue_script('mts_postcodejp', $mts_simple_booking->plugin_url . self::JS_POSTCODEJP);
        }

    }

    // フォーム送信の管理データを生成する
    private function _ctrlInfo($action)
    {
        global $mts_simple_booking;

        $premise = get_option(MTS_Simple_Booking::DOMAIN . '_premise');

        return (object) array(
            'action' => $action,
            'nonce' => wp_create_nonce(self::PAGE_NAME),
            'time' => current_time('timestamp'),
            'loadingImg' => $mts_simple_booking->plugin_url . self::LOADING,
            'zipSearch' => isset($premise['zip_search']) ? $premise['zip_search'] : 0,
            'apiKey' => isset($premise['api_key']) ? $premise['api_key'] : '',
        );
    }

    /**
     * エラーをセットする
     */
    public function setError($errCode)
    {
        $this->errCode = $errCode;
        $this->errflg = true;
        return false;
    }

    /**
     * エラー終了の出力
     */
    public function errorOut($errCode)
    {
        static $errMessage = array(
            'MISSING_DATA' => '入力エラーです。',
            'OUT_OF_DATE' => '操作が正しくありません。',
            'NOT_SELECTED' => '予約の入力を確認してください。',
            'OVER_TIME' => '操作時間がオーバーしました。',
            'FAILED_INSERT' => '新規登録でエラーが発生しました。',
            'FAILED_SENDING' => '登録メールの送信を失敗しました。確認のためご連絡ください。',
            'SESSION_ERROR' => 'セッションエラーが発生しました。COOKIEが有効か確認してください。',
            'SESSION_EMPTY' => '処理は終了しました。登録を確認してください。',
            'ABORTED_REGISTER' => '処理を中止しました。',
        );

        if (array_key_exists($errCode, $errMessage)) {
            $msg = $errMessage[$errCode];
        } else {
            $msg = $errCode;
        }
    
        return $this->_endForm($errCode, sprintf('<div class="form-message error">%s</div>', $msg));
    }

    /**
     * ページメッセージ出力
     */
    public function messageOut($code)
    {
        static $message = array(
            'REGISTERED' => 'ご登録ありがとうございました。仮パスワードを記載したメールを送信しましたのでご確認ください。',
        );

        if (array_key_exists($code, $message)) {
            $msg = $message[$code];
        } else {
            $msg = $code;
        }

        return $this->_endForm($code, sprintf('<div class="form-message">%s</div>', $msg));
    }

    // 終了ページ
    private function _endForm($code, $msg)
    {
        ob_start();
        echo $msg;
        echo apply_filters('mtssb_user_register_tail', '', $code);
        return ob_get_clean();
    }

    /**
     * ユーザー登録フォーム
     */
    public function inputForm($oUser)
    {
        $this->oUser = $oUser;

        $columns = explode(',', $this->reserve['column_order']);

        ob_start();
?>
        <div class="content-form">
            <?php if ($this->errflg) {
                echo $this->errorOut($this->errCode);
            } ?>

            <div class="form-notice"><span class="required">*</span>の項目は必須です。</div>
            <form method="post">
                <fieldset class="user-form user">
                    <legend>ログイン情報</legend>
                    <table>
                        <tr class="user-column username">
                            <th><label for="user-username">ログイン名 (<span class="required">*</span>)</label></th>
                            <td>
                                <input id="user-username" type="text" class="content-text medium" name="user[username]" value="<?php echo $this->oUser->username ?>">
                                <div class="description"><?php echo sprintf('半角英数字(アンダーバーを含む)で%d文字以上%d文字以下です。', MTSSB_User::USERNAME_MIN, MTSSB_User::USERNAME_MAX) ?></div>
                                <?php echo $this->_errMessage('username') ?>
                            </td>
                        </tr>
                        <tr class="user-column email">
                            <th><label for="user-email">E Mail (<span class="required">*</span>)</label></th>
                            <td>
                                <input id="user-email" type="text" class="content-text fat" name="user[email]" value="<?php echo $this->oUser->email ?>">
                                <?php echo $this->_errMessage('email') ?>
                            </td>
                        </tr>
                        <tr class="user-column email2">
                            <th><label for="user-email2">E Mail再入力 (<span class="required">*</span>)</label></th>
                            <td>
                                <input id="user-email2" type="text" class="content-text fat" name="user[email2]" value="<?php echo $this->oUser->email2 ?>">
                                <?php echo $this->_errMessage('email2') ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                
                <fieldset class="user-form client">
                    <legend>連絡先</legend>
                    <table>
                        <?php foreach ($columns as $column) {
                            $require = $this->reserve['column'][$column];
                            // 不要項目はプロフィール入力項目から除外する
                            if ($require <= 0) {
                                continue;
                            }
                            switch ($column) {
                                case 'name':
                                    $this->_outName($require);
                                    break;
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
                </fieldset>

                <?php $this->_outAgreement() ?>
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(self::PAGE_NAME) ?>">
                <input type="hidden" name="start" value="<?php echo current_time('timestamp') ?>">
                <div id="action-button" class="register-button">
                    <input type="submit" class="button" value="確認する" name="cmd_entry">
                </div>
            </form>
        </div>

<?php
        echo apply_filters('mtssb_user_register_tail', '', 'ENTRY');
        return ob_get_clean();
    }

    // 利用規約リンクと同意チェック
    private function _outAgreement($state='entry')
    {
        // 利用規約を利用しない場合は出力しない
        if ($this->miscell['user_consent'] != 1 && empty($this->miscell['user_url'])) {
            return;
        }

        $url = '';
        if ($this->miscell['user_url']) {
            $url = apply_filters('mtssb_user_register_link',
                sprintf('%s <a href="%s" target="_blank">%s</a>', self::LINK_ARROW, $this->miscell['user_url'], 'ユーザー利用規約'));
        }

        $checkbox = $checked = $disabled = $checklabel = '';
        if ($this->miscell['user_consent'] == 1) {
            $checked = $this->oHelper->setChecked($this->oUser->agreement, 1);
            if ($state === 'register') {
                $disabled = ' disabled="disabled"';
            }
            $checkbox = sprintf('<input id="user-agreement-check" type="checkbox" name="user[agreement]" value="1"%s%s>', $checked, $disabled);
            $checklabel = apply_filters('mtssb_user_register_check_label',
                sprintf('<label for="user-agreement-check" class="check-label">規約に同意します。</label>%s', $this->_requiredStr(1)));
        }

?>
        <fieldset class="user-form agreement">
            <legend>利用規約</legend>
            <?php
                if ($url) {
                    echo sprintf('<div id="user-register-link">%s</div>', $url);
                }
                if ($checkbox) {
                    echo sprintf('<div id="user-register-consent">%s%s</div>', $checkbox, $checklabel);
                }
            echo $this->_errMessage('agreement')
            ?>

        </fieldset>

<?php
    }

    // 氏名
    private function _outName($require)
    {
?>
        <tr class="user-column name">
            <th><label for="user-sei">氏　名<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
                <label for="user-sei" class="user-name">姓</label>
                <input id="user-sei" type="text" class="content-text small-medium" name="user[sei]" value="<?php echo $this->oUser->sei ?>">
                <label for="user-mei" class="user-name">名</label>
                <input id="user-mei" type="text" class="content-text small-medium" name="user[mei]" value="<?php echo $this->oUser->mei ?>">
                <?php echo $this->_errMessage('name') ?>
            </td>
        </tr>

<?php
    }

    // フリガナ
    private function _outFurigana($require)
    {
?>
        <tr class="user-column kana">
            <th><label for="user-sei_kana">フリガナ<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
                <label for="user-sei_kana" class="user-name">セイ</label>
                <input id="user-sei_kana" type="text" class="content-text small-medium" name="user[sei_kana]" value="<?php echo $this->oUser->sei_kana ?>">
                <label for="user-mei_kana" class="user-name">メイ</label>
                <input id="user-mei_kana" type="text" class="content-text small-medium" name="user[mei_kana]" value="<?php echo $this->oUser->mei_kana ?>">
                <?php echo $this->_errMessage('furigana') ?>
            </td>
        </tr>

<?php
    }

    // 会社・団体名
    private function _outCompany($require)
    {
?>
        <tr class="user-column company">
            <th><label for="user-company">会社・団体名<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
                <input id="user-company" type="text" class="content-text fat" name="user[company]" value="<?php echo $this->oUser->company ?>">
                <?php echo $this->_errMessage('company') ?>
            </td>
        </tr>

<?php
    }

    // 電話番号
    private function _outTel($require)
    {
?>
        <tr class="user-column tel">
            <th><label for="user-tel">連絡先TEL<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
                <input id="user-tel" type="text" class="content-text medium" name="user[tel]" value="<?php echo $this->oUser->tel ?>">
                <?php echo $this->_errMessage('tel') ?>
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
                <input id="user-postcode" type="text" class="small-medium" name="user[postcode]" value="<?php echo $this->oUser->postcode ?>">
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
                        <input id="user-address1" class="content-text fat" type="text" name="user[address1]" value="<?php echo $this->oUser->address1 ?>">
                    </dd>
                    <dt><label for="user-address2" class="user-address-header">建物</label></dt>
                    <dd>
                        <input id="user-address2" class="content-text fat" type="text" name="user[address2]" value="<?php echo $this->oUser->address2 ?>">
                    </dd>
                </dl>
                <?php echo $this->_errMessage('address') ?>
            </td>
        </tr>

<?php
    }

    // 生年月日
    private function _outBirthday($require)
    {
?>
        <tr class="user-row birthday">
            <th><label for="user-birthday-year">生年月日<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
            <?php
                echo $this->oHelper->selectBirthday('user-birthday', 'date-box', 'user[birthday]', $this->oUser->birthday);
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
        <tr class="user-row gender">
            <th><label for="user-gender">性　別<?php echo $this->_requiredStr($require) ?></label></th>
            <td>
            <?php
                echo $this->oHelper->radioButton('user-gender', 'gender-box', 'user[gender]', MTSSB_User::$gender, $this->oUser->gender);
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

        return sprintf('<div class="error-message">%s</div>', $msg);
    }
    /**
     * ユーザー登録確認フォーム
     */
    public function confirmForm($oUser)
    {
        $this->oUser = $oUser;

        $columns = explode(',', $this->reserve['column_order']);

        ob_start();
?>
        <div class="content-form">
            <?php $this->errorOut($this->errCode) ?>
            <form method="post">
                <fieldset class="user-form">
                    <legend>ログイン情報</legend>
                    <table>
                        <tr class="user-column username">
                            <th>ログイン名</th>
                            <td><?php echo esc_html($oUser->username) ?></td>
                        </tr>
                        <tr class="user-column email">
                            <th>E Mail</th>
                            <td><?php echo esc_html($oUser->email) ?></td>
                        </tr>
                    </table>
                </fieldset>
                
                <fieldset class="user-form">
                    <legend>連絡先</legend>
                    <table>
                        <?php foreach ($columns as $column) {
                            $require = $this->reserve['column'][$column];
                            // 不要項目はプロフィール確認項目から除外する
                            if ($require <= 0) {
                                continue;
                            }
                            $title = $val = '';
                            switch ($column) {
                                case 'name':
                                    $title = '氏　名';
                                    $val = sprintf('%s %s', $oUser->sei, $oUser->mei);
                                    break;
                                case 'furigana':
                                    $title = 'フリガナ';
                                    $val = sprintf('%s %s', $oUser->sei_kana, $oUser->mei_kana);
                                    break;
                                case 'company':
                                    $title = '会社・団体名';
                                    $val = $oUser->company;
                                    break;
                                case 'tel':
                                    $title = '連絡先TEL';
                                    $val = $oUser->tel;
                                    break;
                                case 'postcode':
                                    $title = '郵便番号';
                                    $val = $oUser->postcode;
                                    break;
                                case 'address':
                                    $title = '住　所';
                                    $val = $oUser->address1 . ($oUser->address2 == '' ? '' : "\n{$oUser->address2}");
                                    break;
                                case 'birthday':
                                    $title = '生年月日';
                                    if ($oUser->birthday != '') {
                                        $ymd = explode('-', $oUser->birthday);
                                        $val = sprintf('%s年%s月%s日', $ymd[0], $ymd[1], $ymd[2]);
                                    } else {
                                        $val = '';
                                    }
                                    break;
                                case 'gender':
                                    $title = '性別';
                                    if ($oUser->gender === 'male') {
                                        $val = '男性';
                                    } elseif ($oUser->gender === 'female') {
                                        $val = '女性';
                                    }
                                    break;
                                default:
                                    break;
                            }

                            if (!empty($title)) {
                                echo sprintf('<tr class="user-row %s"><th>%s</th>', $column, $title);
                                echo sprintf("<td>%s</td></tr>\n", nl2br(esc_html($val)));
                            }
                        } ?>

                    </table>
                </fieldset>
    
                <?php $this->_outAgreement('register') ?>

                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(self::PAGE_NAME) ?>">
                <input type="hidden" name="start" value="<?php echo current_time('timestamp') ?>">
                <div id="action-button" class="register-button">
                    <span class="button-box"><input type="submit" class="button" value="登録する" name="cmd_register"></span>
                    <span class="button-box"><input type="submit" class="button" value="戻る" name="cmd_previous"></span>
                    <span class="button-box"><input type="submit" class="button" value="中止する" name="cmd_abort"></span>
                </div>
            </form>
        </div>
        
<?php
        echo apply_filters('mtssb_user_register_tail', '', 'CONFIRM');
        return ob_get_clean();
    }

}
