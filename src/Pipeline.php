<?php

namespace Xudid\Pipeline;

use Collections\Collection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Pipeline implements RequestHandlerInterface
{
    private Collection $middlewares;
    private ResponseInterface $response;
    private ServerRequestInterface $request;

    public function __construct()
    {
        $this->middlewares = new Collection;
        $this->response = new Response();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $this->request = $request;
        return $this->response;
    }

    public function pipe(MiddlewareInterface $middleware)
    {
        $this->middlewares->push($middleware) ;
        return $this;
    }

    public function process(ServerRequestInterface $request , ResponseInterface $response): ResponseInterface
    {
        $this->request = $request;
        if($this->middlewares->valid())
        {
            $middleware = $this->middlewares->current();
            $this->response = $middleware->process($this->request,$this);
            $this->middlewares->next();
            $this->response = $this->process($this->request,$response);
        }

        return $this->response;
    }
}
