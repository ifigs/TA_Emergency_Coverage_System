<?php
// If the account credentials exist in the databse, returns the username. Otherwise, returns an error message
require "db_config.php";



$method = $_SERVER['REQUEST_METHOD'];

if($method = 'GET') {
    $username = $_GET['username'];
    $password = $_GET['password'];

    $fail_message = "Invalid username or password";

    // Connect to database
	$conn = oci_connect($uname, $pword, $dsn);
    if (!$conn) {
        echo "Database connection failed";
        exit;
    }

    // Build and execute login query
    $query = oci_parse($conn, "SELECT username, salt, hashedpw, accounttype FROM Account WHERE username = :username");
    oci_bind_by_name($query, ':username', $username);
    oci_execute($query);

    $fetched_account = oci_fetch_array($query);
    if($fetched_account == false)
    {
    	echo $fail_message;
    	exit;
    }
    else {

	    $db_salt = $fetched_account['SALT'];
	    $db_hashedpw = $fetched_account['HASHEDPW'];
	    //$hashedpw = md5($db_salt . $password);
		$hashedpw = $password;

	    //if(strcmp($hashedpw, $db_hashedpw) != 0) echo $fail_message;
	    //else echo "Login Succeeded.";


		$result = array($db_salt, $db_hashedpw, $fetched_account['ACCOUNTTYPE']);
		echo implode(',', $result);//
    }
    oci_free_statement($query);
	oci_close($conn);
    exit;
}

?>
