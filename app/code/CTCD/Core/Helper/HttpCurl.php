<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 * @author   imam kusuma <imam.kusuma@ctcorpdigital.com>
 */

namespace CTCD\Core\Helper;

class HttpCurl extends \Magento\Framework\HTTP\Client\Curl
{
    /**
     * Max supported protocol by curl CURL_SSLVERSION_TLSv1_2
     * @var int
     */
    private $sslVersion;

    /**
     * Response meta
     * @var array
     */
    protected $_responseMetas = [];

    /**
     * Response status line
     * @var string
     */
    protected $_responseStatusLine;

    /**
     * All response strings
     * @var string
     */
    protected $_responses;

    /**
     * User Agent
     * @var string
     */
    protected $_userAgent = '';

    /**
     * Turn on/off SSL Verification
     * @var bool
     */
    protected $_sslVerification = true;

    /**
     * Make PUT request
     *
     * @param string $uri
     * @param array|string $params
     * @return void
     *
     * @see \Magento\Framework\HTTP\Client#post($uri, $params)
     */
    public function put($uri, $params)
    {
        $this->makeRequest("PUT", $uri, $params);
    }

    /**
     * Make request
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function makeRequest($method, $uri, $params = [])
    {
        $this->_ch = curl_init();
        $this->curlOption(CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_FTP | CURLPROTO_FTPS);
        $this->curlOption(CURLOPT_URL, $uri);
        if ($method == 'POST') {
            $this->curlOption(CURLOPT_POST, 1);
            $this->curlOption(CURLOPT_POSTFIELDS, is_array($params) ? http_build_query($params) : $params);
        } elseif ($method == "GET") {
            $this->curlOption(CURLOPT_HTTPGET, 1);
        } else {
            $this->curlOption(CURLOPT_CUSTOMREQUEST, $method);
        }

        if (count($this->_headers)) {
            $heads = [];
            foreach ($this->_headers as $k => $v) {
                $heads[] = $k . ': ' . $v;
            }
            $this->curlOption(CURLOPT_HTTPHEADER, $heads);
        }

        if (count($this->_cookies)) {
            $cookies = [];
            foreach ($this->_cookies as $k => $v) {
                $cookies[] = "{$k}={$v}";
            }
            $this->curlOption(CURLOPT_COOKIE, implode(";", $cookies));
        }

        if ($this->_timeout) {
            $this->curlOption(CURLOPT_TIMEOUT, $this->_timeout);
        }

        if ($this->_port != 80) {
            $this->curlOption(CURLOPT_PORT, $this->_port);
        }

        $this->curlOption(CURLOPT_RETURNTRANSFER, 1);
        $this->curlOption(CURLOPT_HEADERFUNCTION, [$this, 'parseHeaders']);
        if ($this->sslVersion !== null) {
            $this->curlOption(CURLOPT_SSLVERSION, $this->sslVersion);
        }

        $this->curlOption(CURLOPT_VERBOSE, 1);
        $this->curlOption(CURLOPT_HEADER, 1);

        if ($this->_userAgent) {
            $this->curlOption(CURLOPT_USERAGENT, $this->_userAgent);
        }

        /* Turn off SSL Verification*/
        if (! $this->_sslVerification) {
            $this->curlOption(CURLOPT_SSL_VERIFYHOST, 0);
            $this->curlOption(CURLOPT_SSL_VERIFYPEER, 0);
        }

        if (count($this->_curlUserOptions)) {
            foreach ($this->_curlUserOptions as $k => $v) {
                $this->curlOption($k, $v);
            }
        }

        $response = curl_exec($this->_ch);
        $err = curl_errno($this->_ch);
        if ($err) {
            $this->doError(curl_error($this->_ch));
        }

        $headerSize = curl_getinfo($this->_ch, CURLINFO_HEADER_SIZE);

        $this->_headerCount = 0;
        $this->_responseHeaders = substr($response, 0, $headerSize);
        $this->_responseBody = substr($response, $headerSize);
        $this->_responseMetas = curl_getinfo($this->_ch);
        $this->_responses = $response;
        $this->_responseStatusLine = $this->renderStatusLine($response);

        $this->_responseStatus = is_array($this->_responseMetas) && array_key_exists('http_code', $this->_responseMetas) ? (int) $this->_responseMetas['http_code'] : '';

        curl_close($this->_ch);
    }

    /**
     * Set user agent
     *
     * @param string $userAgent
     * @return void
     */
    public function setUserAgent($userAgent)
    {
        $this->_userAgent = $userAgent;
    }

    /**
     * Turn on/off ssl verification
     *
     * @param bool $state
     * @return void
     */
    public function setSSLVerification($state)
    {
        $this->_sslVerification = $state;
    }

    /**
     * Get responses
     *
     * @return string
     */
    public function getResponses()
    {
        return $this->_responses;
    }

    /**
     * Get response metas
     *
     * @return array
     */
    public function getMetas()
    {
        return $this->_responseMetas;
    }

    /**
     * Get status line string
     *
     * @return string
     */
    public function getStatusLine()
    {
        return $this->_responseStatusLine;
    }

    /**
     * Render response status line
     *
     * @param string $response
     * @return string
     */
    protected function renderStatusLine($response)
    {
        $lines = explode("\r\n", $response);
        if (!is_array($lines) || count($lines) == 1) {
            $lines = explode("\n", $response);
        }

        $firstLine = array_shift($lines);

        $regex   = '/^HTTP\/(?P<version>1\.[01]) (?P<status>\d{3})(?:[ ]+(?P<reason>.*))?$/';
        $matches = array();
        if (!preg_match($regex, $firstLine, $matches)) {
            return (string) __('A valid response status line was not found in the provided string');
        }

        $status = sprintf(
            'HTTP/%s %d %s',
            $matches['version'],
            $matches['status'],
            (isset($matches['reason'])) ? $matches['reason'] : ''
        );

        return trim($status);
    }
}
