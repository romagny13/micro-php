<?php

namespace MicroPHP;

class Csrf
{
    private $storage;
    private $dataStrategy;
    private $csrfKey;
    
    public function __construct(StorageInterface $storage = null, DataStrategyInterface $dataStrategy = null)
    {
        $this->storage = isset($storage) ? $storage : new SessionStorage();
        $this->dataStrategy = isset($dataStrategy) ? $dataStrategy : new PostStrategy();
        $this->csrfKey = 'csrf_token';
    }

    public function generateTokenKey(){
        return sha1(session_id(). openssl_random_pseudo_bytes(64));
    }

    public function getTokenName(){
        return $this->csrfKey;
    }

    public function createToken(){
        $csrf_token = $this->generateTokenKey();
        $this->storage->add($this->csrfKey, $csrf_token);
        return $csrf_token;
    }

    public function hasStoredToken(){
        return $this->storage->has($this->csrfKey);
    }

    public function getStoredToken(){
        if($this->hasStoredToken()){
            return $this->storage->get($this->csrfKey);
        }
    }

    public function deleteStoredToken(){
        if($this->hasStoredToken()){
           return $this->storage->delete($this->csrfKey);
        }
        return false;
    }

    public function getSubmittedToken(){
        return $this->dataStrategy->getData($this->csrfKey);
    }

    public function check(){
        $csrf_token = $this->getStoredToken();
        $post_token = $this->getSubmittedToken();

        $result = $csrf_token && $post_token && $csrf_token === $post_token;
        $this->deleteStoredToken();
        return $result;
    }
}