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

    if($this_account['ACCOUNTTYPE'] == false) {
        echo "Account not found";
    }
    else if($this_account['ACCOUNTTYPE'] == 'admin') {
        $result = array();
        // Get all professor accounts
        $query = oci_parse($conn, "SELECT username, firstname, lastname, email, dept1, dept2 FROM Account WHERE accounttype = 'prof'");
        oci_execute($query);
        while($row = oci_fetch_array($query)) {
            array_push($result, implode($row, chr(180)));
        }
        oci_free_statement($query);
        // Get all ta accounts
        $query = oci_parse($conn, "SELECT username, firstname, lastname, email, dept1, dept2 FROM Account WHERE accounttype = 'ta'");
        oci_execute($query);
        while($row = oci_fetch_array($query)) {
            array_push($result, implode($row, chr(180)));
        }
        oci_free_statement($query);
        echo implode($result, chr(181));
    }
    else if($this_account['ACCOUNTTYPE'] == 'prof') {
        $result = array();
        // Get all ta accounts with the same department
        $query = oci_parse($conn, "SELECT username, firstname, lastname, email, dept1, dept2 FROM Account WHERE accounttype = 'ta' AND (dept1 = :mydept1 OR dept1 = :mydept2 OR dept2 = :mydept1 OR dept2 = :mydept2)");
        oci_bind_by_name($query, ':mydept1', $this_account['DEPT1']);
        oci_bind_by_name($query, ':mydept2', $this_account['DEPT2']);
        oci_execute($query);
        while($row = oci_fetch_array($query)) {
            array_push($result, implode($row, chr(180)));
        }
        oci_free_statement($query);
        echo implode($result, chr(181));
    }
    else if($this_account['ACCOUNTTYPE'] == 'ta') {
        echo "TAs do not need to list accounts";
    }

    oci_close($conn);
    exit;
}

?>
