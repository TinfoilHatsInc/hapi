<?php

namespace core\notification;

use core\database\PortalDBController;
use core\rest\Response;

class NotificationController
{


  /**
   * @param string $message
   * @param string $subject
   * @param string $chubId
   * @return Response
   */
  public static function sendNotification($message, $subject, $chubId) {
    $portalDBController = new PortalDBController();
    $user = $portalDBController->getUserByChub($chubId);
    $mailNotification = new MailNotification($message, $subject, $user['email']);
    return $mailNotification->send();
  }

}