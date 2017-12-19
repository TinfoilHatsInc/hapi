<?php

namespace core\utils;

use core\database\PortalDBController;
use core\database\RegistrationDBController;
use TinfoilHMAC\Util\SharedKey;

class HAPISharedKey extends SharedKey
{

  private $chubId;
  private $registrationDBController;
  private $portalDBController;
  private $sharedKey;

  public function __construct(array $chub)
  {
    $this->chubId = $chub['chubId'];
    $this->registrationDBController = new RegistrationDBController();
    $this->portalDBController = new PortalDBController();
    if (!empty($chub['key']) && !empty($chub['email']) && !empty($chub['password'])
      && $this->portalDBController->checkUserCredentials($this->chubId, $chub['email'], $chub['password'])
      && $this->registrationDBController->saveChubSharedKey($this->chubId, $chub['key'])
    ) {
      $this->sharedKey = $chub['key'];
    }
  }

  public function getSharedKey()
  {
    if (empty($this->sharedKey)) {
      $chub = $this->registrationDBController->checkChubId($this->chubId);
      $this->sharedKey = $chub['devicekey'];
    }
    return $this->sharedKey;
  }

}