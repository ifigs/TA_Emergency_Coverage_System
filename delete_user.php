<?php
// If the account credentials exist in the databse, returns the username. Otherwise, returns an error message
require "db_config.php";

$method = $_SERVER['REQUEST_METHOD'];

if($method = 'POST') {
    $username = $_POST['username'];
	echo $method;

    // Connect to database
	$conn = oci_connect($uname, $pword, $dsn);
    if (!$conn) {
        echo "Database connection failed";
        exit;
    }

    // Build and execute deletion query
    $query = oci_parse($conn, "DELETE FROM Account WHERE username = :username");
    oci_bind_by_name($query, ':username', $username);
    oci_execute($query);

    oci_free_statement($query);
	oci_close($conn);

    // Delete the user's events
    $file_name = 'data/' . $username . '.json';
    unlink($file_name);

    exit;
}

?>
