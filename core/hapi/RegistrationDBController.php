<?php

namespace core\hapi;

use core\utils\ConfigReader;
use mysqli;
use core\rest\SuccessResponse;
use core\rest\ErrorResponse;

class RegistrationDBController
{
  
  private $db;
  private $result;

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
    $this->result = [];
    while($row = $stmtResult->fetch_array(MYSQLI_ASSOC)) {
    	$this->result[] = $row;
    }
    $this->db->close();
  }

  public function print(){
  	if (count($this->result) > 0) {
  	  (new SuccessResponse(SuccessResponse::HTTP_OK, $this->result))->send();
      echo "<table><tr><th>ID</th><th>User</th></tr>";
      // output data of each row
      foreach ($this->result as $row) {
        echo "<tr><td>" . $row["deviceid"] . "</td><td>" . $row["user"] . "</td></tr>";
      }
      echo "</table>";
    } else {
      (new ErrorResponse(ErrorResponse::HTTP_NOT_FOUND, $this->result))->send();
      echo "0 results";
    }
  }


}
