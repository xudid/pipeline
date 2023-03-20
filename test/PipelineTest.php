<?php

namespace Xudid\Pipeline;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class PipelineTest extends TestCase
{
    public function testHandleReturnResponseInterface()
    {
        $pipeline = new Pipeline(new Response());
        $result = $pipeline->handle(ServerRequest::fromGlobals());
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testPipeReturnIsFluent()
    {
        $pipeline = new Pipeline(new Response());
        $builder = $this->getMockBuilder(MiddlewareInterface::class);
        $middleware = $builder->getMock();
        $result = $pipeline->pipe($middleware);
        $this->assertInstanceOf(Pipeline::class, $result);
    }

    public function testProcessWithoutMiddlewareReturnResponse()
    {
        $response = new Response();
        $pipeline = new Pipeline($response);
        $result = $pipeline->process(ServerRequest::fromGlobals(), $response);
        $this->assertInstanceOf(ResponseInterface::class, $result);

    }
}
