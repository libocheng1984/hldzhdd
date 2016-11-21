<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: 0");
header("Cache-Control: no-cache");
header("Pragma content: no-cache");
session_start();
session_commit();
?>
<?php
/**
 * 功能：终端上传图片信息
 * 	{result:"true或false",errmsg:"操作信息",records:[]}
 */
include_once ('../GlobalConfig.class.php');
include_once ('../class/TpmsDB.class.php');
include_once ('../class/CommandDB.class.php');
include_once ('../class/Event.class.php');





$year = date('Y');
$month = date('m');
$day = date('d');
$cjdbh = '0256';

	$base_path = GlobalConfig :: getInstance()->upload_src . 'picture/'; //接收文件目录  	
	$base_path_year = $base_path . $year . '/';
	$base_path_month = $base_path_year . $month . "/";
	$base_path_day = $base_path_month . $day . "/";
	$base_path_cjdbh = $base_path_day . $cjdbh . "/";
	//$target_path = $base_path_cjdbh . basename($_FILES['data']['name']);

	//上传源文件
	//检查文件路径是否存在，不存在则创建
	//if (!file_exists($base_path)) {
		echo $base_path;
		if (!mkdir($base_path, 0777, true)) {
			die('无法建立路径'.$base_path);
		}
	//}
	if (!file_exists($base_path_year)) {
		echo $base_path_year;
		if (!mkdir($base_path_year, 0777, true)) {
			die('无法建立路径');
		}
	}
	if (!file_exists($base_path_month)) {
		if (!mkdir($base_path_month, 0777, true)) {
			die('无法建立路径');
		}
	}

	if (!file_exists($base_path_day)) {
		if (!mkdir($base_path_day, 0777, true)) {
			die('无法建立路径');
		}
	}

	if (!file_exists($base_path_cjdbh)) {
		if (!mkdir($base_path_cjdbh, 0777, true)) {
			die('无法建立路径');
		}
	}


//类方法
?>