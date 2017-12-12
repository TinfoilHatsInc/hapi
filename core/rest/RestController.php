<?php

namespace core\rest;

use core\exception\EntityNotFoundException;
use core\exception\InvalidRequestParamException;
use core\exception\ResourceNotFoundException;
use PHPMailer\PHPMailer\Exception;
use Symfony\Component\Yaml\Yaml;
use TinfoilHMAC\API\SecureAPIRequest;
use TinfoilHMAC\Exception\InvalidRequestException;
use TinfoilHMAC\Exception\MissingParameterException;

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
          return new ErrorResponse(ErrorResponse::HTTP_METHOD_NOT_ALLOWED, 'HTTP-method ' . strtoupper($method) . ' not allowed.');
        }
      } else {
        return new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, 'No REST config defined.');
      }
    }
    return $this->methods;
  }

  /**
   * @param $controllerName
   * @param array $params
   * @return Response
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
            return new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, 'Parameter missing.');
          }
        }
        $sortedParams[$paramName] = $params[$paramName];
      }
      if ($reflectionMethod->isStatic()) {
        return call_user_func_array($class . '::' . $function, $sortedParams);
      } else {
        return call_user_func_array([new $class(), $function], $sortedParams);
      }
    } else {
      return new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, 'Invalid controller called.');
    }
  }

  /**
   * @return Response
   */
  public function handleRequest()
  {

    $methods = $this->getMethods();
    if(get_parent_class($methods) == ErrorResponse::class) {
      return $methods;
    }
    try {
      $request = new SecureAPIRequest();
    } catch (InvalidRequestException $e) {
      return new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, $e->getMessage());
    } catch (MissingParameterException $e) {
      return new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, $e->getMessage());
    } catch (Exception $e) {
      return new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, 'Unknown error.');
    }

    $method = $request->getApiMethod();
    $params = $request->getParams();

    if(array_key_exists($method, $methods)) {
      $methodConfig = $methods[$method];
      if(!array_key_exists('controller', $methodConfig)) {
        return new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, 'No controller defined for called method.');
      }
      $controller =  $methodConfig['controller'];
      try {
        return $this->callController($controller, $params);
      } catch (InvalidRequestParamException $e) {
        return new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, $e->getMessage());
      } catch (EntityNotFoundException $e) {
        return new ErrorResponse(ErrorResponse::HTTP_NOT_FOUND, $e->getMessage());
      } catch (ResourceNotFoundException $e) {
        return new ErrorResponse(ErrorResponse::HTTP_NOT_FOUND, $e->getMessage());
      } catch (Exception $e) {
        return new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
      }
    } else {
      return new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, 'Unknown method called.');
    }

  }


}