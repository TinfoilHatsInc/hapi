<?php

namespace core\rest;

class ErrorResponse extends Response
{

  const HTTP_BAD_REQUEST = '400 Bad Request';
  const HTTP_FORBIDDEN = '403 Forbidden';
  const HTTP_NOT_FOUND = '404 Not Found';
  const HTTP_METHOD_NOT_ALLOWED = '405 Method Not Allowed';
  const HTTP_INTERNAL_SERVER_ERROR = '500 Internal Server Error';

  protected function getBody()
  {
    return [
      'error' => TRUE,
      'message' => $this->getResponse(),
    ];
  }

}