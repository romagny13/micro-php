<?php
namespace MicroPHP;

class Flash
{
    private $flashKey;
    private $storage;
    
    public function __construct(StorageInterface $storage = null)
    {
        $this->storage = isset($storage) ? $storage : new SessionStorage();
        $this->flashKey = '__micro__flash_message';
    }

    public function addMessage($status, $message){
        $this->storage->add($this->flashKey, ['status' => $status,'message' => $message]);
    }

    public function hasFlashMessage(){
        return $this->storage->has($this->flashKey);
    }

    public function deleteFlashMessage(){
        $this->storage->delete($this->flashKey);
    }

    public function getFlashMessage(){
        return  $this->storage->get($this->flashKey);
    }
    
    public function hasMessage($status){
        if($this->hasFlashMessage()){
            $flashMessage = $this->getFlashMessage();
            return isset($flashMessage['status']) && isset($flashMessage['message']) && $flashMessage['status'] === $status;
        }
        return false;
    }

    public function getMessage($status){
        if($this->hasMessage($status)){
            $flashMessage = $this->getFlashMessage();
            $message = $flashMessage['message'];
            $this->deleteFlashMessage();
            return $message;
        }
        return false;
    }
}
