<?php
	//Check write permission on config folder
	$configFolder = "../config/testfile.txt";
	if(!$testfile = fopen($configFolder, 'w')){
		$ConfigWriteCode = 500;
		$configWriteMsg = "No permission";
	}else{
		fclose($testfile);
		unlink($configFolder);
		$ConfigWriteCode = 200;
		$configWriteMsg = "Write permission on config folder";
	}

	//Check OS
	$OS = php_uname('s');
	$osCode = 200;
	if($OS == 'Windows NT'){
		$osMsg = 'Windows';
	}else{
		$osMsg = 'Linux';
	}

	if($ConfigWriteCode == 200){
		$siteconfig = '<?php
	$serverOS = "' . $osMsg . '";
	';

		$saveOS = fopen('../config/siteConf.php', 'w');
		fwrite($saveOS, $siteconfig);
	}
