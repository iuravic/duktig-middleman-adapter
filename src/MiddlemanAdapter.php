<?php
namespace Duktig\Middleware\Dispatcher\Adapter\Middleman;

use mindplay\middleman\Dispatcher as MiddlemanDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;

class MiddlemanAdapter implements DelegateInterface
{
    private $stack;
    private $dispatcher;
    
    /**
     * Initialize the middleware dispatcher by providing the necessary params.
     * 
     * @param array $stack Middleware stack
     */
    public function init(array $stack) : void
    {
        $this->stack = $stack;
        $this->dispatcher = new MiddlemanDispatcher($this->stack);
    }

    /**
     * {@inheritDoc}
     * @see \Interop\Http\ServerMiddleware\DelegateInterface::process()
     */
    public function process(ServerRequestInterface $request)
    {
        if (null === $this->dispatcher) {
            throw new \BadMethodCallException("Middleware stack must first be provided via the init() method.");
        }
        return $this->dispatcher->dispatch($request);
    }
}