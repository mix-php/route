<?php

namespace Mix\Route;

use Mix\Bean\BeanInjector;

/**
 * Class Rule
 * @package Mix\Route
 * @author liu,jian <coder.keda@gmail.com>
 */
class Rule
{

    /**
     * @var object
     */
    public $controller;

    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $middlewares;

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
    public function __construct(array $config)
    {
        BeanInjector::inject($this, $config);
    }

    /**
     * 获取Callback
     * @return callable
     */
    public function getCallback(): callable
    {
        return [$this->controller, $this->method];
    }

    /**
     * 获取中间件名称
     * @return array
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * 获取路由参数
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

}
