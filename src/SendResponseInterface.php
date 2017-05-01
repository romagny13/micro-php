<?php
namespace MicroPHP;


interface SendResponseInterface
{
    public function notFound();
    public function created($result);
    public function noContent();
    public function unauthorized();
    public function badRequest();
    public function json($result);
    public function location($url);
}