<?php

namespace core\database;

class RegistrationDBConnector extends DatabaseConnector
{

  public function getConfigName()
  {
    return 'registrationdb';
  }
}