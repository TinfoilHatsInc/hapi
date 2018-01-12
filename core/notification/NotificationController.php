<?php

namespace core\notification;

use core\database\PortalDBController;
use core\exception\EntityNotFoundException;
use core\rest\Response;

class NotificationController
{


  /**
   * @param string $message
   * @param string $subject
   * @param string $chubId
   * @return Response
   * @throws EntityNotFoundException
   */
  public static function sendNotification($message, $subject, $chubId) {
    $portalDBController = new PortalDBController();
    $user = $portalDBController->getUserByChub($chubId);
    if(!empty($user)) {
      $user = $user[0];
      $mailNotification = new MailNotification([
        'username' => $user['first_name'] . ' ' . $user['last_name'],
        'body' => $message,
      ], $subject, $user['email']);
      return $mailNotification->send();
    } else {
      throw new EntityNotFoundException('User not found.');
    }
  }

}