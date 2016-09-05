<?php

namespace EdwinLuijten\Houston\Payload;

use EdwinLuijten\Houston\Defaults;
use EdwinLuijten\Houston\Error;
use EdwinLuijten\Houston\Helper;
use EdwinLuijten\Houston\Payload\Partials\Level;
use EdwinLuijten\Houston\Payload\Partials\Message;
use EdwinLuijten\Houston\Payload\Partials\Request;
use EdwinLuijten\Houston\Payload\Partials\Server;

class Extractor
{
    protected $environment;
    protected $messageLevel;
    protected $exceptionLevel;
    protected $psrLevels;
    protected $scrubFields;
    protected $errorLevels;
    protected $codeVersion;
    protected $platform;
    protected $framework;
    protected $context;
    protected $requestParams;
    protected $requestBody;
    protected $requestExtras;
    protected $host;
    protected $serverRoot;
    protected $serverBranch;
    protected $serverCodeVersion;
    protected $serverExtras;
    protected $custom;
    protected $fingerprint;
    protected $title;
    protected $notifier;
    protected $baseException;
    protected $includeCodeContext = true;
    protected $shiftFunction;
    protected static $defaults;

    public function __construct($config)
    {
        self::$defaults = Defaults::get();

        $this->setEnvironment($config);
        $this->setDefaultMessageLevel($config);
        $this->setDefaultExceptionLevel($config);
        $this->setDefaultPsrLevels($config);
        $this->setScrubFields($config);
        $this->setErrorLevels($config);
        $this->setCodeVersion($config);
        $this->setPlatform($config);
        $this->setFramework($config);
        $this->setContext($config);
        $this->setRequestParams($config);
        $this->setRequestBody($config);
        $this->setRequestExtras($config);
        $this->setHost($config);
        $this->setServerRoot($config);
        $this->setServerBranch($config);
        $this->setServerCodeVersion($config);
        $this->setServerExtras($config);
        $this->setCustom($config);
        $this->setFingerprint($config);
        $this->setTitle($config);
        $this->setNotifier($config);
        $this->setBaseException($config);
        $this->setIncludeCodeContext($config);

        $this->shiftFunction = $this->get($config, 'shift_function');

        if (!isset($this->shiftFunction)) {
            $this->shiftFunction = true;
        }
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param mixed $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $this->get($environment, 'environment');
    }

    /**
     * @param $toLog
     * @param $context
     * @return Message
     */
    public function getMessage($toLog, $context)
    {
        return new Message((string)$toLog, $context);
    }

    /**
     * @param $messageLevel
     */
    public function setDefaultMessageLevel($messageLevel)
    {
        $this->messageLevel = self::$defaults->messageLevel($this->get($messageLevel, 'messageLevel'));
    }

    public function getLevel($level, $toLog)
    {
        if (is_null($level)) {
            if ($toLog instanceof Error) {
                $level = $this->get($this->errorLevels, $toLog->errorLevel);
            } elseif ($toLog instanceof \Exception) {
                $level = $this->exceptionLevel;
            } else {
                $level = $this->messageLevel;
            }
        }

        return Level::fromName($this->get($this->psrLevels, strtolower($level)));
    }

    /**
     * @param mixed $messageLevel
     * @return Builder
     */
    public function setMessageLevel($messageLevel)
    {
        $this->messageLevel = $messageLevel;
    }

    /**
     * @param $exceptionLevel
     */
    public function setDefaultExceptionLevel($exceptionLevel)
    {
        $this->exceptionLevel = self::$defaults->exceptionLevel($this->get($exceptionLevel, 'exceptionLevel'));
    }

    /**
     * @param mixed $exceptionLevel
     * @return Builder
     */
    public function setExceptionLevel($exceptionLevel)
    {
        $this->exceptionLevel = $exceptionLevel;
    }

    /**
     * @param $psrLevels
     */
    public function setDefaultPsrLevels($psrLevels)
    {
        $this->psrLevels = self::$defaults->psrLevels($this->get($psrLevels, 'psrLevels'));
    }

    /**
     * @param mixed $psrLevels
     * @return Builder
     */
    public function setPsrLevels($psrLevels)
    {
        $this->psrLevels = $psrLevels;
    }

    /**
     * @return mixed
     */
    public function getScrubFields()
    {
        return $this->scrubFields;
    }

    /**
     * @param mixed $scrubFields
     * @return Builder
     */
    public function setScrubFields($scrubFields)
    {
        $this->scrubFields = self::$defaults->scrubFields($this->get($scrubFields, 'scrubFields'));
    }

    /**
     * @param mixed $errorLevels
     * @return Builder
     */
    public function setErrorLevels($errorLevels)
    {
        $this->errorLevels = self::$defaults->errorLevels($this->get($errorLevels, 'errorLevels'));
    }

    /**
     * @return mixed
     */
    public function getCodeVersion()
    {
        return $this->codeVersion;
    }

    /**
     * @param mixed $codeVersion
     * @return Builder
     */
    public function setCodeVersion($codeVersion)
    {
        $version = $this->get($codeVersion, 'codeVersion');

        if (!isset($version)) {
            $version = $this->get($codeVersion, 'code_version');
        }

        $this->codeVersion = self::$defaults->codeVersion($version);
    }

    public function getLanguage()
    {
        return 'PHP ' . phpversion();
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return mixed
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param mixed $platform
     * @return Builder
     */
    public function setPlatform($platform)
    {
        $this->platform = self::$defaults->platform($this->get($platform, 'platform'));
    }

    public function getFramework()
    {
        return $this->framework;
    }

    /**
     * @param mixed $framework
     * @return Builder
     */
    public function setFramework($framework)
    {
        $this->framework = $this->get($framework, 'framework');
    }

    /**
     * @param mixed $context
     * @return Builder
     */
    public function setContext($context)
    {
        $this->context = $this->get($context, 'context');
    }

    /**
     * @param mixed $requestParams
     * @return Builder
     */
    public function setRequestParams($requestParams)
    {
        $this->requestParams = $this->get($requestParams, 'requestParams');
    }

    /**
     * @param mixed $requestBody
     * @return Builder
     */
    public function setRequestBody($requestBody)
    {
        $this->requestBody = $this->get($requestBody, 'requestBody');
    }

    /**
     * @param mixed $requestExtras
     * @return Builder
     */
    public function setRequestExtras($requestExtras)
    {
        $this->requestExtras = $this->get($requestExtras, 'requestExtras');
    }

    public function getRequest()
    {
        $scrubFields = $this->getScrubFields();
        $request     = new Request();
        $request->setUrl($this->getUrl($scrubFields));
        $request->setMethod($this->get($_SERVER, 'REQUEST_METHOD'));
        $request->setHeaders($this->getScrubbedHeaders($scrubFields));
        $request->setParams($this->getRequestParams());
        $request->setGet($this->scrub($_GET, $scrubFields));
        $request->setQueryString($this->scrubUrl($this->get($_SERVER, 'QUERY_STRING'), $scrubFields));
        $request->setPost($this->scrub($_POST, $scrubFields));
        $request->setBody($this->getRequestBody());
        $request->setUserIp($this->getUserIp());

        $extras = $this->getRequestExtras();

        if (!$extras) {
            $extras = [];
        }

        foreach ($extras as $key => $value) {
            if (in_array($scrubFields, $key)) {
                $request->{$key} = str_repeat('*', 8);
            } else {
                $request->{$key} = $value;
            }
        }

        if (is_array($_SESSION) && count($_SESSION) > 0) {
            $request->session = $this->scrub($_SESSION, $scrubFields);
        }

        return $request;
    }

    public function getUserIp()
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

    public function getUrl($scrubFields)
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

        return $this->scrubUrl($url, $scrubFields);
    }

    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     * @return Builder
     */
    public function setHost($host)
    {
        $this->host = $this->get($host, 'host');
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        $server = new Server();
        $server->setHost($this->host);
        $server->setRoot($this->serverRoot);
        $server->setBranch($this->serverBranch);
        $server->setCodeVersion($this->codeVersion);

        $scrubFields = $this->getScrubFields();
        $extras      = $this->serverExtras;

        if (!$extras) {
            $extras = [];
        }

        foreach ($extras as $key => $value) {
            if (in_array($scrubFields, $key)) {
                $server->{$key} = str_repeat('*', 8);
            } else {
                $server->{$key} = $value;
            }
        }

        if (array_key_exists('argv', $_SERVER)) {
            $server->argv = $_SERVER['argv'];
        }

        return $server;
    }

    /**
     * @param mixed $serverRoot
     * @return Builder
     */
    public function setServerRoot($serverRoot)
    {
        $root = $this->get($serverRoot, 'serverRoot');
        if (!isset($root)) {
            $root = $this->get($serverRoot, 'root');
        }

        $this->serverRoot = self::$defaults->serverRoot($root);
    }

    /**
     * @param mixed $serverBranch
     * @return Builder
     */
    public function setServerBranch($serverBranch)
    {
        $branch = $this->get($serverBranch, 'serverBranch');
        if (!isset($branch)) {
            $branch = $this->get($serverBranch, 'branch');
        }
        $this->serverBranch = self::$defaults->gitBranch($branch);
    }

    /**
     * @param mixed $serverCodeVersion
     * @return Builder
     */
    public function setServerCodeVersion($serverCodeVersion)
    {
        $this->serverCodeVersion = $this->get($serverCodeVersion, 'serverCodeVersion');
    }

    /**
     * @param mixed $serverExtras
     * @return Builder
     */
    public function setServerExtras($serverExtras)
    {
        $this->serverExtras = $this->get($serverExtras, 'serverExtras');
    }

    public function getCustom()
    {
        return null;
    }

    /**
     * @param mixed $custom
     */
    public function setCustom($custom)
    {
        $this->custom = $this->get($custom, 'custom');
    }

    /**
     * @return mixed
     */
    public function getFingerprint()
    {
        return $this->fingerprint;
    }

    /**
     * @param mixed $fingerprint
     */
    public function setFingerprint($fingerprint)
    {
        $this->fingerprint = $this->get($fingerprint, 'fingerprint');

        if (!is_null($this->fingerprint) && !is_callable($this->fingerprint)) {
            throw new \InvalidArgumentException(
                'If set, config[\'fingerprint\'] must be a callable that returns a uuid string'
            );
        }
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $this->get($title, 'title');

        if (!is_null($this->title) && !is_callable($this->title)) {
            throw new \InvalidArgumentException('If set, config[\'title\'] must be a callable that returns a string');
        }
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return time();
    }

    public function getNotifier()
    {
        return $this->notifier;
    }

    /**
     * @param mixed $notifier
     * @return Builder
     */
    public function setNotifier($notifier)
    {
        $this->notifier = self::$defaults->notifier($this->get($notifier, 'notifier'));
    }

    public function getBaseException()
    {
        return $this->baseException;
    }

    /**
     * @param mixed $baseException
     * @return Builder
     */
    public function setBaseException($baseException)
    {
        $this->baseException = self::$defaults->baseException($this->get($baseException, 'baseException'));
    }

    /**
     * @param $array
     * @param $fields
     * @param string $replacement
     * @return null
     */
    public function scrub($array, $fields, $replacement = '*')
    {
        if (!$fields || !$array) {
            return null;
        }

        array_walk_recursive(
            $array,
            function (&$val, $key) use ($fields, $replacement, $array) {
                if (array_key_exists($key, $array)) {
                    $val = str_repeat($replacement, 8);
                }
            }
        );

        return $array;
    }

    /**
     * @param $url
     * @param $fields
     * @return mixed
     */
    public function scrubUrl($url, $fields)
    {
        $query = parse_url($url, PHP_URL_QUERY);

        if (!$query) {
            return $url;
        }

        parse_str($query, $output);

        return str_replace($query, http_build_query($this->scrub($output, $fields, 'x')), $url);
    }

    public function getScrubbedHeaders($scrubFields)
    {
        return $this->scrub($this->getHeaders(), $scrubFields);
    }

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

    public function getRequestParams()
    {
        return $this->requestParams;
    }

    public function getRequestBody()
    {
        return $this->requestBody;
    }

    public function getRequestExtras()
    {
        return $this->requestExtras;
    }

    /**
     * @param mixed $includeCodeContext
     * @return Builder
     */
    public function setIncludeCodeContext($includeCodeContext)
    {
        $codeContext = $this->get($includeCodeContext, 'include_error_code_context');

        if (!is_null($codeContext)) {
            $this->includeCodeContext = $codeContext;
        }
    }

    public function willIncludeContext()
    {
        return $this->includeCodeContext;
    }

    public function willShiftFunction()
    {
        return $this->shiftFunction;
    }

    private function get($array, $key)
    {
        return Helper::get($array, $key);
    }

    /**
     * @param $name
     * @param $level
     * @param $toLog
     * @param $context
     * @return null
     */
    private function getOrCall($name, $level, $toLog, $context)
    {
        if (is_callable($this->{$name})) {
            try {
                return $this->{$name}($level, $toLog, $context);
            } catch (\Exception $e) {
                return null;
            }
        }

        return $this->{$name};
    }
}