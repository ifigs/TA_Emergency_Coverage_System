<?php
	$method = $_SERVER["REQUEST_METHOD"];
	if ($method == "POST") {
		$username = $_POST['username'];
		$file_name = 'data/' . $username . '.json';
		$json_data = $_POST['jsondata'];
		file_put_contents($file_name, $json_data);
		exit;
	}
?>
