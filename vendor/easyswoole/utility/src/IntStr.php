<?php


namespace EasySwoole\Utility;


class IntStr
{
    public const intMax = 9223372036854775668;

    private const alphabet = [
        'A', 'a', 'B', 'b', 'C', 'c', 'D', 'd', 'E', 'e',
        'F', 'f', 'G', 'g', 'H', 'h', 'I', 'i', 'J', 'j',
        'K', 'k', 'L', 'l', 'M', 'm', 'N', 'n', 'O', 'o',
        'P', 'p', 'Q', 'q', 'R', 'r', 'S', 's', 'T', 't',
        'U', 'u', 'V', 'v', 'W', 'w', 'X', 'x', 'Y', 'y',
        'Z', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
    ];

    public static function toAlpha($number): string
    {
        if ($number < 0 && $number > self::intMax) {
            throw new \InvalidArgumentException('number error');
        }
        $alpha = '';
        if ($number <= 61) {
            return self::alphabet[$number];
        } elseif ($number > 61) {
            $dividend = ($number + 1);
            while ($dividend > 0) {
                $modulo = ($dividend - 1) % 62;
                if ($modulo < 0) {
                    $modulo = 62 + $modulo;
                }
                $alpha = self::alphabet[$modulo] . $alpha;
                $dividend = bcdiv(($dividend - $modulo), 62, 0);
            }
        }
        return $alpha;
    }

    public static function toNum($string): int
    {
        $alpha_flip = array_flip(self::alphabet);
        $return_value = -1;
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $return_value +=
                ($alpha_flip[$string[$i]] + 1) * bcpow(62, ($length - $i - 1), 0);
        }
        return $return_value;
    }
}
