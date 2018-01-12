<?php
//here we will access the data base to show all the TAs
require "db_config.php";


$method = $_SERVER['REQUEST_METHOD'];

if($method = 'GET') {
    $fail_message = "No TAs in Database.";

    // Connect to database
    $conn = oci_connect($uname, $pword, $dsn);
    if (!$conn) {
        echo "Database connection failed";
        exit;
    }

    // Build and execute login query
    $query = oci_parse($conn, "SELECT firstname, lastname FROM Account");
    oci_execute($query);

    $num_rows = oci_fetch_all($query, $fetched_TAs, null, OCI_FETCHSTATEMENT_BY_ROW, OCI_NUM);
    //print $fetched_TAs;

    $all_TAs = " ";
    for ($i = 0; $i < $num_rows; $i++){
          $TA = $fetched_TAs[0][$i] . " " . $fetched_TAs[1][$i];
          $all_TAs = $all_TAs . $TA . ",  ";
    }


    if($fetched_TAs == false) echo $fail_message;
    else echo $all_TAs;

    oci_free_statement($query);
	   oci_close($conn);
    exit;
}

?>
