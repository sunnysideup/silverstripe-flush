<?php

namespace Sunnysideup\Flush;

use SilverStripe\Control\Director;
use SilverStripe\ORM\DB;

trait FlushNow
{
    public static function do_flush(string $message, ?string $type = '', ?bool $bullet = true)
    {
        if (! is_string($message)) {
            $message = '<pre>' . print_r($message, 1) . '</pre>';
        }
        echo '';
        // check that buffer is actually set before flushing
        if (ob_get_length()) {
            @ob_flush();
            @flush();
            @ob_end_flush();
        }
        @ob_start();
        $colour = self::flush_now_type_to_colour($type);
        if (Director::is_cli()) {
            $colour = self::getColour($colour, false);
            $outputString = "\033[" . $colour . strip_tags($message) . "\033[0m";
        } else {
            $colour = self::getColour($colour, true);
            $message = '<span style="color: ' .  $colour. '">' . $message . '</span>';
        }
        if ($bullet) {
            DB::alteration_message($message, $type);
        } else {
            echo $message."\n";
        }
    }

    public static function do_flush_heading(string $message)
    {
        self::do_flush('--------------------------------------------------------', 'heading', false);
        self::do_flush($message, 'heading', false);
        self::do_flush('--------------------------------------------------------', 'heading', false);

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

    private static function flush_now_type_to_colour($type)
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
                return 'pink';
            default:
                return 'black';
        }
    }



    protected static function getColour(string $colour, ?bool $usehtmlColour = false)
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
            case 'run':
            case 'light_purple':
                $colour = '1;35m';
                $htmlColour = 'violet';
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
            default:
                $colour = '1;30m';
                $htmlColour = '#555';
        }
        if ($usehtmlColour) {
            return $htmlColour;
        }
        return $colour;
    }


}
