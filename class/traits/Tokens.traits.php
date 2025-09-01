<?php
// require_once dirname(dirname(__DIR__)) . '/.env.php';

trait Tokens
{
    public static function timeGapTokenCheck($token, $seed, $gap = 300)
    {
        $check_token = self::timeGapTokenGenerate($seed, $gap);
        return ($token === $check_token) ? true : false;
    }

    public static function timeGapTokenGenerate($seed, $gap = 300)
    {
        $time   = floor(time() / $gap);
        $string = md5("{$seed}#{$time}");
        return self::generateToken($string);
    }

    private function generateToken($string)
    {
        return md5($string);
    }
}
