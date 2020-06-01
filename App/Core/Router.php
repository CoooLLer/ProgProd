<?php

namespace App\Core;

use Exception;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class Router
{
    private array $routes;

    private array $matchedRoute;

    private array $params = [];

    private $cache;

    public string $action;
    public string $requestMethod;


    public function __construct(array $routes)
    {
        if (!is_array($routes)) {
            throw new Exception('Wrong routes parameter.');
        }
        $this->routes = $routes;
        $this->cache = new FilesystemAdapter('', 3600, PROJECT_ROOT . '/cache');
    }

    public function matchRoute(string $checkRoute, string $method): bool
    {
        foreach ($this->routes as $route) {
            if (preg_match($route['path'], $checkRoute, $matches) && in_array($method, $route['methods'])) {
                $this->matchedRoute = $route;
                //удалим элемент с полным совпадением
                $matches = array_slice($matches, 1);

                //вытащим параметры в соответствии с настройками роута
                foreach ($matches as $key => $match) {
                    $this->params[] = $match;
                }
                return true;
            }
        }

        return false;
    }

    public function dispatch()
    {
        //немного покостылим, что бы сильно не переделывать под командную строку, знаю, что не хорошо =)
        global $argv;

        if (!empty($argv)) {
            $this->action = $argv[1];
            $this->requestMethod = 'COMMAND';
        } else {
            $this->action = $_SERVER['REQUEST_URI'];
            $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        }

        /** @var CacheItemInterface $cacheItem */
        $cacheItem = $this->cache->getItem(Kernel::getInstance()->getCacheKey());

        if ($cacheItem->isHit()) {
            echo $cacheItem->get();
            return;
        }

        if (!$this->matchRoute($this->action, $this->requestMethod)) {
            throw new Exception(sprintf('No route found for query %s', $this->action));
        }

        $controller = Kernel::getInstance()->getConfig()['controllers_namespace'] . '\\' . $this->matchedRoute['controller'];


        if (class_exists($controller)) {
            $controller_object = new $controller();
            $action = $this->matchedRoute['action'];
            if (!method_exists($controller_object, $action)) {
                throw new Exception(sprintf("Controller class %s doesn`t have method %s", $controller, $action));
            }

            if (in_array($this->requestMethod, ['PUT'])) {
                $data = json_decode(file_get_contents("php://input"));
                $this->params[] = $data;
            }

            $controller_object->$action(...$this->params);


        } else {
            throw new Exception(sprintf("Controller class %s not found", $controller));
        }


    }
}