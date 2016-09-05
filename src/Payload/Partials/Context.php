<?php

namespace EdwinLuijten\Houston\Payload\Partials;

use EdwinLuijten\Houston\Helper;

class Context extends AbstractPayload
{

    private $pre;

    private $post;

    public function __construct($pre, $post)
    {
        $this->pre  = $pre;
        $this->post = $post;
    }

    public function getPre()
    {
        return $this->pre;
    }

    public function setPre($pre)
    {
        $this->mustBeString($pre, 'pre');
        $this->pre = $pre;
    }

    public function getPost()
    {
        return $this->post;
    }

    public function setPost($post)
    {
        $this->mustBeString($post, 'post');
        $this->post = $post;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return Helper::serialize(get_object_vars($this));
    }

    /**
     * @param $subject
     * @param $argument
     */
    private function mustBeString($subject, $argument)
    {

        foreach ($subject as $line) {
            if (!is_string($line)) {
                throw new \InvalidArgumentException(sprintf('$%s must be a string', $argument));
            }
        }
    }
}