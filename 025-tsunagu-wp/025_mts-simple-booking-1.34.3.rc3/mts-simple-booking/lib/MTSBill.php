<?php
/**
 * MTS Billオブジェクトモジュール
 *
 * @filename    MTSBill.php
 * @author		S.Hayashi
 * @code		2012-12-31 Ver.1.7.0
 *
 * Separated from 1.34.0 on 2020-08-18
 */
class MTS_Bill
{
    static private $value = [
	    'adult' => 0,       // 大人料金
	    'child' => 0,		// 小人料金
	    'baby' => 0,		// 幼児料金
	    'booking' => 0, 	// 予約料金
    ];

    private $article_name = '';			// 品目名
    private $customer_name = '';		// お客様名
    private $number = null;				// 大人・小人・幼児人数(MTS_Value)
    private $basic_charge = 0;			// 基本料金
    private $amount = null;				// 種別料金単価(大人・小人・幼児)(MTS_Value)
    private $tax_type = 0;				// 消費税(0:なし,1:内税,2:外税)
    private $tax = 0;					// 消費税率(%)
    private $option_items = array();	// オプション項目(オプション名・数量・単価)
    private $shop_name = '';			// ストア名
    private $currency_code = 'JPY';		// 通貨

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->number = (object) $this->value;
        $this->amount = (object) $this->value;
    }

    /**
     * 種別料金
     *
     */
    public function get_cost($type = 'adult') {
        return $this->number->$type * $this->amount->$type;
    }

    /**
     * 人数を戻す
     *
     */
    public function get_number($type = '')
    {
        switch ($type) {
            case 'all':
                return $this->number->adult + $this->number->child + $this->number->baby;
            case 'adult':
            case 'child':
            case 'baby':
                return $this->number->$type;
            case 'booking':
                return 1;
        }

        return 0;
    }

    /**
     * オプション料金
     *
     */
    public function get_option_cost($type = '', $price)
    {
        return $this->get_number($type) * $price;
    }

    /**
     * 料金合計
     *
     */
    public function get_total() {
        // 品目の合計
        $total = $this->number->adult * $this->amount->adult
            + $this->number->child * $this->amount->child
            + $this->number->baby * $this->amount->baby
            + $this->basic_charge;

        // オプションの合計
        $option_items = $this->option_items;
        if (!empty($option_items)) {
            foreach($option_items as $item) {
                $total += $item['number'] * $item['price'];
            }
        }

        return $total;
    }

    /**
     * 料金合計消費税計算
     *
     * @inclusive	true 内税 or false 外税
     */
    public function get_amount_tax($inclusive=false) {
        if ($this->tax_type <= 0) {
            return 0;
        }

        if ($inclusive) {
            if ($this->currency_code == 'JPY') {
                $tax = $this->get_total() - ceil($this->get_total() * 100 / ($this->tax + 100));
            } else {
                $tax = $this->get_total() - ceil($this->get_total() * 10000 / ($this->tax + 100)) / 100;
            }
        } else {
            if ($this->currency_code == 'JPY') {
                $tax = intval($this->tax * $this->get_total() / 100);
            } else {
                $tax = intval($this->tax * $this->get_total() * 100) / 10000;
            }
        }

        return $tax;
    }

    /**
     * 金額をフォーマットする
     *
     */
    public function money_format($amount) {
        if ($this->currency_code == 'JPY') {
            return number_format($amount);
        }

        return number_format($amount, 2);
    }

    /**
     * プロパティーを代入する
     *
     */
    public function __set($key, $value)
    {
        $ival = $this->getColumns($key);

        if ($ival === false) {
            throw new Exception("Error:Set undefined propertie Value->{$key}.");
        } else if (is_int($ival)) {
            $this->$key = intval($value);
        } else {
            $this->$key = $value;
        }
    }

    public function __get($key)
    {
        $ival = $this->getColumns($key);

        if ($ival === false) {
            return false;
        }

        return $this->$key;
    }

    public function getColumns($key='')
    {
        $columns = array(
            'article_name' => '',
            'customer_name' => '',
            'number' => null,
            'basic_charge' => 0,
            'amount' => null,
            'tax_type' => 0,			// 0:なし,1:内税,2:外税
            'tax' => 0,					// %
            'option_items' => null,
            'shop_name' => '',
            'currency_code' => '',
        );

        // パラメータがなければプロパティの初期値配列を戻す
        if (empty($key)) {
            return $columns;
        }

        // 指定されたプロパティがあれば初期値を、なければfalseを戻す
        if (array_key_exists($key, $columns)) {
            return $columns[$key];
        }

        return false;
    }
}
