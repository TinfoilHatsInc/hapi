<?php

namespace core\registration;

use core\database\RegistrationDBController;
use core\rest\ErrorResponse;
use core\rest\Response;

class RegistrationController
{

  /**
   * @param $chubid string
   * @return Response
   */
  public function chubRegistration($chubid)
{
  if (strlen($chubid) != 20) {
    return new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, 'Malformed CHUB ID.');
  } else {
    $db = new RegistrationDBController;
    return $db->dbCheck($chubid);
  }
}

}