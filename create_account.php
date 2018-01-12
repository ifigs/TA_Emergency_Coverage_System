<?php
require "db_config.php";

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_POST['username'];
	$email = $_POST['email'];
	$perm_code = $_POST['code'];

	// Connect to database
	$conn = oci_connect($uname, $pword, $dsn);
	if (!$conn) {
		echo "Database connection failed";
		exit;
	}

	$query = oci_parse($conn, "SELECT TABLE_NAME from ALL_TABLES where TABLE_NAME = 'ACCOUNT'");
	oci_execute($query);
	$r = oci_fetch_array($query);
	if (!$r) {
		$q = oci_parse($conn, "
			CREATE TABLE Account (
			    username VARCHAR(50) PRIMARY KEY,
			    salt VARCHAR(20),
			    hashedpw VARCHAR(100),
			    firstname VARCHAR(20),
			    lastname VARCHAR(30),
			    accounttype VARCHAR(10),
			    phone VARCHAR(10),
			    email VARCHAR(100) UNIQUE,
			    dept1 VARCHAR(10),
			    dept2 VARCHAR(10)
			)
		");
		oci_execute($q);
		oci_free_statement($q);
	}
	oci_free_statement($query);

	$query = oci_parse($conn, "SELECT TABLE_NAME from TABS where TABLE_NAME = 'PERMISSION'");
	oci_execute($query);
	$r = oci_fetch_array($query);
	if (!$r) {
		$q = oci_parse($conn, "
			CREATE TABLE Permission (
				email VARCHAR(100) PRIMARY KEY,
				permcode VARCHAR(8),
				accounttype VARCHAR(10)
			)
		");
		oci_execute($q);
		oci_free_statement($q);
	}
	oci_free_statement($query);

	// Build and execute username query
	$query = oci_parse($conn, "SELECT username FROM Account WHERE username = :username");
	oci_bind_by_name($query, ':username', $username);

	oci_execute($query);

	$row = oci_fetch_array($query);

	if($row['USERNAME'] == $username) {
		echo "Username is taken";
		exit;
	}
	oci_free_statement($query);

	// Build and execute email query
	$query = oci_parse($conn, "SELECT email FROM Account WHERE email = :email");
	oci_bind_by_name($query, ':email', $email);
	oci_execute($query);

	$row = oci_fetch_array($query);

	if($row['EMAIL'] == $email) {
		echo "Email is taken";
		exit;
	}
	oci_free_statement($query);

	// Check if this user is the admin
	$accounttype = 'ta';
	$query = oci_parse($conn, "SELECT count(*) FROM Account");
	oci_execute($query);
	$row = oci_fetch_array($query);
	if($row['COUNT(*)'] == 0) {
		$accounttype = 'admin';
	}
	// Permission code checking
	else {
		$que = oci_parse($conn, "SELECT accounttype FROM Permission WHERE email = :email AND lower(permcode) = lower(:perm_code)");
		oci_bind_by_name($que, ':email', $email);
		oci_bind_by_name($que, ':perm_code', $perm_code);
		oci_execute($que);

		$row = oci_fetch_array($que);

		if($row == false) {
			echo "Invalid permission code";
			exit;
		}
		$accounttype = $row['ACCOUNTTYPE'];

		if ($accounttype == "ta") {
			$filename = "data/" . $username . ".json";
			$deps = array();
			$deps['departments'] = array(strtoupper($_POST['dept1']));
			if ($_POST['dept2'] != "") {
				$deps['departments'][1] = strtoupper($_POST['dept2']);
			}
			file_put_contents($filename, json_encode($deps));
		}
		oci_free_statement($que);
	}
	oci_free_statement($query);

	// Build and execute account creation query
	$query = oci_parse($conn, "
		INSERT INTO Account (username, salt, hashedpw, firstname, lastname, accounttype, phone, email, dept1, dept2)
		VALUES ( :username, :salt, :hashedpw, :first_name, :last_name, :accounttype, :phone_number, :email, :dept1, :dept2)
	");
	oci_bind_by_name($query, ':username', $_POST['username']);
	oci_bind_by_name($query, ':salt', $_POST['salt']);
	oci_bind_by_name($query, ':hashedpw', $_POST['hashedpw']);
	oci_bind_by_name($query, ':first_name', $_POST['first_name']);
	oci_bind_by_name($query, ':last_name', $_POST['last_name']);
	oci_bind_by_name($query, ':accounttype', $accounttype);
	oci_bind_by_name($query, ':phone_number', $_POST['phone_number']);
	oci_bind_by_name($query, ':email', $_POST['email']);
	oci_bind_by_name($query, ':dept1', $_POST['dept1']);
	oci_bind_by_name($query, ':dept2', $_POST['dept2']);

	oci_execute($query);
	echo "Account creation successful";
	oci_free_statement($query);
	oci_close($conn);
	exit;
}

?>
