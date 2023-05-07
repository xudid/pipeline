<?php

namespace Xudid\Pipeline;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Pipeline implements RequestHandlerInterface
{
    private array $middlewares=[];
    private int $index = 0;
    private ResponseInterface $response;
    private ServerRequestInterface $request;

    public function __construct()
    {
        $this->response = new Response();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        return $this->response;
    }

    public function pipe(MiddlewareInterface $middleware)
    {
        $this->middlewares[]=$middleware;
        return $this;
    }

    public function process(ServerRequestInterface $request , ResponseInterface $response): ResponseInterface
    {
        $this->request = $request;
        if(isset($this->middlewares[$this->index]))
        {
            $middleware = $this->middlewares[$this->index];
            $this->response = $middleware->process($this->request,$this);


            $statusCode = $this->response->getStatusCode();
            if($statusCode>200)
            {
                return $this->response ;
            }

            $success = $request->getAttribute("success");

            if($success && $this->index == (count($this->middlewares))-1)
            {
                return $this->response ;
            }
            $this->index++;

            $this->response =  $this->process($this->request,$response);
        }

        return $this->response;
    }
}
