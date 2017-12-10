<?php

namespace core\hapi;

use core\rest\Response;
use core\utils\ConfigReader;
use mysqli;
use core\rest\SuccessResponse;
use core\rest\ErrorResponse;

class RegistrationDBController
{
  
  private $db;

  /**
   * @param $checkid
   * @return Response
   */
  public function dbCheck($checkid)
  {

    $config = (new ConfigReader('registrationdb'))->requireConfig([
      'host',
      'dbusername',
      'dbpassword',
      'dbname',
    ]);

    $this->db = new mysqli(
      $config['host'],
      $config['dbusername'],
      $config['dbpassword'],
      $config['dbname']
    );

    $checkid = filter_var($checkid, FILTER_SANITIZE_STRING);
    $stmt = $this->db->prepare("SELECT * from IDTable WHERE deviceid = ?");
    $stmt->bind_param('s', $checkid);
    $stmt->execute();
    $stmtResult = $stmt->get_result();
    $result = [];
    while($row = $stmtResult->fetch_array(MYSQLI_ASSOC)) {
    	$result[] = $row;
    }
    $this->db->close();
    if (count($result) > 0) {
      return new SuccessResponse(SuccessResponse::HTTP_OK, $result);
    } else {
      return new ErrorResponse(ErrorResponse::HTTP_NOT_FOUND, $result);
    }
  }

}
