<?php

namespace EdwinLuijten\Houston\Payload\Extractors;

use EdwinLuijten\Houston\Payload\Partials\Server;

class ServerExtractor extends AbstractExtractor
{
    private $host;

    private $serverRoot;

    private $serverBranch;

    private $codeVersion;

    private $serverExtras;

    /**
     * Server constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct($config);

        $this->setServerRoot($config);
        $this->setServerBranch($config);
        $this->setServerCodeVersion($config);
        $this->setServerExtras($config);
    }

    /**
     * @return Server
     */
    public function extract()
    {
        $server = new Server();
        $server->setHost($this->host);
        $server->setRoot($this->serverRoot);
        $server->setBranch($this->serverBranch);
        $server->setCodeVersion($this->codeVersion);

        $extras = $this->serverExtras;

        if (!$extras) {
            $extras = [];
        }

        foreach ($extras as $key => $value) {
            if (in_array($this->scrubFields, $key)) {
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
     */
    private function setServerRoot($serverRoot)
    {
        $root = $this->get($serverRoot, 'serverRoot');

        if (!isset($root)) {
            $root = $this->get($serverRoot, 'root');
        }

        $this->serverRoot = self::$defaults->serverRoot($root);
    }

    /**
     * @param mixed $serverBranch
     */
    private function setServerBranch($serverBranch)
    {
        $branch = $this->get($serverBranch, 'serverBranch');

        if (!isset($branch)) {
            $branch = $this->get($serverBranch, 'branch');
        }

        $this->serverBranch = self::$defaults->gitBranch($branch);
    }

    /**
     * @param mixed $serverCodeVersion
     */
    private function setServerCodeVersion($serverCodeVersion)
    {
        $this->codeVersion = $this->get($serverCodeVersion, 'serverCodeVersion');
    }

    /**
     * @param mixed $serverExtras
     */
    private function setServerExtras($serverExtras)
    {
        $this->serverExtras = $this->get($serverExtras, 'serverExtras');
    }
}