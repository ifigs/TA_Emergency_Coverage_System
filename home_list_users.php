<?php
//here we will access the data base to show all the TAs
require "db_config.php";


$method = $_SERVER['REQUEST_METHOD'];

if($method = 'GET') {
    $fail_message = "No TAs in Database.";

    $username = $_GET['username'];

    // Connect to database
    $conn = oci_connect($uname, $pword, $dsn);
    if (!$conn) {
        echo "Database connection failed";
        exit;
    }

    // Check what account type this user is
    $query = oci_parse($conn, "SELECT accounttype, dept1, dept2 FROM Account WHERE username = :username");
    oci_bind_by_name($query, ':username', $username);
    oci_execute($query);

    $this_account = oci_fetch_array($query);
    oci_free_statement($query);

    if($this_account == false) {
        echo "Account not found";
    }
    else if($this_account['ACCOUNTTYPE'] == 'admin') {
        $result = "";
        // Get all professor accounts
        $query = oci_parse($conn, "SELECT username, firstname, lastname, email, accounttype, dept1, dept2 FROM Account WHERE accounttype = 'prof'");
        oci_execute($query);

	//the default for oci_fetch_array is that it gets both the associative array and the numeric array. need to get only the numeric so it doesn't get both!!
        while($row = oci_fetch_array($query, OCI_NUM)) {
	    //$row_str = implode(chr(180), $row);
	    $row_str = implode("'", $row);
            //$result = $result . $row_str . chr(181);
            $result = $result . $row_str . "~";
        }
        oci_free_statement($query);
        // Get all ta accounts
        $query = oci_parse($conn, "SELECT username, firstname, lastname, email, accounttype, dept1, dept2 FROM Account WHERE accounttype = 'ta'");
        oci_execute($query);
        while($row = oci_fetch_array($query, OCI_NUM)) {
	    //$row_str = implode(chr(180), $row);
	    $row_str = implode("'", $row);
            //$result = $result . $row_str . chr(181);
            $result = $result . $row_str . "~";
        }
        oci_free_statement($query);
        echo $result;
    }
    else if($this_account['ACCOUNTTYPE'] == 'prof') {
        $result = "";
	// Get all ta accounts
        $query = oci_parse($conn, "SELECT username, firstname, lastname, email, accounttype, dept1, dept2 FROM Account WHERE accounttype = 'ta'");
        oci_execute($query);
        while($row = oci_fetch_array($query, OCI_NUM)) {
            //$row_str = implode(chr(180), $row);
            $row_str = implode("'", $row);
            //$result = $result . $row_str . chr(181);
            $result = $result . $row_str . "~";
        }
        oci_free_statement($query);
        echo $result;

    }
    else if($this_account['ACCOUNTTYPE'] == 'ta') {
        echo "TAs do not need to list accounts";
    }

    oci_close($conn);
    exit;
}

?>
