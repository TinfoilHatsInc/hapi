<?php

namespace core\rest;

use PHPMailer\PHPMailer\Exception;
use Symfony\Component\Yaml\Yaml;
use TinfoilHMAC\API\SecureIncomingRequest;
use TinfoilHMAC\Exception\InvalidRequestException;
use TinfoilHMAC\Exception\MissingParameterException;
use Twig\Error\Error;

class RestController
{

  private $methods;

  /**
   * @return mixed
   */
  private function getMethods()
  {
    if (empty($this->methods)) {
      $config = Yaml::parse(file_get_contents('config/config.yml', FILE_USE_INCLUDE_PATH));
      if (array_key_exists('rest_config', $config)) {
        $methods = $config['rest_config'];
        if(empty($methods)) {
          $methods = [];
        }
        $method = RequestController::getHTTPRequestMethod();
        if (array_key_exists($method, $methods)) {
          $this->methods = $methods[$method];
        } else {
          (new ErrorResponse(ErrorResponse::HTTP_METHOD_NOT_ALLOWED, 'HTTP-method ' . strtoupper($method) . ' not allowed.'))->send();
        }
      } else {
        (new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, 'No REST config defined.'))->send();
      }
    }
    return $this->methods;
  }

  /**
   * @param $controllerName
   * @param array $params
   * @return mixed
   */
  private function callController($controllerName, $params = [])
  {
    $controllerArr = explode('::', $controllerName);
    $class = $controllerArr[0];
    if (class_exists($class)) {
      $function = $controllerArr[1];
      $reflectionClass = new \ReflectionClass($class);
      $reflectionMethod = $reflectionClass->getMethod($function);
      $reflectionParams = $reflectionMethod->getParameters();
      $sortedParams = [];
      foreach($reflectionParams as $param) {
        $paramName = $param->getName();
        if(!array_key_exists($paramName, $params)) {
          if($param->isDefaultValueAvailable()) {
            $params[$paramName] = $param->getDefaultValue();
          } else {
            (new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, 'Parameter missing.'))->send();
          }
        }
        $sortedParams[$paramName] = $params[$paramName];
      }
      if ($reflectionMethod->isStatic()) {
        $result = call_user_func_array($class . '::' . $function, $sortedParams);
      } else {
        $result = call_user_func_array([new $class(), $function], $sortedParams);
      }
      return $result;
    } else {
      (new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, 'Invalid controller called.'))->send();
    }
  }

  public function handleRequest()
  {

    $methods = $this->getMethods();
    try {
      $request = SecureIncomingRequest::create();
    } catch (InvalidRequestException $e) {
      (new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, $e->getMessage()))->send();
    } catch (MissingParameterException $e) {
      (new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, $e->getMessage()))->send();
    } catch (Exception $e) {
      (new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, 'Unknown error.'))->send();
    }

    $method = $request->getApiMethod();
    $params = $request->getParams();

    if(array_key_exists($method, $methods)) {
      $methodConfig = $methods[$method];
      if(!array_key_exists('controller', $methodConfig)) {
        (new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, 'No controller defined for called method.'))->send();
      }
      $controller =  $methodConfig['controller'];
      try {
        $value = $this->callController($controller, $params);
        (new SuccessResponse(SuccessResponse::HTTP_OK, $value))->send();
      } catch (Exception $e) {
        (new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage()))->send();
      }
    } else {
      (new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, 'Unknown method called.'))->send();
    }

  }


}