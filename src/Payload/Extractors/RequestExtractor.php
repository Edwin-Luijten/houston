<?php

namespace EdwinLuijten\Houston\Payload\Extractors;

use EdwinLuijten\Houston\Helper;
use EdwinLuijten\Houston\Payload\Partials\Request as Request;

class RequestExtractor extends AbstractExtractor
{
    /**
     * @var array
     */
    protected $requestParams;

    /**
     * @var array
     */
    protected $requestBody;

    /**
     * @var array
     */
    protected $requestExtras;

    /**
     * Request constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct($config);

        $this->setRequestParams($config);
        $this->setRequestBody($config);
        $this->setRequestExtras($config);
    }

    /**
     * @return Request
     */
    public function extract()
    {
        $request = new Request();
        $request->setUrl($this->getUrl());
        $request->setMethod($this->get($_SERVER, 'REQUEST_METHOD'));
        $request->setHeaders($this->getScrubbedHeaders($this->scrubFields));
        $request->setParams($this->requestParams);
        $request->setGet($this->scrub($_GET));
        $request->setQueryString($this->scrubUrl($this->get($_SERVER, 'QUERY_STRING')));
        $request->setPost($this->scrub($_POST));
        $request->setBody($this->requestBody);
        $request->setUserIp($this->getUserIp());

        $extras = $this->requestExtras;

        if (!$extras) {
            $extras = [];
        }

        foreach ($extras as $key => $value) {
            if (in_array($this->scrubFields, $key)) {
                $request->{$key} = str_repeat('*', 8);
            } else {
                $request->{$key} = $value;
            }
        }

        if (is_array($_SESSION) && count($_SESSION) > 0) {
            $request->session = $this->scrub($_SESSION);
        }

        return $request;
    }

    private function getUserIp()
    {
        $forwardFor = $this->get($_SERVER, 'HTTP_X_FORWARDED_FOR');
        if ($forwardFor) {
            // return everything until the first comma
            $parts = explode(',', $forwardFor);

            return $parts[0];
        }
        $realIp = $this->get($_SERVER, 'HTTP_X_REAL_IP');
        if ($realIp) {
            return $realIp;
        }

        return $this->get($_SERVER, 'REMOTE_ADDR');
    }

    private function getUrl()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $proto = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']);
        } elseif (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $proto = 'https';
        } else {
            $proto = 'http';
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        } elseif (!empty($_SERVER['HTTP_HOST'])) {
            $parts = explode(':', $_SERVER['HTTP_HOST']);
            $host  = $parts[0];
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'];
        } else {
            $host = 'unknown';
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_PORT'])) {
            $port = $_SERVER['HTTP_X_FORWARDED_PORT'];
        } elseif (!empty($_SERVER['SERVER_PORT'])) {
            $port = $_SERVER['SERVER_PORT'];
        } elseif ($proto === 'https') {
            $port = 443;
        } else {
            $port = 80;
        }

        $path = Helper::coalesce($this->get($_SERVER, 'REQUEST_URI'), '/');

        $url = $proto . '://' . $host;
        if (($proto == 'https' && $port != 443) || ($proto == 'http' && $port != 80)) {
            $url .= ':' . $port;
        }
        $url .= $path;
        if ($host == 'unknown') {
            $url = null;
        }

        return $this->scrubUrl($url);
    }

    /**
     * @param $url
     * @return mixed
     */
    public function scrubUrl($url)
    {
        $query = parse_url($url, PHP_URL_QUERY);

        if (!$query) {
            return $url;
        }

        parse_str($query, $output);

        return str_replace($query, http_build_query($this->scrub($output, 'x')), $url);
    }

    /**
     * @param $scrubFields
     * @return null
     */
    public function getScrubbedHeaders($scrubFields)
    {
        return $this->scrub($this->getHeaders(), $scrubFields);
    }

    /**
     * @return array|null
     */
    public function getHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $key => $val) {
            if (substr($key, 0, 5) == 'HTTP_') {
                // convert HTTP_CONTENT_TYPE to Content-Type, HTTP_HOST to Host, etc.
                $name = strtolower(substr($key, 5));
                if (strpos($name, '_') != -1) {
                    $name = preg_replace('/ /', '-', ucwords(preg_replace('/_/', ' ', $name)));
                } else {
                    $name = ucfirst($name);
                }
                $headers[$name] = $val;
            }
        }
        if (count($headers) > 0) {
            return $headers;
        } else {
            return null;
        }
    }

    /**
     * @param mixed $requestParams
     */
    private function setRequestParams($requestParams)
    {
        $this->requestParams = $this->get($requestParams, 'requestParams');
    }

    /**
     * @param mixed $requestBody
     */
    private function setRequestBody($requestBody)
    {
        $this->requestBody = $this->get($requestBody, 'requestBody');
    }

    /**
     * @param mixed $requestExtras
     */
    private function setRequestExtras($requestExtras)
    {
        $this->requestExtras = $this->get($requestExtras, 'requestExtras');
    }
}