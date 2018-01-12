<?php

namespace core\deadmodule;

use core\database\PortalDBController;
use core\notification\NotificationController;
use core\rest\SuccessResponse;

class DeadModuleController
{

  private $portalDBController;

  public function __construct()
  {
    $this->portalDBController = new PortalDBController();
  }

  public function notifyDeadModules($deadModules, $chubId) {
    $this->portalDBController->saveDeadModules($deadModules, $chubId);
    NotificationController::sendNotification(
      'The following modules are not responding: ' . implode(', ', $deadModules) . '.',
      'Dead modules',
      $chubId);
    return new SuccessResponse(SuccessResponse::HTTP_CREATED, 'All dead modules are saved.');
  }

}