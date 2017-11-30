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
    header('HTTP/1.0 ' . $this->code);
    echo json_encode($this->getBody());
    exit;
  }

}