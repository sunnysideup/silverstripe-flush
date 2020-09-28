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
        if (Director::is_cli()) {
            $message = strip_tags($message);
        } else {
            $message = '<span style="color: ' . self::flush_now_type_to_colour($type) . '">' . $message . '</span>';
        }
        if ($bullet) {
            DB::alteration_message($message, $type);
        } else {
            echo $message;
        }
    }

    /**
     * output a line
     **/
    protected function flushNowLine()
    {
        self::do_flush('--------------------------------------------------------');
    }

    /**
     * output a message to command line or screen
     *
     * @param string $message to display
     * @param string $type one of [created|changed|repaired|obsolete|deleted|error]
     * @param bool $bullet add a bullet to message?
     *
     **/
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
            case 'bad':
                return 'red';
            default:
                return 'black';
        }
    }
}
