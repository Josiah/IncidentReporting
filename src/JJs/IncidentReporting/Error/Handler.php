<?php

namespace JJs\IncidentReporting\Error;

/**
 * Error Handler
 *
 * Generates incidents when php errors are triggered.
 *
 * @api
 * @author Josiah <josiah@jjs.id.au>
 */
class Handler
{
    /**
     * Reporter
     * 
     * @var ReporterInterface
     */
    protected $reporter;

    /**
     * Suppression flag
     *
     * Indicates whether errors encountered by this handler should be suppressed
     * if reported.
     * 
     * @var bool
     */
    protected $suppress;

    /**
     * Listens for exceptions as the 
     * 
     * @param ReporterInterface $reporter
     *        Incident reporter
     * @param bool $suppress
     *        Indicates whether normal error handling should be suppressed when
     *        errors are handled.
     */
    public function __construct(ReporterInterface $reporter, $suppress = false)
    {
        $this->reporter = $reporter;
        $this->suppress = (bool) $suppress;
    }

    /**
     * Handles an error occurrence
     *
     * Generates and reports an incident when an error is encountered by this
     * handler.
     *
     * See {@link http://www.php.net/manual/en/function.set-error-handler.php}
     * for more information about the arguments passed to this method.
     *
     * @api
     * @param int $code
     *        Error code. See {@link php.net/manual/en/errorfunc.constants.php}
     * @param string $message
     *        Error message
     * @param string $file
     *        File where the error was triggered
     * @param int $line
     *        Line number where the error was triggered
     * @return bool
     */
    public function handle($code, $message, $file, $line)
    {
        // Generate a new incident for this error
        $incident = new Incident($code, $message, $file, $line);
        if (!$this->reporter->report($incident)) {
            return false;
        } else {
            return $this->suppress;
        }
    }

    /**
     * Invokes the error handler
     *
     * Implementation of this method allows an exception handler instance to be
     * set directly into the {@link set_exception_handler()} method.
     *
     * @api
     * @param int $code
     *        Error code. See {@link php.net/manual/en/errorfunc.constants.php}
     * @param string $message
     *        Error message
     * @param string $file
     *        File where the error was triggered
     * @param int $line
     *        Line number where the error was triggered
     * @uses Handler::handle()
     */
    public function __invoke($code, $message, $file, $line)
    {
        $this->handle($code, $message, $file, $line);
    }
}