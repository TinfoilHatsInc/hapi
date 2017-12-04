<?php

namespace core\hapi;

use mysqli;
use Symfony\Component\Yaml\Yaml;

class RegistrationDBController
{
  
  private $db;

  public function dbCheck($checkid)
  {

    $config = Yaml::parse(file_get_contents(__DIR__ . '/../../config/registrationdb.config.yml'));

    $this->db = new mysqli($config['host'], $config['dbusername'], $config['dbpassword'], $config['dbname']);

    $checkid = filter_var($checkid, FILTER_SANITIZE_STRING);
    $stmt = $this->db->prepare("SELECT * from IDTable WHERE id = ?");
    $stmt->bind_param('s', $checkid);
    $result = $this->db->query($stmt);

    if ($result->num_rows > 0) {
      echo "<table><tr><th>ID</th><th>User</th></tr>";
      // output data of each row
      while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["id"] . "</td><td>" . $row["user"] . "</td></tr>";
      }
      echo "</table>";
    } else {
      echo "0 results";
    }
    $this->db->close();
  }


}
