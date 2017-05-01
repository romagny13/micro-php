<?php
namespace MicroPHP;

class Url
{
    private $server;

    public function __construct(ServerInterface $server = null)
    {
        $this->server = isset($server) ? $server: new ServerInfos();
    }

    public function getMethod(){
        return $this->server->getMethod();
    }

    public function getFullUrl(){
        return $this->server->getFullUrl();
    }

    public function getOrigin() {
        $url = $this->getFullUrl();
        return  $this->trimQueryString($url);
    }

    public function trimBase($base, $url) {
        $fullPath = str_replace($base, '',$url);
        if ($fullPath === '') {
            return '/';
        }
        else {
            // add /
            if ($fullPath[0] !== '/') { $fullPath = '/' . $fullPath; }
            return $fullPath;
        }
    }

    public function trimQueryString($url) {
        if (strpos($url,'?')) {
            return preg_split('/\?/',$url)[0];
        }
        return $url;
    }

    public function getPath($base, $url){
        $path = $this->trimBase($base, $url);
        return $this->trimQueryString($path);
    }

    public function isValidMethod($method){
        return preg_match('/^(GET|POST|PUT|DELETE|PATCH|OPTIONS)$/', $method) === 1;
    }

    public function validMethods($methods){
        foreach ($methods as $method){
            if(! Url::isValidMethod($method)){
                return false;
            }
        }
        return true;
    }

    public function concatGroupBaseAndPath($groupBase, $path){
        // base '' or '/' or 'auth' or '/auth'
        // path '' or '/' or 'signin' or '/signin'
        if($groupBase === '' || $groupBase === '/'){
            if($path === '' || $path === '/'){
                return '/';
            }
            else if($path[0] !== '/'){
                return '/' .$path;
            }
            else {
                return $path;
            }
        }
        else {
            if($groupBase[0] !== '/') {
                $groupBase = '/' . $groupBase;
            }

            if($path === '' || $path === '/'){
                return $groupBase;
            }
            else if($path[0] !== '/'){
                return $groupBase . '/' .$path;
            }
            else {
                return $groupBase .$path;
            }
        }
    }
}