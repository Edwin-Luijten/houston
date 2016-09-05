<?php

namespace EdwinLuijten\Houston\Senders;

use EdwinLuijten\Houston\Payload\Payload;

interface SenderInterface
{
    public function send(Payload $payload);
}