<?php

namespace core\rest;

use core\exception\DatabaseException;
use core\exception\EntityNotFoundException;
use core\exception\InvalidRequestParamException;
use core\exception\InvalidRESTConfigException;
use core\exception\MethodNotAllowedException;
use core\exception\ResourceNotFoundException;
use core\exception\SharedKeyUpdateException;
use core\utils\HAPISharedKey;
use Exception;
use Symfony\Component\Yaml\Yaml;
use TinfoilHMAC\API\SecureAPIRequest;
use TinfoilHMAC\Exception\InvalidRequestException;
use TinfoilHMAC\Exception\InvalidSessionParamException;
use TinfoilHMAC\Exception\MissingParameterException;

class RestController
{

  private $methods;

  /**
   * @return mixed
   * @throws InvalidRESTConfigException
   * @throws MethodNotAllowedException
   */
  private function getMethods()
  {
    if (empty($this->methods)) {
      $config = Yaml::parse(file_get_contents('config/config.yml', FILE_USE_INCLUDE_PATH));
      if (array_key_exists('rest_config', $config)) {
        $methods = $config['rest_config'];
        if (empty($methods)) {
          $methods = [];
        }
        $method = RequestController::getHTTPRequestMethod();
        if (array_key_exists($method, $methods)) {
          $this->methods = $methods[$method];
        } else {
          throw new MethodNotAllowedException('HTTP-method ' . strtoupper($method) . ' not allowed.');
        }
      } else {
        throw new InvalidRESTConfigException('No REST config defined.');
      }
    }
    return $this->methods;
  }

  /**
   * @param $controllerName
   * @param array $params
   * @return mixed
   * @throws InvalidRESTConfigException
   * @throws InvalidRequestParamException
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
      foreach ($reflectionParams as $param) {
        $paramName = $param->getName();
        if (!array_key_exists($paramName, $params)) {
          if ($param->isDefaultValueAvailable()) {
            $params[$paramName] = $param->getDefaultValue();
          } else {
            throw new InvalidRequestParamException('Parameter missing.');
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
      throw new InvalidRESTConfigException('Invalid controller called.');
    }
  }

  /**
   * @return Response
   */
  public function handleRequest()
  {
    try {
      $methods = $this->getMethods();
      $request = new SecureAPIRequest(HAPISharedKey::class);
    } catch (InvalidRequestException
    | MissingParameterException
    | InvalidSessionParamException $e) {
      return new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, $e->getMessage());
    } catch (DatabaseException
    | InvalidRESTConfigException $e) {
      return new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
    } catch (MethodNotAllowedException $e) {
      return new ErrorResponse(ErrorResponse::HTTP_METHOD_NOT_ALLOWED, $e->getMessage());
    } catch (SharedKeyUpdateException $e) {
      return new ErrorResponse(ErrorResponse::HTTP_UNAUTHORIZED, $e->getMessage());
    } catch (Exception $e) {
      return new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, 'Unknown error.');
    }

    $method = $request->getApiMethod();
    $params = $request->getParams();

    if (array_key_exists($method, $methods)) {
      $methodConfig = $methods[$method];
      if (!array_key_exists('controller', $methodConfig)) {
        return new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, 'No controller defined for called method.');
      }
      $controller = $methodConfig['controller'];
      try {
        return $this->callController($controller, $params);
      } catch (InvalidRequestParamException $e) {
        return new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, $e->getMessage());
      } catch (EntityNotFoundException
      | ResourceNotFoundException $e) {
        return new ErrorResponse(ErrorResponse::HTTP_NOT_FOUND, $e->getMessage());
      } catch (InvalidRESTConfigException $e) {
        return new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
      } catch (Exception $e) {
        return new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, 'Unknown error.');
      }
    } else {
      return new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, 'Unknown method called.');
    }

  }


}