<?php

namespace MicroPHP;


class ServerInfos implements ServerInterface
{
    public function getMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getFullUrl(){
        return (isset($_SERVER['HTTPS']) ? 'https' : 'http') ."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    public function getQueryString() {
        return $_SERVER['QUERY_STRING'];
    }

    public function hasContentType(){
        return isset($_SERVER['CONTENT_TYPE']);
    }
    
    public function getContentType(){
        return $_SERVER['CONTENT_TYPE'];
    }

    public function getContent(){
        if($this->hasContentType()){
            $contentType = $this->getContentType();
            if ($contentType === 'application/json') {
                $json = file_get_contents('php://input');
                return json_decode($json);
            } else if ($contentType === 'application/x-www-form-urlencoded') {
                return(object)$_POST;
            }
        }
        return null;
    }
    

}