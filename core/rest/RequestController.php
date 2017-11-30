<?php

namespace core\rest;

class RequestController
{

  /**
   * @var Request
   */
  private static $request;

  /**
   * @return string
   */
  public static function getHTTPRequestMethod()
  {
    return strtolower($_SERVER['REQUEST_METHOD']);
  }

  /**
   * @return Request
   */
  public static function getRequest()
  {
    if(empty(self::$request)) {
      $rawBody = file_get_contents('php://input');
      if (!empty($rawBody)) {
        $body = json_decode($rawBody, TRUE);
        if (json_last_error() == JSON_ERROR_NONE) {
          if (!array_key_exists('method', $body)) {
            (new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, 'No method given.'))->send();
          }
        } else {
          (new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, 'Invalid JSON.'))->send();
        }
      } else {
        $body = $_REQUEST;
        if (!empty($body)) {
          if (!array_key_exists('method', $body)) {
            (new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, 'No method given.'))->send();
          }
          $method = $body['method'];
          unset($body['method']);
          $body = [
            'method' => $method,
            'params' => $body,
          ];
        } else {
          (new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, 'Request has no body.'))->send();
        }
      }
      $params = [];
      if (!empty($body['params'])) {
        $params = $body['params'];
      }
      self::$request = new Request($body['method'], $params);
    }
    return self::$request;
  }

}