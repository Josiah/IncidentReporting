<?php

namespace JJs\IncidentReporting\Exception;

use Exception;
use JJs\IncidentReporting\ErrorInterface;
use JJs\IncidentReporting\IncidentInterface;
use JJs\IncidentReporting\BacktraceInterface;

/**
 * Exception Incident
 *
 * Represents an exception as an incident so that it can be reported.
 *
 * @api
 * @author Josiah <josiah@jjs.id.au>
 */
class Incident implements IncidentInterface, ErrorInterface, BacktraceInterface
{
    /**
     * Exception
     * 
     * @var Exception
     */
    protected $exception;

    /**
     * Instantiates this class
     * 
     * @param Exception $exception Exception to represent
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Returns the message of this incident
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->exception->getMessage();
    }

    /**
     * Returns the type of error which occured
     *
     * This should usually be an exception class name
     * 
     * @return string
     */
    public function getType()
    {
        return get_class($this->exception);
    }

    /**
     * Returns the error backtrace
     * 
     * @return array of BacktraceInterface instances
     */
    public function getBacktrace()
    {
        // Wrap each line in the exception backtrace
        $backtrace = array_map(function ($line) { return new BacktraceLine($line); }, $this->exception->getTrace());

        // Add this wrapper to the backtrace
        array_push($backtrace, $this);

        // Return the backtrace
        return $backtrace;
    }

    /**
     * Returns the line number
     *
     * @return integer
     */
    public function getLine()
    {
        return $this->exception->getLine();
    }

    /**
     * Returns the file path
     *
     * @return string
     */
    public function getFile()
    {
        return $this->exception->getFile();
    }

    /**
     * Returns the method
     *
     * Always returns null.
     *
     * @return null
     */
    public function getMethod()
    {
        return null;
    }
}