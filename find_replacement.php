<?php
	$method = $_SERVER["REQUEST_METHOD"];
	if($method == "GET") {
/*
		$username = $_GET[0];
		$file_name = 'data/' . $username . '.json';
		echo $file_name;
		$json_string = file_get_contents($file_name);
		$json_array = json_decode($json_string, true);
		echo $json_array;
*/
		$myfile = $_GET['username'] . '.json';

		$dir = 'data';
		$data = array();
		$files = scandir($dir);

		foreach ($files as $file) {
			if (($file != $myfile) && (strstr($file, '.') == '.json')) {
				$data[] = $dir . '/' . $file;
			}
		}

		$jsonData = json_encode($data);
		echo($jsonData);
		exit;

	}
?>
