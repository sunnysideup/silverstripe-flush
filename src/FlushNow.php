<?php

namespace Sunnysideup\Flush;

use SilverStripe\Control\Director;
use SilverStripe\ORM\DB;

trait FlushNow
{
    /**
     * Show a message about task currently running
     *
     * @param string $message to display
     * @param string $type one of [created|changed|repaired|obsolete|deleted|error]
     * @param boolean $bullet add a bullet to message?
     *
     **/
    public static function flushNowLine()
    {
        $this->flushNow('--------------------------------------------------------');
    }

    /**
     * Show a message about task currently running
     *
     * @param string $message to display
     * @param string $type one of [created|changed|repaired|obsolete|deleted|error]
     * @param boolean $bullet add a bullet to message?
     *
     **/
    protected static function flushNow($message, $type = '', $bullet = true)
    {
        if (is_string($message)) {
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
