<?php
	$method = $_SERVER["REQUEST_METHOD"];
	if ($method == "POST") {
		$username = $_POST['username'];
/*
		if (!file_exists('data')) {
			mkdir('data', 0700, true);
		}
*/
		$file_name = 'data/' . $username . '.json';
		$new_event = array(json_decode($_POST['jsondata'], true));
		$json_string = file_get_contents($file_name);
		$json_array = array();
		$date = $new_event[0]['date'];
		if ($json_string == false) {
			$json_array[$date] = $new_event;
		} else {
			$json_array = json_decode($json_string, true);
			if (array_key_exists($date, $json_array)) {
				$json_array[$date] = array_merge($json_array[$date], $new_event);
			} else {
				$json_array[$date] = $new_event;
			}
		}
		$updated_json_string = json_encode($json_array);
		file_put_contents($file_name, $updated_json_string);
		exit;
	}
?>
