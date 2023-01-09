<?php

namespace Sunnysideup\Flush;

use SilverStripe\Control\Director;
use SilverStripe\ORM\DB;

trait FlushNow
{
    public static function flush_return(string $message, ?string $type = '', ?bool $bullet = true): string
    {
        $message = self::do_flush_inner($message, $type, $bullet, Director::is_cli());
        if (Director::is_cli()) {
            return $message . "\n";
        }

        return '<p>' . $message . '</p>';
    }

    public static function do_flush(string $message, ?string $type = '', ?bool $bullet = true)
    {
        $isCli = Director::is_cli();
        $message = self::do_flush_inner($message, $type, $bullet, $isCli);
        if (! $isCli) {
            self::flushBuffer();
        }

        if ($bullet && ! $isCli) {
            DB::alteration_message($message, $type);
        } elseif ($isCli) {
            echo "\n" . $message;
        } else {
            echo '<hr/>';
            echo $message;
        }
    }

    public static function do_flush_heading(string $message)
    {
        if (Director::is_cli()) {
            self::do_flush('--------------------------------------------------------', 'heading', false);
            self::do_flush('', 'heading', false);
            self::do_flush($message, 'heading', false);
            self::do_flush('--------------------------------------------------------', 'heading', false);
        } else {
            self::do_flush('<h3>' . $message . '</h3><hr />', 'heading', false);
        }
    }

    protected static function flushBuffer()
    {
        echo ' ';
        // check that buffer is actually set before flushing
        if (ob_get_length()) {
            @ob_flush();
            @flush();
            @ob_end_flush();
        }

        @ob_start();
    }

    /**
     * output a line.
     */
    protected function flushNowNewLine()
    {
        $isCli = Director::is_cli();
        if ($isCli) {
            echo "\r\n";
        } else {
            echo '<br />';
        }

        $this->flushBuffer();
    }

    /**
     * output a line.
     */
    protected function flushNowLine()
    {
        self::do_flush('--------------------------------------------------------');
    }

    /**
     * output a message to command line or screen.
     *
     * @param string $message to display
     * @param string $type    one of [created|changed|repaired|obsolete|deleted|error]
     * @param bool   $bullet  add a bullet to message?
     */
    protected function flushNow(string $message, ?string $type = '', ?bool $bullet = true)
    {
        self::do_flush($message, $type, $bullet);
    }

    protected static function flush_now_colour_for_mode(string $colour, ?bool $isCli = true): string
    {
        $htmlColour = str_replace('_', '', $colour);
        $htmlColour = str_replace('-', '', $htmlColour);
        switch ($colour) {
            case 'black':
                $colour = '0;30m';

                break;
            case 'blue':
                $colour = '0;34m';

                break;
            case 'light_blue':
                $colour = '1;34m';

                break;
            case 'green':
                $colour = '0;32m';

                break;
            case 'light_green':
                $colour = '1;32m';

                break;
            case 'cyan':
                $colour = '0;36m';

                break;
            case 'light_cyan':
                $colour = '1;36m';

                break;
            case 'red':
            case 'error':
                $colour = '0;31m';
                $htmlColour = 'red';

                break;
            case 'light_red':
                $colour = '1;31m';
                $htmlColour = 'pink';

                break;
            case 'purple':
                $colour = '0;35m';

                break;
            case 'brown':
                $colour = '0;33m';

                break;
            case 'yellow':
            case 'warning':
                $colour = '1;33m';
                $htmlColour = 'yellow';

                break;
            case 'light_gray':
                $colour = '0;37m';
                $htmlColour = '#999';

                break;
            case 'white':
                $colour = '1;37m';

                break;
            case 'dark_gray':
            case 'notice':
                $colour = '1;30m';
                $htmlColour = '#555';

                break;
            case 'run':
            case 'light_purple':
                $colour = '1;35m';
                $htmlColour = 'violet';

                break;
            default:
                $colour = '1;37m';

                break;
        }

        if ($isCli) {
            return $colour;
        }

        return $htmlColour;
    }

    private static function do_flush_inner(string $message, ?string $type = '', ?bool $bullet = true, ?bool $isCli = false): string
    {
        if (! is_string($message)) {
            $message = '<pre>' . print_r($message, 1) . '</pre>';
        }

        $colour = self::flush_now_type_to_colour($type);
        $colour = self::flush_now_colour_for_mode($colour, Director::is_cli());
        if (Director::is_cli()) {
            $message = str_replace('<br />', "\n", $message);
            $message = str_replace('<br>', "\n", $message);
            $message = str_replace('<p>', "\n", $message);
            $message = strip_tags($message);
            // https://stackoverflow.com/questions/37203694/remove-spaces-at-the-start-of-each-line-in-a-multiline-string-variable
            $message = preg_replace('/^ +/m', '', $message);
            $message = "\033[" . $colour . ' ' . strip_tags($message) . "\033[0m";
        } else {
            $message = '<span style="color: ' . $colour . '">' . $message . '</span>';
        }

        return $message;
    }

    private static function flush_now_type_to_colour(string $type): string
    {
        switch ($type) {
            case 'created':
            case 'good':
                return 'green';
            case 'changed':
            case 'info':
                return 'orange';
            case 'obsolete':
                return 'purple';
            case 'repaired':
                return 'blue';
            case 'deleted':
            case 'error':
            case 'bad':
                return 'red';
            case 'heading':
                return Director::is_cli() ? 'pink' : 'brown';
            default:
                return $type;
        }
    }
}
