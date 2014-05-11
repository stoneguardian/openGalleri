<?php
	include 'preCheck.php';
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
		<script src="../js/test.js"></script>
	</head>
	<body>
		<div id="wrapper">
			<h1>Konfigurasjon av bildeGalleri</h1>
			<?php
				if($configWriteCode == 500){
					echo $configWriteMsg;
					echo '<br>';
					echo 'Fant ' . $osMsg . ' operativsystem';
				}else{
					include "form.php";
				}
			?>
		</div>
	</body>
</html>