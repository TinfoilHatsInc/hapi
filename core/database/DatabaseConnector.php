<?php

namespace core\database;

use core\common\Singleton;
use core\utils\ConfigReader;
use mysqli;

abstract class DatabaseConnector extends Singleton
{

  /**
   * @var \mysqli
   */
  private $conn;
  /**
   * @var mixed
   */
  private $lastInsertID;

  public function __construct()
  {
    $config = (new ConfigReader($this->getConfigName()))->requireConfig([
      'host',
      'dbusername',
      'dbpassword',
      'dbname',
    ]);
    $this->conn = new mysqli(
      $config['host'],
      $config['dbusername'],
      $config['dbpassword'],
      $config['dbname']
    );
  }

  public abstract function getConfigName();

  /**
   * @param $query
   * @param QueryParam[] $params
   * @return bool|\mysqli_result
   */
  private function executeSQLStatement($query, array $params) {
    $stmt = $this->conn->prepare($query);
    foreach($params as $param) {
      $paramType = $param->getType();
      $paramValue = $param->getValue();
      $stmt->bind_param($paramType, $paramValue);
    }
    $stmt->execute();
    $stmtResult = $stmt->get_result();
    return $stmtResult->fetch_all(MYSQLI_ASSOC);
  }

  /**
   * @param $query
   * @param QueryParam[] ...$params
   * @return bool|\mysqli_result
   */
  public function executeSQLSelectStatement($query, ...$params)
  {
    return $this->executeSQLStatement($query, $params);
  }

  /**
   * @param $query
   * @param QueryParam[] ...$params
   * @return bool|\mysqli_result
   */
  public function executeSQLInsertStatement($query, ...$params)
  {
    $this->lastInsertID = NULL;

    $result = $this->executeSQLStatement($query, $params);

    $this->lastInsertID = mysqli_insert_id($this->conn);

    return $result;

  }

  /**
   * @param $query
   * @param QueryParam[] ...$params
   * @return bool|\mysqli_result
   */
  public function executeSQLUpdateStatement($query, ...$params)
  {

    $result = $this->executeSQLStatement($query, $params);

    return $result;

  }

  /**
   * @param $query
   * @param QueryParam[] ...$params
   * @return bool|int|\mysqli_result
   */
  public function executeSQLDeleteStatement($query, ...$params)
  {

    $result = $this->executeSQLStatement($query, $params);

    if ($result) {
      $result = $this->conn->affected_rows;
    }

    return $result;

  }

}