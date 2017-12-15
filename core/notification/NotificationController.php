<?php

namespace core\notification;

use core\database\PortalDBController;
use core\rest\Response;

class NotificationController
{


  /**
   * @param $message
   * @param $subject
   * @param $receiver
   * @return Response
   */
  public static function sendNotification($message, $subject, $receiver) {
    $portalDBController = new PortalDBController();
    $user = $portalDBController->getUserDetails($receiver);
    $mailNotification = new MailNotification($message, $subject, $user['email']);
    return $mailNotification->send();
  }

}