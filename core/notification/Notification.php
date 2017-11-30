<?php

namespace core\notification;

abstract class Notification
{

  /**
   * @var string
   */
  private $message;
  /**
   * @var string
   */
  private $subject;
  /**
   * @var string
   */
  private $receiver;

  /**
   * Notification constructor.
   * @param $message string
   * @param $subject string
   * @param $receiver string
   */
  public function __construct($message, $subject, $receiver)
  {
    $this->message = $message;
    $this->subject = $subject;
    $this->receiver = $receiver;
  }

  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }

  /**
   * @return string
   */
  public function getSubject()
  {
    return $this->subject;
  }

  /**
   * @return string
   */
  public function getReceiver()
  {
    return $this->receiver;
  }

  /**
   * @return bool
   */
  public abstract function send();

}