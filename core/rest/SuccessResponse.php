<?php

namespace core\rest;

class SuccessResponse extends Response
{

  const HTTP_OK = 200;
  const HTTP_CREATED = 201;

  public function getBody()
  {
    return [
      'message' => $this->getResponse(),
    ];
  }

}