<?php


class EmployeeData
{
	
	public function connect(){
		$this->servername = "localhost";
		$this->dbuser = "root";
		$this->dbpass = "";
		$this->dbname = "employee";
		$conn = new mysqli($this->servername,$this->dbuser,$this->dbpass,$this->dbname);
		return $conn;
	}
}


?>