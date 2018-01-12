<?php

require "db_config.php";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $account_type = $_POST['account_type'];
    $invitee_email = $_POST['email'];
    //changed the info we get



    // Connect to database
	$conn = oci_connect($uname, $pword, $dsn);
	if (!$conn) {
		echo "Database connection failed";
		exit;
	}

    //this needs to change - can't get account type before you create an account - need to get account type from the javascript code i have
    // Get the inviter's account type to determine the invitee's account type
/*
    $query = oci_parse($conn, "SELECT accounttype from Account WHERE username = :username");
    oci_bind_by_name($query, ':username', $username);
    oci_execute($query);
    $row = oci_fetch_array($query);
    if($row == false) {
        echo "Username not found";
        exit;
    }
    $inviter_account_type = $row['ACCOUNTTYPE'];
    $invitee_account_type = '';
    if($inviter_account_type == 'admin') $invitee_account_type = 'prof';
    else if($inviter_account_type == 'prof') $invitee_account_type = 'ta';
    else if($inviter_account_type == 'ta') {
        echo "TAs cannot invite users";
        exit;
    }
    oci_free_statement($query);
*/
    // Generate the permission code
    $perm_code = generateRandomString(6);

    // Check if email was already invited
    $already_invited = true;
    $query = oci_parse($conn, "SELECT email FROM Permission WHERE email = :email");
    oci_bind_by_name($query, ':email', $invitee_email);
    oci_execute($query);
    $row = oci_fetch_array($query);
    if($row == false) $already_invited = false;
    oci_free_statement($query);

    if($already_invited) {
        // Update the email with a new permission code
        $query = oci_parse($conn, "UPDATE Permission SET permcode = :permcode WHERE email = :email");
        oci_bind_by_name($query, ':email', $invitee_email);
        oci_bind_by_name($query, ':permcode', $perm_code);
        oci_execute($query);
        oci_free_statement($query);
    }
    else {
        // Insert new permission code record
        $query = oci_parse($conn, "INSERT INTO Permission(email, permcode, accounttype) VALUES(:email, :permcode, :accounttype)");
        oci_bind_by_name($query, ':email', $invitee_email);
        oci_bind_by_name($query, ':permcode', $perm_code);
        oci_bind_by_name($query, ':accounttype', $account_type);
        oci_execute($query);
        oci_free_statement($query);
    }

    echo $perm_code;
    exit;

}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>
