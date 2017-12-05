<?php

namespace core\notification;

use core\rest\ErrorResponse;
use core\rest\SuccessResponse;
use core\utils\ConfigReader;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\Yaml\Yaml;
use Twig_Environment;
use Twig_Loader_Filesystem;

class MailNotification extends Notification
{

  /**
   * @var PHPMailer
   */
  private $mail;

  public function __construct($message, $subject, $receiver)
  {
    $this->mail = new PHPMailer(TRUE);
    parent::__construct($message, $subject, $receiver);
  }

  public function send()
  {

    $mailCredentials = (new ConfigReader('mail'))->requireConfig([
      'mail_host',
      'mail_username',
      'mail_password',
      'mail_secure',
      'mail_port',
    ]);

    //Enable SMTP debugging.
    $this->mail->SMTPDebug = 0;
    //Set PHPMailer to use SMTP.
    $this->mail->isSMTP();
    $this->mail->SMTPOptions = array('ssl' => array('verify_peer_name' => false));
    //Set SMTP host name
    $this->mail->Host = gethostbyname($mailCredentials['mail_host']);
    //Set this to true if SMTP host requires authentication to send email
    $this->mail->SMTPAuth = true;
    //Provide username and password
    $this->mail->Username = $mailCredentials['mail_username'];
    $this->mail->Password = $mailCredentials['mail_password'];
    //If SMTP requires TLS encryption then set it
    $this->mail->SMTPSecure = $mailCredentials['mail_secure'];
    //Set TCP port to connect to
    $this->mail->Port = $mailCredentials['mail_port'];

    $this->mail->setFrom($mailCredentials['mail_username'], 'Tinfoilhats Inc.');
    $this->mail->isHTML(true);
    $this->mail->SMTPKeepAlive = true;

    $loader = new Twig_Loader_Filesystem([__DIR__ . '/template']);
    $twig = new Twig_Environment($loader, array(
      'cache' => __DIR__ . '/cache',
    ));

    $twig->addExtension(new \Twig_Extension_StringLoader());

    $body = $twig->render('mail.twig', [
      'mailContent' => $this->getMessage(),
    ]);

    $this->mail->Subject = $this->getSubject();
    $this->mail->addAddress($this->getReceiver());
    $this->mail->Body = $body;
    try {
      if (!$this->mail->send()) {
        (new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, 'Mail could not be send.'))->send();
      } else {
        (new SuccessResponse(SuccessResponse::HTTP_OK, 'Notification send.'))->send();
      }
    } catch (Exception $e) {
      (new ErrorResponse(ErrorResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage()))->send();
    }

  }
}