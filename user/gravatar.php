<?php

    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		require '../class/userClass.php';
        $url = genGravatarURL($_POST['mail']);
        $return = array('userimg' => $url);
        echo json_encode($return);
	    header("Content-Type: application/json", true);
    }