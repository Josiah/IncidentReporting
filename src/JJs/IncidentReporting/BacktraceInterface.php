<?php

namespace JJs\IncidentReporting;

/**
 * Backtrace
 *
 * Provides contextual information about a segment within an error backtrace.
 *
 * @api
 * @author Josiah <josiah@jjs.id.au>
 */
interface BacktraceInterface
{
    /**
     * Returns the line number
     *
     * @api
     * @return integer
     */
    public function getLine();

    /**
     * Returns the file path
     *
     * @api
     * @return string
     */
    public function getFile();

    /**
     * Returns the method
     *
     * Will return false, null or an empty string where there is no method in
     * this backtrace context line.
     *
     * @api
     * @return string|null
     */
    public function getMethod();
}