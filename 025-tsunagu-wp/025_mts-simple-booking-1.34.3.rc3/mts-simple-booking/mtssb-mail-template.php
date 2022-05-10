<?php
/**
 * MTS Simple Booking Mail Template メール文テンプレート処理モジュール
 *
 * @Filename    mtssb-mail-template.php
 * @Date        2014-01-13 Ver.1.14.0
 * @Author      S.Hayashi
 *
 * Updated to 1.34.0 on 2020-08-21
 */

class MTSSB_Mail_Template
{
    // テンプレートオプション名
    const TEMPLATE = '_templates_';

    protected $domain;

    // 操作対象データ
    private $data = array(
        'option_name' => '',
        'mail_subject' => '',
        'mail_cc' => '',
        'mail_bcc' => '',
        'mail_reply_to' => '',
        'mail_body' => '',
    );


    public function __construct()
    {
        $this->domain = MTS_Simple_Booking::DOMAIN;
    }
    
    /**
     * テンプレート名リストを戻す
     */
    public static function listTemplates()
    {
        global $wpdb;

        $oTemplate = new MTSSB_Mail_Template;
        
        $templates = ['' => '----'];

        $sql = $wpdb->prepare(
            "SELECT * FROM " . $wpdb->options . "
            WHERE option_name LIKE %s
            ORDER BY option_id DESC;", MTS_Simple_Booking::DOMAIN . self::TEMPLATE . '%');
        $rows = $wpdb->get_results($sql);
    
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $oTemplate->_transform($row->option_name, unserialize($row->option_value));
                $templates[$oTemplate->option_name] = $oTemplate->mail_subject;
            }
        }
    
        // サンプルテンプレートを追加する
        $templates = array_merge($templates, $oTemplate->sampleTemplate());
    
        return $templates;
    }

    // テンプレートデータをテンプレートオブジェクトに変換する
    private function _transform($option_name, $data=[])
    {
        $this->option_name = $option_name;
        $this->mail_subject = isset($data['mail_subject']) ? $data['mail_subject'] : '';
        $this->mail_cc = isset($data['mail_cc'] ) ? $data['mail_cc'] : '';
        $this->mail_bcc = isset($data['mail_bcc']) ? $data['mail_bcc'] : '';
        $this->mail_reply_to = isset($data['mail_reply_to']) ? $data['mail_reply_to'] : '';
        $this->mail_body = isset($data['mail_body']) ? $data['mail_body'] : '';
    }
    
    /**
     * テンプレートを読み込む
     */
    public function getTemplate($option_name)
    {
        // テンプレート番号設定の場合(Ver.1.33以前)
        if (preg_match("/^[0-9]+$/", $option_name)) {
            return $this->readTemplate(MTS_Simple_Booking::DOMAIN . self::TEMPLATE . $option_name);
        }

        // 登録されたテンプレートを読み込む
        elseif (preg_match("/^" . MTS_Simple_Booking::DOMAIN . self::TEMPLATE . "([0-9]+)$/", $option_name)) {
            return $this->readTemplate($option_name);
    
        // サンプルテンプレートを読み込む
        } else {
            $template = $this->sampleTemplate($option_name);
            $this->_transform($option_name, $template);
        }
    
        return true;
    }
    
    /**
     * メールテンプレートを読み込む
     */
    public function readTemplate($option_name='')
    {
        $data = get_option($option_name);

        if ($data) {
            $this->_transform($option_name, $data);
            return true;
        }

        $this->clearTemplate();
        return false;
    }

    /**
     * 当該オブジェクトのテンプレートデータをクリアする
     */
    public function clearTemplate()
    {
        $this->option_name = '';
        $this->mail_subject = '';
        $this->mail_cc = '';
        $this->mail_bcc = '';
        $this->mail_reply_to = '';
        $this->mail_body = '';
    }
    
    /**
     * メールテンプレートの新規登録
     */
    public function addTemplate()
    {
        global $wpdb;
    
        // 仮オプション名
        $interimName = $this->domain . self::TEMPLATE;
    
        // 保存データ
        $optionData = serialize($this->data);

        // オプションテーブルに仮データを登録する
        $wpdb->replace(
            $wpdb->options,
            [
                'option_name' => $interimName,
                'option_value' => '',
                'autoload' => '',
            ],
            ['%s', '%s', '%s']
        );

        // 正式オプション名
        $optionId = $wpdb->insert_id;
        $optionName = $interimName . $optionId;

        // オプションデータ
        $this->option_name = $optionName;
        $optionData = serialize($this->data);

        // オプション名を書き換える
        $ret = $wpdb->update(
            $wpdb->options,
            [
                'option_name' => $optionName,
                'option_value' => $optionData,
                'autoload' => 'no'
            ],
            ['option_id' => $optionId],
            ['%s', '%s', '%s'],
            ['%d']
        );

        return $ret;
    }
    
    /**
     * メールテンプレートの保存
     */
    public function saveTemplate()
    {
        // 当該テンプレートを取得する
        $option = get_option($this->option_name);
        
        if (empty($option)) {
            return false;
        }

        if ($option == $this->data) {
            return true;
        }

        // オプションデータを保存する
        return update_option($this->option_name, $this->data);
    }
    
    /**
     * メールテンプレートの削除
     */
    public function deleteTemplate()
    {
        if (delete_option($this->option_name)) {
            $this->clearTemplate();
            return true;
        }

        return false;
    }

    /**
     * プロパティをセットする
     */
    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->data)) {
            switch ($key) {
                default:
            }
            
            $this->data[$key] = $value;
            return $value;
        }
        
        return null;
    }

    /**
     * プロパティから読み出す
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        
        $trace = debug_backtrace();
        trigger_error(sprintf(
            "Undefined property: '%s&' in %s on line %d, E_USER_NOTICE",
            $key, $trace[0]['file'], $trace[0]['line']
        ));
        
        return null;
    }

    /**
     * isset(),empty()アクセス不能プロパティ
     */
    public function __isset($key)
    {
        if (array_key_exists($key, $this->data)) {
            // empty()の場合、__isset()の反転を戻す
            return !empty($this->data[$key]);
        }
        
        return false;
    }

    /**
     * サンプルテンプレート文を戻す
     */
    public function sampleTemplate($sampleId='')
    {
        static $sample = [
            // 予約完了メールのサンプル
            'booking_confirmation' => [
                'mail_subject' => '予約完了メールのサンプル',
                'mail_body' =>
"%CLIENT_NAME% 様
ご予約ID：%RESERVE_ID%

この度はご予約いただきありがとうございます。下記の「ご予約情報」の内容で承
りました。

予約の確認はホームページの「予約確認」メニューから、予約IDとメールアドレス
を入力して内容をご確認いただけます。

キャンセルは予約日３日前まで受け付けております。キャンセルの有効期限内であ
れば、予約確認の表示画面の右上にキャンセルボタンが表示されますので、そちら
をご利用ください。

キャンセルが実行されると予約データは削除され、復帰できませんのであらかじめ
ご了承ください。

ご予約情報
[ご予約] %ARTICLE_NAME%
[日　時] %BOOKING_DATE% %BOOKING_TIME%
[人　数] %BOOKING_NUMBER%人
[予約ID] %RESERVE_ID%
[連絡先] TEL %CLIENT_TEL%
　〒%CLIENT_POSTCODE%
　%CLIENT_ADDRESS1%
　%CLIENT_ADDRESS2%

%CLIENT_NAME%様のお越しをお待ちしております。

%NAME%
〒%POSTCODE%
%ADDRESS%
TEL:%TEL% FAX:%FAX%
EMail:%EMAIL%
Web:%WEB%
"
            ],

            // ユーザー通知メールのサンプル
            'user_reminder' => [
                'mail_subject' => '予約通知メールのサンプル',
                'mail_body' =>
"%CLIENT_NAME% 様
ご予約ID：%RESERVE_ID%

この度はご予約いただきありがとうございました。ご予約日時が近づいて参りまし
たのでそのお知らせです。

ご予約は下記の「ご予約情報」の内容で承っております。

スタッフ一同%CLIENT_NAME%様のご来店を心よりお待ちしております。

ご予約情報
[ご予約] %ARTICLE_NAME%
[日　時] %BOOKING_DATE% %BOOKING_TIME%
[人　数] %BOOKING_NUMBER%人
[予約ID] %RESERVE_ID%
[連絡先] TEL %CLIENT_TEL%
　〒%CLIENT_POSTCODE%
　%CLIENT_ADDRESS1%
　%CLIENT_ADDRESS2%

%NAME%
〒%POSTCODE%
%ADDRESS%
TEL:%TEL% FAX:%FAX%
EMail:%EMAIL%
Web:%WEB%
"
            ],

            // ユーザー登録メールのサンプル
            'user_registration' => [
                'mail_subject' => 'ユーザー登録メールのサンプル',
                'mail_body' =>
"%USER_COMPANY%
%USER_SEI% %USER_MEI% 様

このたびはユーザー登録いただきまことにありがとうございます。
以下の通り登録が完了しましたのでお知らせいたします。

[手続き完了日] %USER_DATE%
[ユーザー名] %USER_USERNAME%
[仮パスワード] %USER_PASSWORD%
[名　前] %USER_SEI% %USER_MEI%(%USER_SEI_KANA% %USER_MEI_KANA%) 様
[電話番号] %USER_TEL%
[E メール] %USER_EMAIL%
[会社・団体名] %USER_COMPANY%
[連絡先]
 〒%USER_POSTCODE%
 %USER_ADDRESS1%
 %USER_ADDRESS2%
[誕生日] %USER_BIRTHDAY%
[性　別] %USER_GENDER%

ユーザー登録後のご予約は、サイトへログインしてからお申込み下さい。

登録情報の変更は、ユーザープロフィールページから修正いただけます。

プロフィールページの表示は、ログイン後画面上部にアドミンバーが表示され
ますので、右上のお名前からプルダウンメニューを引き出して選択してくださ
い。メニューからはプロフィールの編集や予約履歴を参照いただけます。

今後ともご愛顧を賜りますようお願い申し上げます。


%NAME%
〒%POSTCODE%
%ADDRESS%
TEL:%TEL% FAX:%FAX%
EMail:%EMAIL%
Web:%WEB%
"
            ],
        ];

        if (!empty($sampleId)) {
            return isset($sample[$sampleId]) ? $sample[$sampleId] : [];
        }

        // サンプルIDが指定されていない場合はサンプルのタイトル一覧を戻す
        $list = [];
        foreach ($sample as $key => $sampleMail) {
            $list[$key] = $sampleMail['mail_subject'];
        }

        return $list;
    }

}
