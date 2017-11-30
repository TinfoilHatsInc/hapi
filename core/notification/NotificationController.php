<?php

namespace core\notification;

class NotificationController
{

  public static function sendNotification($message, $subject, $receiver) {
    $mailNotification = new MailNotification($message, $subject, $receiver);
    $mailNotification->send();
  }

}