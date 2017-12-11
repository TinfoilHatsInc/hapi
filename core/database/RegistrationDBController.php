<?php

namespace core\database;

use core\rest\ErrorResponse;
use core\rest\Response;
use core\rest\SuccessResponse;

class RegistrationDBController
{

  /**
   * @var DatabaseConnector
   */
  private $databaseConnector;

  public function __construct()
  {
    $this->databaseConnector = RegistrationDBConnector::getInstance();
  }

  /**
   * @param $checkid
   * @return Response
   */
  public function dbCheck($checkid)
  {
    $result = $this->databaseConnector->executeSQLSelectStatement(
      'SELECT * from IDTable WHERE deviceid = ?',
      new QueryParam('s', $checkid));
    if (is_array($result) && count($result) > 0) {
      return new SuccessResponse(SuccessResponse::HTTP_OK, $result);
    } else {
      return new ErrorResponse(ErrorResponse::HTTP_NOT_FOUND, 'No CHUB found with given id.');
    }
  }

}
