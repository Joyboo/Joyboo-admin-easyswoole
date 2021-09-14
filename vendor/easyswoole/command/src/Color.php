<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace EasySwoole\Command;

/**
 * Class Color
 * @package EasySwoole\Command
 * @method static string black(string $text)
 * @method static string red(string $text)
 * @method static string green(string $text)
 * @method static string brown(string $text)
 * @method static string blue(string $text)
 * @method static string cyan(string $text)
 * @method static string normal(string $text)
 * @method static string yellow(string $text)
 * @method static string magenta(string $text)
 * @method static string white(string $text)
 * @method static string success(string $text)
 * @method static string info(string $text)
 * @method static string comment(string $text)
 * @method static string note(string $text)
 * @method static string notice(string $text)
 * @method static string warning(string $text)
 * @method static string error(string $text)
 * @method static string danger(string $text)
 */
class Color
{
    public const STYLES = [
        'black' => '0;30',
        'red' => '0;31',
        'green' => '0;32',
        'brown' => '0;33',
        'blue' => '0;34',
        'cyan' => '0;36',
        'normal' => '39',// no color
        'yellow' => '1;33',
        'magenta' => '1;35',
        'white' => '1;37',

        'success'     => '1;32',
        'info'        => '0;32',
        'comment'     => '0;33',
        'note'        => '36;1',
        'notice'      => '36;4',
        'warning'     => '0;30;43',
        'danger'      => '0;31',
        'error'       => '97;41',
    ];

    public const COLOR_TPL = "\033[%sm%s\e[0m";

    public const MATCH_TAG = '/<([a-zA-Z=;_]+)>(.*?)<\/\\1>/s';

    /**
     * @param string $method
     * @param array $arguments
     * @return string
     */
    public static function __callStatic(string $method, array $arguments): string
    {
        if (isset(self::STYLES[$method])) {
            return self::render($arguments[0], $method);
        }

        return '';
    }

    /**
     * @param string $text
     * @param null $style
     * @return string
     */
    public static function render(string $text, $style = null): string
    {

        if ($style == null || empty($style)) {
            return self::parseTag($text);
        } else {
            $color = self::STYLES[$style] ?? 0;
            return sprintf(self::COLOR_TPL, $color, $text);
        }
    }

    /**
     * @param string $text
     * @return string
     */
    public static function parseTag(string $text): string
    {
        if (!$text || false === strpos($text, '</')) {
            return $text;
        }

        if (!preg_match_all(self::MATCH_TAG, $text, $matches)) {
            return $text;
        }

        $messages = current($matches);
        $tags = next($matches);
        $values = next($matches);

        foreach ($messages as $k => $message) {
            $style = self::STYLES[$tags[$k]] ?? null;
            if (is_null($style)) continue;

            $tag = $tags[$k];
            $value = $values[$k];
            $repl = sprintf(self::COLOR_TPL, $style, $value);
            $text = str_replace("<$tag>$value</$tag>", $repl, $text);
        }

        return $text;
    }
}