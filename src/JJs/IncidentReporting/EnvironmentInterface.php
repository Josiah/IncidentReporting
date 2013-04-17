<?php

namespace JJs\IncidentReporting;

/**
 * Server Environment
 *
 * Provides contextual information about the server environment of the
 * application which is currently running.
 * 
 * @api
 * @author Josiah <josiah@web-dev.com.au>
 */
interface EnvironmentInterface
{
    /**
     * Returns the root directory of the application
     * 
     * @return string
     */
    public function getApplicationDirectory();

    /**
     * Returns the server environment (e.g. prod, test, dev)
     * 
     * @return string
     */
    public function getEnvironmentName();

    /**
     * Returns the application version
     * 
     * @return string
     */
    public function getApplicationVersion();
}