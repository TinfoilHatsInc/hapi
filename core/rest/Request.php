<?php

namespace core\rest;

class Request
{

  /**
   * @var string
   */
  private $method;
  /**
   * @var array
   */
  private $params;

  /**
   * Request constructor.
   * @param $method
   * @param array $params
   */
  public function __construct($method, array $params = [])
  {
    $this->method = $method;
    $this->params = $params;
  }

  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }

  /**
   * @return array
   */
  public function getParams()
  {
    return $this->params;
  }

}