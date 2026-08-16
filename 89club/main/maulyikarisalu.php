<?php 
	ob_start();
	session_start();
	include("conn.php");
	
	$adid = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['username']));
	$psad = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['password']));
	$samasye = "SELECT * FROM `nirvahaka_shonu` WHERE `nirvahaka_hesaru`='".$adid."' AND `guptapada`='".md5($psad)."' AND `sthiti`='1'";
	$phalitansa = mysqli_query($conn,$samasye);
	$sankhye = mysqli_num_rows($phalitansa);
	$salu = mysqli_fetch_assoc($phalitansa);
	
	if($sankhye >= 1){
		$_SESSION['unohs'] = $salu['unohs'];
		$_SESSION['nirvahaka_hesaru'] = $salu['nirvahaka_hesaru'];
		echo '<script>window.location="dashboard.php";</script>';
		header("Location: dashboard.php", true, 302);
		exit;
	}
	else{
		echo '<script>alert("Invalid Username or Password!");window.location="index.php?err=true";</script>';
		header("Location: index.php?err=true", true, 302);
		exit;
	}
?>