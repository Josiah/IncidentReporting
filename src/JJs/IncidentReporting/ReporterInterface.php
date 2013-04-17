<?php

namespace JJs\IncidentReporting;

/**
 * Error Reporter
 *
 * Reports errors from an application to a 3rd party
 *
 * @author Josiah <josiah@jjs.id.au>
 */
interface ReporterInterface
{
    /**
     * Reports an error to a 3rd party
     *
     * Returns TRUE when the report was successfully delivered; FALSE otherwise.
     *
     * @api
     * @param IncidentInterface $report Report to send
     * @return boolean
     */
    public function report(IncidentInterface $report);
}