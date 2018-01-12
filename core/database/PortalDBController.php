<?php

namespace core\database;

use core\exception\DatabaseException;
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

  /**
   * @param $userId
   * @return bool|\mysqli_result
   */
  public function getUserDetails($userId) {
    $result = $this->getDatabaseConnector()->executeSQLSelectStatement(
      'SELECT * FROM user WHERE userid = ?',
      new QueryParam(QueryParam::TYPE_INTEGER, $userId)
    );
    return $result;
  }

  /**
   * @param $chubId string
   * @param $email string
   * @param $password string
   * @return bool
   */
  public function checkUserCredentials($chubId, $email, $password) {
    $result = $this->getDatabaseConnector()->executeSQLSelectStatement(
      'SELECT user.chub_hash FROM user, chub WHERE user.id = chub.user_id AND chub.id = ? 
      AND user.email = ? LIMIT 1',
      new QueryParam(QueryParam::TYPE_STRING, $chubId),
      new QueryParam(QueryParam::TYPE_STRING, $email)
    );
    if(!empty($result)) {
      $passwordHash = $result[0]['chub_hash'];
      return password_verify($password, $passwordHash);
    } else {
      return FALSE;
    }
  }

  public function alarmStatusUpdate($chubId, $status) {
    $result = $this->getDatabaseConnector()->executeSQLUpdateStatement(
      'UPDATE chub SET alarm_status = ? WHERE chub.id = ?',
      new QueryParam('s', $status),
      new QueryParam('s', $chubId)
    );
    return (bool) $result;
  }

  public function getUserByChub($chubId) {
    $result = $this->getDatabaseConnector()->executeSQLSelectStatement(
      'SELECT user.* FROM user, chub WHERE user.id = chub.user_id AND chub.id = ?',
      new QueryParam('s', $chubId)
    );
  }

}