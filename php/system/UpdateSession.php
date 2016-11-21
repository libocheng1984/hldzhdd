<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: 0");
header("Cache-Control: no-cache");
header("Pragma content: no-cache");
session_start();

?>
<?php

	//登陆信息写入session
	$_SESSION["isLayout"] = "1";
	session_commit();
		

?>