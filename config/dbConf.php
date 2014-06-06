<?php
	//- Connection properties --------------//
	$dbServer = "";     		//IP or hostname to MySQL-server
	$db = "";                   //Which database
	$dbUsername = "";           //Username
	$dbPassword = "";           //Password

	//- Table-names ------------------------//
	$prefix = "b_";

	// Current names
	// to change names, change the value on the right
	$tblName = array('albm' => $prefix.'album',
					 'pict' => $prefix.'pictures',
					 'user' => $prefix.'users',
					 'acce' => $prefix.'albumAccess',
					 'code' => $prefix.'code');

	//- Backup ------------------------------//
	//Old prefix
   	$old_prefix = "b_";

    //Old names
    $old_album = $old_prefix . "album";
    $old_pictures = $old_prefix . "pictures";
    $old_users = $old_prefix . "users";
    $old_tblAlbumAccess = $old_prefix . "albumAccess";
	$old_tblCode = $old_prefix . "code";
		