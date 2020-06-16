<?php

namespace Sunnysideup\Flush;

use SilverStripe\Control\Director;
use SilverStripe\ORM\DB;

trait FlushNow
{


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
     * @param boolean $bullet add a bullet to message?
     *
     **/
    protected function flushNow(string $message, ?string $type = '', ?bool $bullet = true)
    {
        self::do_flush($message, $type, $bullet);
    }

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
        }
        if ($bullet) {
            DB::alteration_message($message, $type);
        } else {
            echo $message;
        }
    }
}
