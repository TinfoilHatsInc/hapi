<?php

namespace core\database;

use core\exception\EntityNotFoundException;
use core\exception\InvalidRequestParamException;

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
   * @param $chubId
   * @return bool|\mysqli_result
   * @throws EntityNotFoundException
   * @throws InvalidRequestParamException
   */
  public function checkChubId($chubId)
  {
    if (strlen($chubId) != 20) {
      throw new InvalidRequestParamException('Malformed CHUB ID.');
    } else {
      $result = $this->databaseConnector->executeSQLSelectStatement(
        'SELECT * FROM IDTable WHERE deviceid = ? LIMIT 1',
        new QueryParam('s', $chubId));
      if (is_array($result) && count($result) == 1) {
        return $result[0];
      } else {
        throw new EntityNotFoundException('No CHUB found with given id.');
      }
    }
  }

}
