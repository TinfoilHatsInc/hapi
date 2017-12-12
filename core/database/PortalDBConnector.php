<?php

namespace core\database;

class PortalDBConnector extends DatabaseConnector
{

  public function getConfigName()
  {
    return 'portaldb';
  }
}