<?php

namespace core\rest;

class ErrorResponse extends Response
{

  const HTTP_BAD_REQUEST = 400;
  const HTTP_FORBIDDEN = 403;
  const HTTP_NOT_FOUND = 404;
  const HTTP_METHOD_NOT_ALLOWED = 405;
  const HTTP_INTERNAL_SERVER_ERROR = 500;

  protected function getBody()
  {
    return [
      'error' => TRUE,
      'message' => $this->getResponse(),
    ];
  }

}