<?php

namespace MicroPHP;

interface ServerInterface
{
    public function getMethod();
    public function getFullUrl();
    public function getQueryString();
    public function getContentType();
    public function hasContentType();
    public function getContent();
}