<?php
namespace MicroPHP;


class SendResponse
{
    public $messages = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    protected $headerStrategy;
    protected $jsonStrategy;
    
    public function __construct(HeaderStrategyInterface $headerStrategy = null, JsonStrategyInterface $jsonStrategy=null)
    {
        $this->headerStrategy = isset($headerStrategy) ? $headerStrategy : new HeaderStrategy();
        $this->jsonStrategy = isset($jsonStrategy) ? $jsonStrategy : new JsonStrategy();
    }

    public function getDefaultMessage($code){
        if(!isset($this->messages[$code])){throw new \Exception('No message found for response code '. $code); }
        return $this->messages[$code];
    }

    public function setHeaderString($string){
        $this->headerStrategy->setHeaderString($string);
        return $this;
    }

    public function setHeader($code, $message = null){
        if(!isset($message)){
            $message = $this->getDefaultMessage($code);
        }
        return $this->setHeaderString("HTTP/1.1 $code $message");
    }

    public function notFound($message = null){
        $this->setHeader(404, $message);
    }

    public function created($content = null, $message = null){
        $this->setHeader(201,$message);
        if(isset($content)){
            $this->json($content);
        }
    }

    public function ok($content = null, $message = null){
        $this->setHeader(200,$message);
        if(isset($content)){
            $this->json($content);
        }
    }

    public function noContent($message = null){
        $this->setHeader(204, $message);
    }
    
    public function unauthorized($message = null){
        $this->setHeader(401, $message);
    }

    public function badRequest($message = null){
        $this->setHeader(400, $message);
    }

    public function internalServerError(){
        $this->setHeader(500);
    }

    public function redirect($url){
        $this->setHeaderString('Location:'.$url);
    }

    public function json($content){
        $this->setHeaderString('Content-Type: application/json');
        $this->jsonStrategy->encode($content);
    }
}