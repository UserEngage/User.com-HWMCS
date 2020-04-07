<?php

class UserComHelper {
    public static function parseDomainUrl($url)
    {
        if(preg_match('/^(?:http(s)?:\/\/)?([a-zA-Z-.]+).user.com(\/)?/is', $url, $data)) {
            return $data[2];
        }

        return $url;
    }
}
