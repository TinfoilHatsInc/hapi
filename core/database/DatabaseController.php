<?php

namespace core\database;

abstract class DatabaseController
{

  /**
   * @var DatabaseConnector
   */
  private $databaseConnector;

  public function __construct(DatabaseConnector $databaseConnector)
  {
    $this->databaseConnector = $databaseConnector;
  }

  /**
   * @return DatabaseConnector
   */
  protected function getDatabaseConnector()
  {
    return $this->databaseConnector;
  }

}