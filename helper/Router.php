<?php

    class Router {

        private $defaultController;
        private $defaultMethod;

        public function __construct($defaultController, $defaultMethod) {
            $this->defaultController = $defaultController;
            $this->defaultMethod = $defaultMethod;
        }



        private function getControllerFrom($module) {
            $controllerName = 'get' . ucfirst($module) . 'Controller';
            $validController = method_exists("Configuration", $controllerName) ? $controllerName : $this->defaultController;
            return call_user_func(array("Configuration", $validController));
        }


        public function route($controllerName, $methodName, $idQuestion) {
            $controller = $this->getControllerFrom($controllerName);
            $this->executeMethodFromController($controller, $methodName, $idQuestion);
        }
        private function executeMethodFromController($controller, $method, $idQuestion) {
            $validMethod = method_exists($controller, $method) ? $method : $this->defaultMethod;
            call_user_func(array($controller, $validMethod), $idQuestion);
        }
}