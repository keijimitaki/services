<?php
/**
 * MTS 時間アクセスモジュール
 *
 * @filename    MTSWPDate.php
 * @author		S.Hayashi
 * @code		2013-07-02
 *
 * Separated from 1.34.0 on 2020-08-18
 */
class MTS_WPTime {

    private		$utime = 0;

    public function __construct($time = 0)
    {
        $this->utime = $time;
    }

    public function __get($key) {
        return $this->$key;
    }

    /**
     * 入力データを確認して時刻設定する
     *
     * return	Unix Time or false
     */
    public static function get_utime($hour, $minute)
    {
        if (is_numeric($hour) && is_numeric($minute)) {
            $time = strtotime($hour . ':' . $minute);
            return $time;
        }

        return false;
    }

    /**
     * 時分選択セレクトの表示
     *
     */
    public function time_form($keyname, $name)
    {
        $hour_range = apply_filters('mtssb_wptime_hour_range', array('min' => 0, 'max' => 23, 'step' => 1), $keyname);
        $minute_range = apply_filters('mtssb_wptime_minute_range', array('min' => 0, 'max' => 59, 'step' => 10), $keyname);
        $hour = $minute = '';

        if (is_numeric($this->utime)) {
            $hour = intval(date_i18n('H', $this->utime));
            $minute = intval(date_i18n('i', $this->utime));
        }

        ob_start();
        ?>
        <span class="time-form">
			<select name="<?php echo $name . "[$keyname][hour]" ?>" class="booking-time <?php echo $keyname ?> hour">
				<option value=""></option>
				<?php for ($i = $hour_range['min']; $i <= $hour_range['max']; $i += $hour_range['step']) {
                    echo '<option value="' . $i . '"' . ($i === $hour ? ' selected="selected"' : '') . '>' . substr("0$i", -2) . '</option>';
                } ?>
			</select>時
		</span>
        <span class="time-form">
			<select name="<?php echo $name . "[$keyname][minute]" ?>" class="booking-time <?php echo $keyname ?> minute">
				<option value=""></option>
				<?php for ($i = $minute_range['min']; $i <= $minute_range['max']; $i += $minute_range['step']) {
                    echo '<option value="' . $i . '"' . ($i === $minute ? ' selected="selected"' : '') . '>' . substr("0$i", -2) . '</option>';
                } ?>
			</select>分
		</span>

        <?php
        return ob_get_clean();
    }

    /**
     * 時間入力hiddenフォーム出力
     *
     * @name		name名
     */
    public function time_form_hidden($name)
    {
        ob_start();
        ?>
        <input type="hidden" name="<?php echo $name ?>[hour]" value="<?php echo date_i18n('H', $this->utime) ?>" />
        <input type="hidden" name="<?php echo $name ?>[minute]" value="<?php echo date_i18n('i', $this->utime) ?>" />

        <?php
        return ob_get_clean();
    }

}
