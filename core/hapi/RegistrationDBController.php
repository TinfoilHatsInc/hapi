<?php

namespace core\hapi;

class RegistrationDBController{

	private $dbusername = 'tinfoil';
	private $dbpw = '';
	private $host = 'localhost';
	private $dbname = 'CHUB_IDs';
	private $db;

	public function dbCheck($checkid){
		this->$db = new mysqli($host, $dbusername, $dbpw, $dbname);

		$checkid = filter_var($checkid, FILTER_SANITIZE_STRING);
		$stmt = $db->prepare("SELECT * from IDTable WHERE id = ?");
		$stmt->bind_param('s', $checkid);
		$result = $db->query($stmt);

		if ($result->num_rows > 0) {
    		echo "<table><tr><th>ID</th><th>User</th></tr>";
    		// output data of each row
    		while($row = $result->fetch_assoc()) {
        		echo "<tr><td>".$row["id"]."</td><td>".$row["user"]."</td></tr>";
    		}
    		echo "</table>";
		} 
		else {
    		echo "0 results";
		}
		$db->close();
	}


}
