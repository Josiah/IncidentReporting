<?php

namespace JJs\IncidentReporting;

/**
 * Request
 *
 * Provides tracing information back to the request where the incident occured.
 *
 * @author Josiah <josiah@web-dev.com.au>
 */
interface RequestInterface
{
    /**
     * Returns the url where this error occured
     *
     * @api
     * @return string
     */
    public function getUrl();

    /**
     * Returns the component of the request
     *
     * Within an MVC application this is the controller which was servicing the
     * request.
     *
     * @api
     * @return string
     */
    public function getComponent();

    /**
     * Returns the request action
     *
     * Within an MVC application this is the controller action which was
     * servicing the request.
     *
     * @api
     * @return string
     */
    public function getAction();

    /**
     * Returns the request parameters
     *
     * Nested arrays are permitted and will be handled appropriately by the
     * reporter.
     *
     * @api
     * @return array
     */
    public function getParams();

    /**
     * Returns the session parameters
     *
     * Nested arrays are permitted and will be handled appropriately by the
     * reporter.
     *
     * @api
     * @return array
     */
    public function getSession();

    /**
     * Returns the server parameters
     *
     * Nested arrays are permitted and will be handled appropriately by the
     * reporter.
     *
     * @api
     * @return array
     */
    public function getServer();
}