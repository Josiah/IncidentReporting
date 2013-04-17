<?php

namespace JJs\IncidentReporting;

use XMLWriter;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;

/**
 * Airbrake Reporter
 *
 * Reports errors which occur within the application to an airbrake api
 * compatible endpoint.
 *
 * @see http://help.airbrake.io/kb/api-2/notifier-api-version-23
 * @author Josiah <josiah@jjs.id.au>
 */
class AirbrakeReporter implements ReporterInterface
{
    /**
     * Airbrake Version
     *
     * @var string
     */
    const AirbrakeVersion = "2.3";

    /**
     * Notifier name
     *
     * @var string
     */
    const NotifierName = "jjs-incident-reporting";

    /**
     * Notifier version
     *
     * @var string
     */
    const NotifierVersion = "0.1.0";

    /**
     * Notifier url
     *
     * @var string
     */
    const NotifierUrl = "https://github.com/Josiah/IncidentReporting";

    /**
     * Endpoint URL
     * 
     * @var string
     */
    protected $url;

    /**
     * Airbrake API Key
     * 
     * @var string
     */
    protected $apiKey;

    /**
     * Instantiates the airbrake reporter
     * 
     * @param string $url    Endpoint url
     * @param string $apiKey Airbrake API Key
     */
    public function __construct($url, $apiKey)
    {
        $this->url = $url;
        $this->apiKey = $apiKey;
    }

    /**
     * Returns the url
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns the api key
     * 
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Reports an error to a 3rd party
     *
     * Returns TRUE when the report was successfully delivered; FALSE otherwise.
     *
     * @api
     * @param IncidentInterface $incident Incident report to send
     * @return boolean
     */
    public function report(IncidentInterface $incident)
    {
        $xml = new XMLWriter();

        // Open the endpoint URI
        if (!$xml->openMemory()) return false;
        
        // Start the xml document
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('notice');
        $xml->writeAttribute('version', static::AirbrakeVersion);
        $xml->writeElement('api-key', $this->getApiKey());
        $this->writeNotifier($xml);

        // Write the error element if the incident supports it
        if ($incident instanceof ErrorInterface) {
            $this->writeError($xml, $incident);
        } else {
            // Only error incidents are supported by the airbrake reporter.
            // Support for non-error incidents may be added in the future.
            return false;
        }

        // Write the request element if the incident supports it
        if ($incident instanceof RequestInterface) {
            $this->writeRequest($xml, $incident);
        }

        // Write the server environment element if the incident supports it
        if ($incident instanceof EnvironmentInterface) {
            $this->writeServerEnvironment($xml, $incident);
        }

        // Finish the xml document
        $xml->endElement();
        $xml = $xml->flush();

        // Send the document to the endpoint
        try {
            $request = (new Client())->post($this->getUrl());
            $request->setBody($xml);
            $request->send();

            return true;
        } catch (BadResponseException $exception) {
            return false;
        }
    }

    /**
     * Writes /notice/notifier
     * 
     * @param XMLWriter $xml Writer
     */
    protected function writeNotifier(XMLWriter $xml)
    {
        $xml->startElement('notifier');
        $xml->writeElement('name',    static::NotifierName);
        $xml->writeElement('version', static::NotifierVersion);
        $xml->writeElement('url',     static::NotifierUrl);
        $xml->endElement();
    }

    /**
     * Writes /notice/error
     * 
     * @param XMLWriter      $xml   Writer
     * @param ErrorInterface $error Error
     */
    protected function writeError(XMLWriter $xml, ErrorInterface $error)
    {
        $xml->startElement('error');
        $xml->writeElement('class', $error->getType());
        $xml->writeElement('message', $error->getMessage());
        $this->writeBacktrace($xml, $error);
        $xml->endElement();
    }


    /**
     * Writes /notice/error/backtrace
     * 
     * @param XMLWriter      $xml   Writer
     * @param ErrorInterface $error Error
     */
    protected function writeBacktrace(XMLWriter $xml, ErrorInterface $error)
    {
        $xml->startElement('backtrace');
        foreach ($error->getBacktrace() as $backtrace) {
            $this->writeBacktrace($xml, $backtrace);
        }
        $xml->endElement();
    }

    /**
     * Writes /notice/error/backtrace/line
     * 
     * @param XMLWriter          $xml       Writer
     * @param BacktraceInterface $backtrace Backtrace line
     */
    protected function writeBacktraceLine(XMLWriter $xml, BacktraceInterface $backtrace)
    {
        $xml->startElement('line');
        $xml->writeAttribute('file', $backtrace->getFile());
        $xml->writeAttribute('line', $backtrace->getLine());
        if ($backtrace->getMethod()) {
            $xml->writeAttribute('method', $backtrace->getMethod());
        }
        $xml->endElement();
    }

    /**
     * Writes /notice/request
     * 
     * @param XMLWriter        $xml     Writer
     * @param RequestInterface $request Request context
     */
    protected function writeRequest(XMLWriter $xml, RequestInterface $request)
    {
        $xml->startElement('request');
        $xml->writeElement('url', $request->getUrl());
        $xml->writeElement('component', $request->getComponent());
        $xml->writeElement('action', $request->getAction());
        $this->writeRequestParams($xml, $request);
        $this->writeRequestSession($xml, $request);
        $this->writeRequestCgiData($xml, $request);
        $xml->endElement();
    }

    /**
     * Writes /notice/request/params
     * 
     * @param XMLWriter        $xml     Writer
     * @param RequestInterface $request Request context
     */
    protected function writeRequestParams(XMLWriter $xml, RequestInterface $request)
    {
        $xml->startElement('params');
        foreach ($request->getParams() as $key => $value) {
            $this->writeVar($xml, $key, $value);
        }
        $xml->endElement();
    }

    /**
     * Writes /notice/request/session
     * 
     * @param XMLWriter        $xml     Writer
     * @param RequestInterface $request Request context
     */
    protected function writeRequestSession(XMLWriter $xml, RequestInterface $request)
    {
        $xml->startElement('session');
        foreach ($request->getSession() as $key => $value) {
            $this->writeVar($xml, $key, $value);
        }
        $xml->endElement();
    }

    /**
     * Writes /notice/request/cgi-data
     * 
     * @param XMLWriter        $xml     Writer
     * @param RequestInterface $request Request context
     */
    protected function writeRequestCgiData(XMLWriter $xml, RequestInterface $request)
    {
        $xml->startElement('cgi-data');
        foreach ($request->getServer() as $key => $value) {
            $this->writeVar($xml, $key, $value);
        }
        $xml->endElement();
    }

    /**
     * Writes //var
     * 
     * @param XMLWriter $xml   Writer
     * @param string    $key   Key
     * @param string    $value Value
     */
    protected function writeVar(XMLWriter $xml, $key, $value)
    {
        $xml->startElement('var');
        $xml->writeAttribute('key', $key);
        $xml->text($value);
        $xml->endElement();
    }

    protected function writeServerEnvironment(XMLWriter $xml, EnvironmentInterface $environment)
    {
        $xml->startElement('server-environment');
        $xml->writeElement('project-root',     $environment->getApplicationDirectory());
        $xml->writeElement('environment-name', $environment->getEnvironmentName());
        $xml->writeElement('app-version',      $environment->getApplicationVersion()); 
        $xml->endElement();
    }
}