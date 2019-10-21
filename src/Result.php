<?php

namespace Mix\Route;

/**
 * Class Result
 * @package Mix\Route
 * @author liu,jian <coder.keda@gmail.com>
 */
class Result
{

    /**
     * @var callable
     */
    public $callbock;

    /**
     * @var array
     */
    public $middleware;

    /**
     * @var array
     */
    public $params;

    /**
     * Rule constructor.
     * @param array $config
     * @throws \PhpDocReader\AnnotationException
     * @throws \ReflectionException
     */
    public function __construct(array $callbock, array $middleware, array $params)
    {
        $this->callbock   = $callbock;
        $this->middleware = $middleware;
        $this->params     = $params;
    }

    /**
     * 获取Callback
     * @return callable
     */
    public function getCallback(): callable
    {
        list($class, $method) = $this->callbock;
        return [new $class, $method];
    }

    /**
     * 获取中间件名称
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * 获取路由参数
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

}
