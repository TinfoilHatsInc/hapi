<?php

namespace core\alarm;

use core\database\RegistrationDBController;

class AlarmController
{

  /**
   * @var RegistrationDBController
   */
  private $registrationDBController;

  public function __construct()
  {
    $this->registrationDBController = new RegistrationDBController();
  }

  public function alarmTriggered($chubId, $triggerName, array $snapshots)
  {
    $chub = $this->registrationDBController->checkChubId($chubId);

  }

}