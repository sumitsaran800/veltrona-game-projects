<?php
	$conn = mysqli_connect('localhost', 'onorc_92go', 'onorc_92go', 'onorc_92go');
	
	if (!$conn) {
		echo "Error: " . mysqli_connect_error();
		exit();
	}
	
	date_default_timezone_set("Asia/Kolkata"); 
?>