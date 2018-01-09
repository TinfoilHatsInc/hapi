<?php

namespace core\database;

use core\common\Singleton;
use core\exception\DatabaseException;
use core\utils\ConfigReader;
use mysqli;
use PHPMailer\PHPMailer\Exception;

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
    $socket = (new ConfigReader($this->getConfigName()))->requireConfig('socket', FALSE);
    $this->conn = new mysqli(
      $config['host'],
      $config['dbusername'],
      $config['dbpassword'],
      $config['dbname'],
      3306,
      $socket
    );
  }

  public abstract function getConfigName();

  /**
   * @param $query
   * @param array $params
   * @return mixed
   * @throws DatabaseException
   */
  private function executeSQLStatement($query, array $params)
  {
    $stmt = $this->conn->prepare($query);
    if (!$stmt) {
      if ($this->conn->error) {
        throw new DatabaseException($this->conn->error);
      } else {
        throw new DatabaseException('Database not configured.');
      }
    }

    $paramTypes = '';

    $paramValues = [];

    foreach ($params as $param) {
      $paramTypes .= $param->getType();
      $paramValues[] = $param->getValue();
    }

    $values = [
      & $paramTypes,
    ];

    for ($i = 0; $i < count($paramValues); $i++) {
      $values[] = &$paramValues[$i];
    }

    call_user_func_array(array($stmt, 'bind_param'), $values);

    $stmt->execute();
    $stmtResult = $stmt->get_result();

    if (!$stmtResult) {
      if (!empty($stmt->error)) {
        return FALSE;
      } else {
        return $this->conn->affected_rows;
      }
    }

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

  /**
   * @return mixed
   */
  public function getLastInsertId()
  {
    return $this->lastInsertID;
  }

}