<?php

namespace core\rest;

class RequestController
{

  /**
   * @return string
   */
  public static function getHTTPRequestMethod()
  {
    return strtolower($_SERVER['REQUEST_METHOD']);
  }

}