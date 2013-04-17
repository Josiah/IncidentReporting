<?php

namespace JJs\IncidentReporting\Error;

use Exception;
use JJs\IncidentReporting\ErrorInterface;
use JJs\IncidentReporting\IncidentInterface;
use JJs\IncidentReporting\BacktraceInterface;

/**
 * Error Incident
 *
 * Incident which is reported when a php error is triggered.
 *
 * @api
 * @author Josiah <josiah@jjs.id.au>
 */
class Incident implements IncidentInterface, ErrorInterface, BacktraceInterface
{
    /**
     * Error code
     * 
     * @var int
     */
    protected $code;

    /**
     * Error message
     * 
     * @var string
     */
    protected $message;

    /**
     * File path of the code which triggered the error
     * 
     * @var string
     */
    protected $file;

    /**
     * Line number of the code which triggered the error
     * 
     * @var string
     */
    protected $line;

    /**
     * Instantiates this class
     * 
     * @param int $code
     *        Error code. See {@link php.net/manual/en/errorfunc.constants.php}
     * @param string $message
     *        Error message
     * @param string $file
     *        File where the error was triggered
     * @param int $line
     *        Line number where the error was triggered
     */
    public function __construct($code, $message, $file, $line)
    {
        $this->code = $code;
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
    }

    /**
     * Returns the message of this incident
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns the type of error which occured
     *
     * The error type is derived from the error code and will reflect the most
     * severe error constant which matches the error code.
     * 
     * @return string
     */
    public function getType()
    {
        foreach ([
            'error'      => E_ERROR|E_CORE_ERROR|E_USER_ERROR|E_RECOVERABLE_ERROR,
            'warning'    => E_WARNING|E_CORE_WARNING|E_USER_WARNING,
            'notice'     => E_NOTICE|E_USER_NOTICE,
            'deprecated' => E_DEPRECATED|E_USER_DEPRECATED,
            'strict'     => E_STRICT,
        ] as $type => $code) if ($this->code & $code) return $type;

        return sprintf('unknown (%s)', $this->code);
    }

    /**
     * Returns the error backtrace
     * 
     * @return array of BacktraceInterface instances
     */
    public function getBacktrace()
    {
        return [$this];
    }

    /**
     * Returns the line number
     *
     * @api
     * @return integer
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Returns the file path
     *
     * @api
     * @return string
     */
    public function getFile()
    {
        return $this->file;
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
        return null;
    }
}