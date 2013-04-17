<?php

namespace JJs\IncidentReporting;

/**
 * Incident
 *
 * Generic information about an incident which has occured in an application.
 *
 * @author Josiah <josiah@jjs.id.au>
 * @api
 */
interface IncidentInterface
{
    /**
     * Returns the message of this incident
     * 
     * @return string
     */
    public function getMessage();
}