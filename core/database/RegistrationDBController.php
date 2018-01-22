<?php

namespace core\database;

use core\exception\EntityNotFoundException;
use core\exception\InvalidRequestParamException;

class RegistrationDBController extends DatabaseController
{

  public function __construct()
  {
    parent::__construct(RegistrationDBConnector::getInstance());
  }

  /**
   * @param $chubId
   * @return bool|\mysqli_result
   * @throws EntityNotFoundException
   * @throws InvalidRequestParamException
   */
  public function checkChubId($chubId)
  {
    if (strlen($chubId) != 36) {
      throw new InvalidRequestParamException('Malformed CHUB ID.');
    } else {
      $result = $this->getDatabaseConnector()->executeSQLSelectStatement(
        'SELECT * FROM IDTable WHERE deviceid = ? LIMIT 1',
        new QueryParam('s', $chubId));
      if (is_array($result) && count($result) == 1) {
        return TRUE;
      } else {
        throw new EntityNotFoundException('No CHUB found with given id.');
      }
    }
  }

  public function getChubDetails($chubId)
  {
    if (strlen($chubId) != 36) {
      throw new InvalidRequestParamException('Malformed CHUB ID.');
    } else {
      $result = $this->getDatabaseConnector()->executeSQLSelectStatement(
        'SELECT * FROM IDTable WHERE deviceid = ? LIMIT 1',
        new QueryParam('s', $chubId));
      if (is_array($result) && count($result) == 1) {
        return $result[0];
      } else {
        throw new EntityNotFoundException('No CHUB found with given id.');
      }
    }
  }

  public function saveChubSharedKey($chubId, $sharedKey)
  {
    return (bool)$this->getDatabaseConnector()->executeSQLUpdateStatement(
      'UPDATE IDTable SET devicekey = ? WHERE deviceid = ?',
      new QueryParam(QueryParam::TYPE_STRING, $sharedKey),
      new QueryParam(QueryParam::TYPE_STRING, $chubId)
    );
  }

}
