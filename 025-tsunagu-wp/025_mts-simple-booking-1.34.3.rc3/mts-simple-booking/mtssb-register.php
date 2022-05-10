<?php
/**
 * MTS Simple Booking フロントエンドユーザー登録ページ
 *
 * @Filename	mtssb-register.php
 * @Date		2014-11-28
 * @Implemented Ver.1.20.0
 * @Author		S.Hayashi
 *
 * Updated to 1.34.0 on 2020-08-10
 * Updated to 1.33.0 on 2020-07-01
 */
if (!class_exists('MtssbUserForm')) {
    require_once('lib/MtssbUserForm.php');
}

class MTSSB_Register
{
	const PAGE_NAME = MTS_Simple_Booking::PAGE_REGISTER;
    const PAGE_THANKS = MTS_Simple_Booking::PAGE_REGISTER_THANKS;

    private $view = null;

    private $oUser = null;

    private $errflg = false;
    private $errCode = '';

    public function __construct()
    {
        $this->view = new MtssbUserForm($this);
    }

    /**
     * ユーザー登録処理
     */
    public function registerUser()
    {
        // 新規ユーザーデータを取得する
        $this->oUser = new MTSSB_User;
    
        // 入力Nonceをチェックする、エラーなら処理中止
        if (isset($_POST['nonce']) && $this->_checkNonce()) {
            // ユーザー登録データを入力する
            if (isset($_POST['cmd_entry'])) {
                // 入力を取得する
                $post = stripslashes_deep($_POST['user']);

                // 入力データを取得する
                $this->oUser->normalize($post);

                // 入力データをチェックする
                if (!$this->_checkInput()) {
                    return $this->view->setError('MISSING_DATA');
                }
    
                // セッションデータを保存する
                if (!$this->oUser->saveSession()) {
                    return $this->_setError('SESSION_ERROR');
                }
            }

            // 中止するボタン
            elseif (isset($_POST['cmd_abort'])) {
                // セッションデータを削除する
                $this->oUser->deleteSession();

                return $this->_setError('ABORTED_REGISTER');
            }

            // 戻るボタン
            elseif (isset($_POST['cmd_previous'])) {
                // セッションデータを読み込む
                if (!$this->oUser->readSession()) {
                    return $this->_setError('SESSION_EMPTY');
                }
            }

            // ユーザーを登録する
            elseif (isset($_POST['cmd_register'])) {
                // セッションデータを読み込む
                if (!$this->oUser->readSession()) {
                    return $this->_setError('SESSION_EMPTY');
                }

                // セッションデータを削除する
                $this->oUser->deleteSession();

                // ユーザー新規登録
                if (!$this->oUser->addUser()) {
                    return $this->_setError('FAILED_INSERT');
                }

                // 登録完了メール送信
                if (!$this->_sendMail()) {
                    return $this->_setError('FAILED_SENDING');
                }

                // 登録完了リダイレクト表示
                $oWp = get_page_by_path(self::PAGE_THANKS);
                if ($oWp) {
                    $url = get_permalink($oWp);
                    $redirect = add_query_arg(array(
                        'action' => 'registered',
                        'nonce'  => wp_create_nonce(self::PAGE_NAME),
                    ), $url);
                    wp_redirect($redirect);
                    exit();
                }
            }

            else {
                return $this->_setError('OUT_OF_DATE');
            }
        }

        return true;
    }


    // 入力Nonceチェック
    private function _checkNonce()
    {
        // NONCEチェック
        if (!wp_verify_nonce($_POST['nonce'], self::PAGE_NAME)) {
            return $this->_setError('OUT_OF_DATE');
        }

        // 操作時間チェック
        if ((intval($_POST['start']) + 3600 < current_time('timestamp'))) {
            return $this->_setError('OVER_TIME');
        }

        return true;
    }

    // 入力チェック
    private function _checkInput()
    {
        $ret = true;
    
        // ユーザー名をチェックする
        $ret &= $this->oUser->checkNewUsername();

        // メールアドレスをチェックする
        $ret &= $this->oUser->checkEmail();

        // 入力項目の洗い出し
        $columns = explode(',', $this->view->reserve['column_order']);
        $requires = $this->view->reserve['column'];
        if ($this->view->miscell['user_consent'] == 1) {
            $columns[] = 'agreement';
            $requires['agreement'] = 1;
        }
    
        // 入力項目エラーを確認する
        $ret &= $this->oUser->checkEntry($columns, $requires, false);

        return $ret;
    }

    // ユーザー登録完了メールの送信
    private function _sendMail()
    {
        global $mts_simple_booking;

        // 各種設定のユーザー登録設定を取得する
        $miscellaneous = get_option(MTS_Simple_Booking::DOMAIN . '_miscellaneous');

        // メールテンプレートオブジェクト
        $oMailTemplate = $mts_simple_booking->_load_module('MTSSB_Mail_Template');

        // メールオブジェクト
        $oMail = $mts_simple_booking->_load_module('MTSSB_Mail');

        // ユーザー登録テンプレートを読込む
        if (!$oMailTemplate->getTemplate($miscellaneous['user_mail'])) {
            return true;
        }

        // テンプレート変数を取得する
        $vars = array_merge($this->oUser->userTempVar(), $oMail->shopTempVar());

        // テンプレート文を置換する
        $body = $oMail->replaceVariable($oMailTemplate->mail_body, $vars);

        $headers = apply_filters('mtssb_register_mail_header', []);

        // ユーザー登録メールを送信する
        return $oMail->templateMail($this->oUser->email, $oMailTemplate->mail_subject, $body, '', $headers);
    }

    /**
     * wp_content
     */
    public function content($content)
    {
        // NONCE、操作時間、上位処理エラーの確認
        if ($this->errflg) {
            return $this->view->errorOut($this->errCode);
        }

        // ユーザ−登録終了
        if (isset($_POST['cmd_register']) && !$this->view->errflg) {
            return $this->view->messageOut('REGISTERED');
        }
        // 入力フォームエラーなし
        elseif (isset($_POST['cmd_entry']) && !$this->view->errflg) {
            return $this->view->confirmForm($this->oUser) . $content;
        }

        // ユーザー登録フォームを表示する
        $content = $this->view->inputForm($this->oUser) . $content;

        return $content;
    }

    // エラーコード設定
    private function _setError($errCode)
    {
        $this->errCode = $errCode;
        $this->errflg = true;

        return false;
    }

}