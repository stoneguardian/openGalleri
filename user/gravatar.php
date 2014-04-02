<?php
/**
 * Created by PhpStorm.
 * User: Hallvard
 * Date: 09.03.14
 * Time: 14:33
 */
    function genGravatarURL($mail, $size = '80', $default = 'mm'){
        $md5 = md5( strtolower( trim( $mail ) ) );
	    return "http://www.gravatar.com/avatar/$md5?s=$size&d=$default";
    }

    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $url = genGravatarURL($_POST['mail']);
        $return = array('userimg' => $url);
        echo json_encode($return);
	    header("Content-Type: application/json", true);
    }