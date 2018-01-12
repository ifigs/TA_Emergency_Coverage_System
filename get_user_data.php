<?php
// If the account credentials exist in the databse, returns the username. Otherwise, returns an error message
require "db_config.php";

$method = $_SERVER['REQUEST_METHOD'];

if($method = 'GET') {
    $data = json_decode($_GET['data']);

    // Connect to database
	$conn = oci_connect($uname, $pword, $dsn);
    if (!$conn) {
        echo "Database connection failed";
        exit;
    }

    // Get the contact information for each user
    $contact_info = array();
    foreach($data as $username) {
        $query = oci_parse($conn, "SELECT first_name, last_name, email, phone FROM Account WHERE username = :username");
        oci_bind_by_name($query, ':username', $username);
        oci_execute($query);

        $row = oci_fetch_array($query);
        $contact_info[$username] = $row;

        oci_free_statement($query);
    }
	oci_close($conn);

    echo json_encode($contact_info);
    exit;
}

?>
