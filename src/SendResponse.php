<?php
namespace MicroPHP;

class SendResponse
{
    private $response;
    
    public function __construct(SendResponseInterface $response=null)
    {
        $this->response = isset($response) ? $response: new HttpResponse();
    }

    public function notFound(){
      $this->response->notFound();
    }

    public function created($result){
       $this->response->created($result);
    }

    public function noContent(){
       $this->response->noContent();
    }

    public function unauthorized(){
       $this->response->unauthorized();
    }

    public function badRequest(){
       $this->response->badRequest();
    }

    public function json($result){
       $this->response->json($result);
    }

    public function location($url){
       $this->response->location($url);
    }
}