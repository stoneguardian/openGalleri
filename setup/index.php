<?php
	//test skrivetilgang
	$path1 = "../config/testfile.txt";
	$path2 = "../album/testfile.txt";
	$errorMsg = "Ikke skrivetilgang!";

	if(!$testfile = fopen($path1, 'w') or !$testfile2 = fopen($path2, 'w')){
		$error = true;
	}else{
		fclose($testfile);
		unlink($path1);
		fclose($testfile2);
		unlink($path2);
	}
?>

<html lang="no">
	<head>
		<meta charset="utf-8">
		<title>Konfigurasjon</title>
		<link rel="stylesheet" href="../css/reset.css" type="text/css">
		<link rel="stylesheet" type="text/css" href="../css/setup.css">
		<link href='http://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet' type='text/css'>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
		<script src="../js/setup.js"></script>
	</head>
	<body>
		<div id="wrapper">
			<h1>Konfigurasjon av bildeGalleri</h1>
			<?php
				if($error == true){
					echo $errorMsg;
					echo "<br><h2>Auda..</h2>
							Jeg trenger skriverettigheter på mappene 'config' og 'album'";
				}else{
					include_once 'form.php';
				}
			?>
		</div>
	</body>
</html>