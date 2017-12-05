<?php

namespace core\hapi;

use core\utils\ConfigReader;
use mysqli;

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
    $stmt = $this->db->prepare("SELECT * from IDTable WHERE id = ?");
    $stmt->bind_param('s', $checkid);
    $this->result = $stmt->execute();#$this->db->query($stmt);
    $this->db->close();
  }

  public function print(){
  	if ($this->result->num_rows > 0) {
      echo "<table><tr><th>ID</th><th>User</th></tr>";
      // output data of each row
      while ($row = $this->result->fetch_assoc()) {
        echo "<tr><td>" . $row["id"] . "</td><td>" . $row["user"] . "</td></tr>";
      }
      echo "</table>";
    } else {
      echo "0 results";
    }
  }


}
