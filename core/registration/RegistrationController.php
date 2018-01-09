<?php

namespace core\registration;

use core\database\RegistrationDBController;
use core\rest\Response;
use core\rest\SuccessResponse;

class RegistrationController
{

  /**
   * @param $chubId string
   * @return Response
   */
  public function chubRegistration($chubId)
  {
    $db = new RegistrationDBController;
    return new SuccessResponse(SuccessResponse::HTTP_OK, $db->checkChubId($chubId));
  }

  /**
   * @return SuccessResponse
   */
  public function incomingRegistration() {
    return new SuccessResponse(SuccessResponse::HTTP_OK, 'Initial registration done.');
  }

}