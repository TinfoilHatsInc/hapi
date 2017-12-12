<?php

namespace core\rest;

use TinfoilHMAC\API\SecureAPIResponse;

abstract class Response
{

  private $code;
  private $response;

  public function __construct($code, $response)
  {
    $this->code = $code;
    $this->response = $response;
  }

  protected abstract function getBody();

  protected function getResponse() {
    return $this->response;
  }

  public function send() {
    $response = new SecureAPIResponse($this->code, $this->getBody());
    $response->send();
  }

}