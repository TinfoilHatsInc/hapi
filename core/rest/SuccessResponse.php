<?php

namespace core\rest;

class SuccessResponse extends Response
{

  const HTTP_OK = '200 OK';
  const HTTP_CREATED = '201 Created';

  public function getBody()
  {
    return [
      'message' => $this->getResponse(),
    ];
  }

}