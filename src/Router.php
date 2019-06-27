<?php

namespace Mix\Route;

use Mix\Bean\BeanInjector;
use Mix\Route\Exception\NotFoundException;
use Mix\Route\Helper\FileSystemHelper;
use Mix\Route\Helper\NameHelper;

/**
 * Class Router
 * @package Mix\Route
 * @author liu,jian <coder.keda@gmail.com>
 */
class Router
{

    /**
     * 控制器命名空间
     * @var string
     */
    public $controllerNamespace = '';

    /**
     * 中间件命名空间
     * @var string
     */
    public $middlewaresNamespace = '';

    /**
     * 默认变量规则
     * @var string
     */
    public $defaultPattern = '[\w-]+';

    /**
     * 路由变量规则
     * @var array
     */
    public $patterns = [];

    /**
     * 全局中间件
     * @var array
     */
    public $middlewares = [];

    /**
     * 路由规则
     * @var array
     */
    public $rules = [];

    /**
     * 转化后的路由规则
     * @var array
     */
    protected $_materials = [];

    /**
     * Route constructor.
     * @param array $config
     * @throws \PhpDocReader\AnnotationException
     * @throws \ReflectionException
     */
    public function __construct(array $config)
    {
        BeanInjector::inject($this, $config);
        $this->init();
    }

    /**
     * 初始化
     * 生成路由数据，将路由规则转换为正则表达式，并提取路由参数名
     */
    public function init()
    {
        // URL 目录处理
        $rules = [];
        foreach ($this->rules as $rule => $route) {
            $rules[$rule] = $route;
            if (strpos($rule, '{controller}') !== false && strpos($rule, '{action}') !== false) {
                $prev    = dirname($rule);
                $prevTwo = dirname($prev);
                $prevTwo = $prevTwo == '.' ? '/' : $prevTwo;
                $prevTwo = $prevTwo == '\\' ? '/' : $prevTwo;
                list($controller) = $route;
                // 增加上两级的路由
                $prevRules = [
                    $prev    => [$controller, 'Index'],
                    $prevTwo => [str_replace('{controller}', 'Index', $controller), 'Index'],
                ];
                // 附上中间件
                if (isset($route['middlewares'])) {
                    $prevRules[$prev]['middlewares']    = $route['middlewares'];
                    $prevRules[$prevTwo]['middlewares'] = $route['middlewares'];
                }
                $rules += $prevRules;
            }
        }
        // 转正则
        foreach ($rules as $rule => $route) {
            if ($blank = strpos($rule, ' ')) {
                $method = substr($rule, 0, $blank);
                $method = "(?:{$method}) ";
                $rule   = substr($rule, $blank + 1);
            } else {
                $method = '(?:CLI|GET|POST|PUT|PATCH|DELETE|HEAD|OPTIONS) ';
            }
            $fragment = explode('/', $rule);
            $names    = [];
            foreach ($fragment as $k => $v) {
                preg_match('/{([\w-]+)}/i', $v, $matches);
                if (!empty($matches)) {
                    list($fname) = $matches;
                    if (isset($this->patterns[$fname])) {
                        $fragment[$k] = str_replace("{$fname}", "({$this->patterns[$fname]})", $fragment[$k]);
                    } else {
                        $fragment[$k] = str_replace("{$fname}", "({$this->defaultPattern})", $fragment[$k]);
                    }
                    $names[] = $fname;
                }
            }
            $pattern            = '/^' . $method . implode('\/', $fragment) . '\/*$/i';
            $this->_materials[] = [$pattern, $route, $names];
        }
    }

    /**
     * 匹配
     * @param string $method
     * @param string $pathinfo
     * @return Rule
     * @throws \PhpDocReader\AnnotationException
     * @throws \ReflectionException
     */
    public function match(string $method, string $pathinfo)
    {
        // 由于路由歧义，会存在多条路由规则都可匹配的情况
        $rule   = "{$method} {$pathinfo}";
        $result = [];
        foreach ($this->_materials as $item) {
            list($pattern, $route, $names) = $item;
            if (preg_match($pattern, $rule, $matches)) {
                $queryParams = [];
                // 提取路由查询参数
                foreach ($names as $k => $v) {
                    $queryParams[$v] = $matches[$k + 1];
                }
                // 替换路由中的变量
                $fragments   = explode('/', $route[0]);
                $fragments[] = $route[1];
                foreach ($fragments as $k => $v) {
                    preg_match('/{([\w-]+)}/i', $v, $matches);
                    if (!empty($matches)) {
                        list($fname) = $matches;
                        if (isset($queryParams[$fname])) {
                            $fragments[$k] = $queryParams[$fname];
                        }
                    }
                }
                // 记录参数
                $shortAction = array_pop($fragments);
                $shortClass  = implode('\\', $fragments);
                $result[]    = [[$shortClass, $shortAction, 'middlewares' => isset($route['middlewares']) ? $route['middlewares'] : []], $queryParams];
            }
        }
        // 筛选有效的结果
        foreach ($result as $item) {
            list($route, $queryParams) = $item;
            // 路由参数导入请求类
            \Mix::$app->request->setRoute($queryParams);
            // 实例化控制器
            list($shortClass, $shortAction) = $route;
            $controllerDir    = FileSystemHelper::dirname($shortClass);
            $controllerDir    = $controllerDir == '.' ? '' : "$controllerDir\\";
            $controllerName   = NameHelper::snakeToCamel(\Mix\Helper\FileSystemHelper::basename($shortClass), true);
            $controllerClass  = "{$this->controllerNamespace}\\{$controllerDir}{$controllerName}Controller";
            $shortAction      = NameHelper::snakeToCamel($shortAction, true);
            $controllerAction = "action{$shortAction}";
            // 判断类是否存在
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                // 判断方法是否存在
                if (method_exists($controllerInstance, $controllerAction)) {
                    // 返回
                    return new Rule([
                        'controller'  => $controllerInstance,
                        'method'      => $controllerAction,
                        'middlewares' => array_merge($this->middlewares, $route['middlewares']),
                        'params'      => $queryParams,
                    ]);
                }
            }
            // 不带路由参数的路由规则找不到时，直接抛出错误
            if (empty($queryParams)) {
                break;
            }
        }
        throw new NotFoundException('Not Found (#404)');
    }

}
