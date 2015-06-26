<?php

namespace Optimus\BatchRequest\Router\Adapter;

use Optimus\ApiConsumer\Router;
use Optimus\BatchRequest\Router\RouterInterface;

class OptimusApiConsumer implements RouterInterface {

    private $router;

    private $config;

    public function __construct(Router $router, array $config)
    {
        $this->router = $router;
        $this->config = $config;
    }

    public function batch(array $requests)
    {
        $order = $this->getKeyOrder($requests);

        if ($this->config["disable_middleware"]) {
            $this->router->disableMiddleware();
        }

        $responses = $this->router->batchRequest($this->formatRequests($requests));

        return array_combine($order, $responses);
    }

    private function formatRequests(array $requests)
    {
        return array_map(function($request){
            return [
                isset($request['method']) ? $request['method'] : 'GET',
                $this->createActionUrl($request['action']),
                isset($request['data']) ? $request['data'] : [],
                isset($request['headers']) ? $request['headers'] : []
            ];
        }, $requests);
    }

    private function getKeyOrder(array $requests)
    {
        $return = [];

        foreach($requests as $request) {
            $return[] = $request['key'];
        }

        return $return;
    }

    private function createActionUrl($action)
    {
        return is_string($this->config['url_prefix']) ?
                    sprintf('%s%s', $this->config['url_prefix'], $action) : $action;
    }

}
