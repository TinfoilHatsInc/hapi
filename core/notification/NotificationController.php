<?php

namespace core\notification;

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
    $mailNotification = new MailNotification($message, $subject, $receiver);
    return $mailNotification->send();
  }

}