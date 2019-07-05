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
     * @var object
     */
    public $controller;

    /**
     * @var string
     */
    public $action;

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
        return [$this->controller, $this->action];
    }

    /**
     * 获取Controller
     * @return object
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * 获取Action
     * @return object
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * 获取中间件名称
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
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
