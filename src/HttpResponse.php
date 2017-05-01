<?php
namespace MicroPHP;


class HttpResponse implements SendResponseInterface
{
    public function notFound(){
        header("HTTP/1.1 404 Not found");
        exit;
    }

    public function created($result){
        header("HTTP/1.1 201 Created");
        echo json_encode($result);
    }

    public function noContent(){
        header("HTTP/1.1 204 No Content");
        exit;
    }
    
    public function unauthorized(){
        header("HTTP/1.1 401 Unauthorized");
        exit;
    }

    public function badRequest(){
        header("HTTP/1.1 400 Bad Request");
        exit;
    }

    public function json($result){
        echo json_encode($result);
    }

    public function location($url){
        header('Location:'.$url);
    }
}