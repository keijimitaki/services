<?php
/**
 * MTS Simple Booking Mail Template メール文テンプレート管理モジュール
 *
 * @Filename    mtssb-mail-template-admin.php
 * @Date        2014-01-10 Ver.1.14.0
 * @Author      S.Hayashi
 *
 * Updated to Ver.1.34.0 on 2020-08-21
 * Updated to Ver.1.21.0 on 2015-01-09
 */
if (!class_exists('MTSSB_Mail_Template')) {
    require_once(dirname(__FILE__) . '/mtssb-mail-template.php');
}

class MTSSB_Mail_Template_Admin extends MTSSB_Mail_Template
{
    const PAGE_NAME = 'simple-booking-mail-template';

    private static $iTemplate = null;


    private $mode = 'new';
    private $action = '';
    private $message = '';
    private $errflg = false;

    //private $templates = null;

    /**
     * インスタンス化
     *
     */
    static function get_instance() {
        if (!isset(self::$iTemplate)) {
            self::$iTemplate = new MTSSB_Mail_Template_Admin();
    }

        return self::$iTemplate;
    }

    public function __construct()
    {
        global $mts_simple_booking;

        $this->domain = MTS_Simple_Booking::DOMAIN;

        // CSSロード
        $mts_simple_booking->enqueue_style();

        // Javascriptロード
        //wp_enqueue_script("mtssb_mail_template__admin_js", $mts_simple_booking->plugin_url . "js/mtssb-mail-template-admin.js", array('jquery'));
    }

    /**
     * 管理画面メニュー処理
     */
    public function mail_template_page()
    {
        $this->errflg = false;
        $this->message = '';
        $this->mode = 'new';
    
        if (isset($_POST['template_select'])) {
            $post = stripslashes_deep($_POST['template']);
            $this->getTemplate($post['option_name']);
            if (preg_match('/^' . MTS_Simple_Booking::DOMAIN . '/', $this->option_name)) {
            }
        }

        // NONCEチェックがOKならテープレートデータを保存する
        elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], self::PAGE_NAME)) {
            // 入力データをメンバ変数にセットする
            $post = stripslashes_deep($_POST['template']);
            $this->_normalize($post);
        
            // テンプレート新規追加
            if (isset($_POST['template_add'])) {
                if ($this->addTemplate()) {
                    $this->message = __('The mail template has been saved.', $this->domain);
                } else {
                    $this->_setError(__('Adding the new template has been failed.', $this->domain));
                }
            }
            
            // テンプレート編集保存
            elseif (isset($_POST['template_save'])) {
                if ($this->saveTemplate()) {
                    $this->message = __('The mail template has been saved.', $this->domain);
                } else {
                    $this->_setError(__('Saving the template has been failed.', $this->domain));
                }
            }

            // テンプレート削除
            elseif (isset($_POST['template_delete'])) {
                if ($this->deleteTemplate()) {
                    $this->message = __('The template has been deleted.', $this->domain);
                } else {
                    $this->_setError(__('Deleting the template has been failed.', $this->domain));
                }
            } else {
                $this->message = '';
            }
        }

        // 編集モード切替 オプション名が空白かテンプレート名以外なら編集モードにする
        if (preg_match("/_[0-9]+$/", $this->option_name)) {
            $this->mode = 'edit';
        }

        $this->_outEditForm();
    }
    
    // 入力データをプロパティにセットする
    private function _normalize($post)
    {
        if (isset($post['option_name'])) {
            $this->option_name = $post['option_name'];
        }
        if (isset($post['mail_subject'])) {
            $this->mail_subject = trim($post['mail_subject']);
        }
        if (isset($post['mail_body'])) {
            $this->mail_body = $post['mail_body'];
        }
        if (isset($post['mail_cc'])) {
            $this->mail_cc = trim($post['mail_cc']);
        }
        if (isset($post['mail_bcc'])) {
            $this->mail_bcc = trim($post['mail_bcc']);
        }
        if (isset($post['mail_reply_to'])) {
            $this->mail_reply_to = trim($post['mail_reply_to']);
        }
    }

    // エラーメッセージ設定
    private function _setError($msg)
    {
        $this->message = $msg;
        $this->errflg = true;
        return false;
    }




    // メールテンプレート編集フォーム表示
    private function _outEditForm()
    {
        ob_start();
?>
    <div class="wrap">
        <h2><?php _e('Mail Template', $this->domain) ?></h2>
        <?php if (!empty($this->message)) : ?>
            <div class="<?php echo ($this->errflg) ? 'error' : 'updated' ?>"><p><strong><?php echo $this->message; ?></strong></p></div>
        <?php endif; ?>

        <?php $this->_select_template_form_out() ?>

        <form id="mail-template" method="post">

            <?php $this->_template_form_out() ?>

            <?php if ($this->mode == 'edit') {
                echo sprintf('<input type="submit" class="mts-button button-primary" name="template_save" value="%s">', __('Save'));
                echo sprintf('<input type="submit" class="mts-button button-primary" name="template_delete" value="%s" onclick="%s">',
                    __('Delete'), ("return confirm('" . __('This template will be deleted.', $this->domain) . "');"));
            } ?>
            <input type="submit" class="button-primary" name="template_add" value="<?php _e('Add new', $this->domain) ?>">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(self::PAGE_NAME) ?>">
        </form>

        <?php $this->_footer_variable_out() ?>
    </div><!-- wrap -->

<?php
        ob_end_flush();
    }

    /**
     * メールテンプレートの選択フォーム出力
     */
    private function _select_template_form_out()
    {
?>
        <div id="select-mail-template">
            <form method="post">
                <table class="form-table" style="width: 100%">
                    <tr class="form-field">
                        <th><?php _e('Select templates', $this->domain) ?></th>
                        <td><?php $this->_template_select_box_out() ?>
                            <input type="submit" class="button-secondary" name="template_select" value="<?php _e('Select', $this->domain) ?>">
                        </td>
                    </tr>
                </table>
            </form>
        </div>

<?php
    }

    // メールテンプレートの選択セレクトボックス
    private function _template_select_box_out()
    {
        $lists = MTSSB_Mail_Template::listTemplates();
    
        if (empty($lists)) {
            echo __('Any template has been not saved yet.', $this->domain);
            return;
        }
?>
        <select class="template-name" name="template[option_name]">
            <?php foreach ($lists as $option_name => $mail_subject) :
                $selected = $this->option_name == $option_name ? ' selected="selected"' : '';
                echo sprintf('<option value="%s"%s>%s</option>', $option_name, $selected, $mail_subject);
            endforeach; ?>
        </select>

<?php
    }

    /**
     * メール編集テーブルフォーム
     */
    private function _template_form_out()
    {
?>
        <h3><?php _e('Edit mail template', $this->domain) ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="mail-subject"><?php _e('Subject', $this->domain) ?></label>
                    <input type="hidden" name="template[option_name]" value="<?php echo $this->option_name ?>">
                </th>
                <td>
                    <input id="mail-subject" class="large-text" type="text" name="template[mail_subject]" value="<?php echo $this->mail_subject?>"
                    <div class="mts-description"><?php _e("It's the subject of this mail template.", $this->domain) ?></div>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="mail-body"><?php _e('Mail body', $this->domain) ?></label>
                </th>
                <td>
                    <textarea id="mail-body" class="large-text" name="template[mail_body]" rows="12"><?php echo $this->mail_body ?></textarea>
                    <div class="mts-description"><?php _e("It's the mail main sentence. Using under variables are available in it.", $this->domain) ?></div>
                </td>
            </tr>
        </table>

<?php
    }

    /**
     * 利用できる変数項目の説明文
     */
    private function _footer_variable_out()
    {
?>
        <p>設定したテンプレートデータは、予約完了メール、予約通知メール、ユーザー登録メールの各メールで利用します。</p>
        <p>メール文で利用可能な代替変数の一覧は、以下のページに掲載されています。送信メールにより利用できない代替変数もありますのでご注意ください。<p>
        <p>► <a href="http://dm.mt-systems.jp/mtssb/reference/?p=15" target="_blank">メール文の代替変数一覧</a></p>
        
        <p><?php _e("The following variables are available to use in a mail.", $this->domain) ?></p>
        <ul class="ul-description">
            <li>%CLIENT_NAME%</br><?php _e("Reservation application guest's name.", $this->domain) ?></li>
            <li>%RESERVE_ID%</br><?php _e("Reservation ID generated automatically in the booking mail only.", $this->domain) ?></li>
            <li>%NAME%</br><?php _e("Shop Name", $this->domain) ?></li>
            <li>%POSTCODE%</br><?php _e("Post Code", $this->domain) ?></li>
            <li>%ADDRESS%</br><?php _e("Address", $this->domain) ?></li>
            <li>%TEL%</br><?php _e("TEL Number", $this->domain) ?></li>
            <li>%FAX%</br><?php _e("FAX Number", $this->domain) ?></li>
            <li>%EMAIL%</br><?php _e("E-Mail", $this->domain) ?></li>
            <li>%WEB%</br><?php _e("Web Site", $this->domain) ?></li>
        </ul>

<?php
    }

    /**
     * メールテンプレートの保存
     *
     */
/*
    private function _save_mail_template()
    {
        global $wpdb;

        // 当該テンプレートを取得する
        $option = $this->_read_mail_template($this->option_name);

        if (!$option) {
            return null;
        }

        // オプションデータを保存する
        return update_option($this->option_name,
            array(
                'mail_subject' => $this->mail_subject,
                'mail_body' => $this->mail_body
            ));
    }
*/
    /**
     * メールテンプレートの削除
     *
     */
/*
    private function _delete_mail_template()
    {
        return delete_option($this->option_name);
    }
*/
    /**
     * メールテンプレートを読み込む
     *
     * @option_name
     */
/*
    private function _read_mail_template($option_name='')
    {
        global $wpdb;

        // オプションデータを読み込む
        $sql = $wpdb->prepare("
            SELECT * FROM " . $wpdb->options . "
            WHERE option_name=%s", $option_name);
        $row = $wpdb->get_row($sql);

        // データが存在すればテンプレートオブジェクトを戻す
        if ($row) {
            $row->option_value = unserialize($row->option_value);
            $option = self::_data2template($row);
            return $option;
        }

        return null;
    }
*/
    /**
     * 当該オブジェクトのテンプレートデータをクリアする
     *
     */
/*
    private function _clear_mail_template()
    {
        $this->option_name = '';
        $this->mail_subject = '';
        $this->mail_body = '';
    }
*/

}
