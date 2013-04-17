<?php

namespace JJs\IncidentReporting;

/**
 * Error
 *
 * Provides details of a traceable code error which has occured in the system.
 *
 * @author Josiah <josiah@jjs.id.au>
 * @api
 */
interface ErrorInterface
{
    /**
     * Returns the type of error which occured
     *
     * This should usually be an exception class name
     * 
     * @return string
     */
    public function getType();

    /**
     * Returns the error backtrace
     * 
     * @return array of BacktraceInterface instances
     */
    public function getBacktrace();
}