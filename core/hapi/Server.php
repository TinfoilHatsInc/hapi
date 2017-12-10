<?php

namespace core\hapi;

use core\rest\ErrorResponse;
use core\rest\Response;

class Server
{
  /**
   * @param $chubid
   * @return Response
   */
  public function chubRegistration($chubid)
  {
    if (strlen($chubid) > 20 || strlen($chubid) < 20) {
      return new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, 'Malformed CHUB ID');
    } else {
      $db = new RegistrationDBController;
      return $db->dbCheck($chubid);
    }
  }


}
