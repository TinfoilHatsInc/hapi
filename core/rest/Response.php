<?php

namespace core\rest;

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
    http_response_code($this->code);
    echo json_encode($this->getBody());
    exit;
  }

}