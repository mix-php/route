<?php

namespace Mix\Route;

use Mix\Bean\BeanInjector;

/**
 * Class MatchRule
 * @package Mix\Route
 * @author liu,jian <coder.keda@gmail.com>
 */
class MatchRule
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
    public function __construct(callable $callbock, array $middleware, array $params)
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
        return $this->callbock;
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
