<?php

namespace JJs\IncidentReporting\Exception;

use Exception;
use JJs\IncidentReporting\ReporterInterface;

/**
 * Exception Handler
 *
 * Handles exceptions by generating a new exception incident when an exception
 * occurs within the application.
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
     * Listens for exceptions as the 
     * 
     * @param ReporterInterface $reporter Incident reporter
     */
    public function __construct(ReporterInterface $reporter)
    {
        $this->reporter = $reporter;
    }

    /**
     * Handles an exception
     * 
     * @param Exception $exception Exception to handle
     */
    public function handle(Exception $exception)
    {
        // Generate a new exception incident and report it
        $incident = new Incident($exception);
        $this->reporter->report($incident);

        // Restore the previous exception handler and let it handle the
        // exception
        restore_exception_handler();
        throw $exception;
    }

    /**
     * Invokes the exception handler
     *
     * Implementation of this method allows an exception handler instance to be
     * set directly into the {@link set_exception_handler()} method.
     *
     * @api
     * @param Exception $exception Exception to handle
     * @uses Handler::handle()
     */
    public function __invoke(Exception $exception)
    {
        $this->handle($exception);
    }
}