<?php
require "db_config.php";

if($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = preg_replace('/\s+/', ' ', $_POST['username']);
	$fname = preg_replace('/\s+/', ' ',$_POST['first_name']);
	$lname = preg_replace('/\s+/', ' ',$_POST['last_name']);
	$phone = preg_replace('/\s+/', ' ',$_POST['phone_number']);
	$email = preg_replace('/\s+/', ' ',$_POST['email']);
	$password = preg_replace('/\s+/', ' ',$_POST['password']);

        // Connect to database
        $conn = oci_connect($uname, $pword, $dsn);
        if (!$conn) {
                echo "Database connection failed";
                exit;
        }

	if($_POST['first_name'] != "") {
		// Build and execute username query
		$update = oci_parse($conn, "UPDATE Account SET firstname = :fname WHERE username = :username");
		oci_bind_by_name($update, ':username', $username);
		oci_bind_by_name($update, ':fname', $fname);

		oci_execute($update);
		oci_free_statement($update);

	}
	if($_POST['last_name'] != "") {
		// Build and execute username query
		$update = oci_parse($conn, "UPDATE Account SET lastname = :lname WHERE username = :username");
		oci_bind_by_name($update, ':username', $username);
		oci_bind_by_name($update, ':lname', $lname);

		oci_execute($update);
		oci_free_statement($update);

	}
	if($_POST['password'] != "") {
		// Build and execute username query
		$update = oci_parse($conn, "UPDATE Account SET password = :password WHERE username = :username");
		oci_bind_by_name($update, ':username', $username);
		oci_bind_by_name($update, ':password', $password);

		oci_execute($update);
		oci_free_statement($update);

	}
	if($phone != "") {
		// Build and execute username query
		$update = oci_parse($conn, "UPDATE Account SET phone = :phone WHERE username = :username");
		oci_bind_by_name($update, ':username', $username);
		oci_bind_by_name($update, ':phone', $phone);

		oci_execute($update);
		oci_free_statement($update);
	}
	if($email != "") {
		// Build and execute username query
		$update = oci_parse($conn, "UPDATE Account SET email = :email WHERE username = :username");
		oci_bind_by_name($update, ':username', $username);
		oci_bind_by_name($update, ':email', $email);

		oci_execute($update);
		oci_free_statement($update);
	}


        oci_close($conn);
	echo "Edit Successful";
        exit;
}

?>
