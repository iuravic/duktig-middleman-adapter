<?php
namespace Duktig\Middleware\Dispatcher\Adapter\Middleman;

use PHPUnit\Framework\TestCase;
use Duktig\Middleware\Dispatcher\Adapter\Middleman\MiddlemanAdapter;
use mindplay\middleman\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

class MiddlemanAdapterTest extends TestCase
{
    private $adapter;
    private $mockRequest;
    
    public function setUp()
    {
        parent::setUp();
        $this->setAdapterAndMock();
    }
    
    public function tearDown()
    {
        parent::tearDown();
        if ($container = \Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }
        \Mockery::close();
        $this->unsetAdapterAndMock();
    }
    
    public function testThrowsExceptionIfMiddlewareStackIsNotSet()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage("Middleware stack must first be provided via the init() method.");
        $this->adapter->process($this->mockRequest);
    }
    
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRunsMiddlemansDispatchMethod()
    {
        $stack = [
            \Mockery::mock(MiddlewareInterface::class), 
            \Mockery::mock(MiddlewareInterface::class)
        ];
        $mockDispatcher = \Mockery::mock('overload:mindplay\middleman\Dispatcher');
        $mockDispatcher->shouldReceive('dispatch')->once()->with($this->mockRequest);
        
        $this->adapter->init($stack);
        $this->adapter->process($this->mockRequest);
    }
    
    private function setAdapterAndMock()
    {
        $this->adapter = new MiddlemanAdapter();
        $this->mockRequest = \Mockery::mock(ServerRequestInterface::class);
    }
    
    private function unsetAdapterAndMock()
    {
        unset($this->adapter, $this->mockRequest);
    }
}