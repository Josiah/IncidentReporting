<?php

namespace JJs\IncidentReporting\Exception;

use JJs\IncidentReporting\BacktraceInterface;

/**
 * Exception Backtrace Line
 *
 * Wraps a php exception backtrace to provide an implementation of an error
 * incident backtrace.
 *
 * @api
 * @author Josiah <josiah@web-dev.com.au>
 */
class BacktraceLine implements BacktraceInterface
{
    /**
     * Trace data
     * 
     * @var array
     */
    protected $trace;

    /**
     * Instantiates this class
     * 
     * @param array $trace Exception trace data
     */
    public function __construct(array $trace)
    {
        $this->trace = $trace;
    }

    /**
     * Returns the line number
     *
     * @api
     * @return integer
     */
    public function getLine()
    {
        return @$this->trace['line'];
    }

    /**
     * Returns the file path
     *
     * @api
     * @return string
     */
    public function getFile()
    {
        return @$this->trace['file'];
    }

    /**
     * Returns the method
     *
     * Will return false, null or an empty string where there is no method in
     * this backtrace context line.
     *
     * @api
     * @return string|null
     */
    public function getMethod()
    {
        if (array_key_exists('class', $this->trace)) {
            return @$this->trace['class']
                 . @$this->trace['type']
                 . @$this->trace['function'];
        } else {
            return @$this->trace['function'];
        }
    }
}