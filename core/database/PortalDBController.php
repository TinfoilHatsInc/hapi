<?php

namespace core\database;

use PHPMailer\PHPMailer\Exception;

class PortalDBController extends DatabaseController
{

  public function __construct()
  {
    parent::__construct(PortalDBConnector::getInstance());
  }

  /**
   * @param $chubId
   * @param $triggerName
   * @return mixed
   * @throws Exception
   */
  public function saveNotification($chubId, $triggerName)
  {

    $result = $this->getDatabaseConnector()->executeSQLInsertStatement(
      'INSERT INTO notification (chubid, triggername) VALUES (?, ?);',
      new QueryParam(QueryParam::TYPE_INTEGER, $chubId),
      new QueryParam(QueryParam::TYPE_STRING, $triggerName)
    );

    if ($result) {
      return $this->getDatabaseConnector()->getLastInsertId();
    } else {
      throw new Exception('Unknown database error.');
    }

  }

  /**
   * @param $notificationId
   * @param $filePath
   * @return bool
   */
  public function saveSnapshot($notificationId, $filePath)
  {

    $result = $this->getDatabaseConnector()->executeSQLInsertStatement(
      'INSERT INTO snapshot (notificationid, filepath) VALUES (?, ?)',
      new QueryParam(QueryParam::TYPE_INTEGER, $notificationId),
      new QueryParam(QueryParam::TYPE_STRING, $filePath)
    );

    return (bool) $result;

  }

  public function getUserDetails($userId) {
    $result = $this->getDatabaseConnector()->executeSQLSelectStatement(
      'SELECT * FROM user WHERE userid = ?',
      new QueryParam(QueryParam::TYPE_INTEGER, $userId)
    );
    return $result;
  }

}