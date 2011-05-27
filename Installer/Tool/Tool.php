<?php

namespace Installer\Tool;

/**
 * Tool
 *
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 */
class Tool
{
    /**
     * Generate a random secret string
     *
     */
    public static function generateSecret()
    {
        $secret = '';
        $alphanum = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $length = strlen($alphanum);
        for($a = 0; $a < 32; $a++) {
            $secret .= substr($alphanum, rand(0, $length-1), 1);
        }

        return $secret;
    }
}