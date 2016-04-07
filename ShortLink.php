<?php

require_once "Http.php";

class ShortLink
{
    const URL = "http://dwz.cn/create.php";

    public static function createShortLink($url) {
        $data = array(
            'url' => $url,
        );
        $ret = Http::post(self::URL, $data);
        $ret = @json_decode($ret, true);
        if (isset($ret['status']) && $ret['status'] == 0) {
            return $ret['tinyurl'];
        }
        return false;
    }
}


/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
?>
