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

/*获取参数*/

/*调试*/
if (isDebug()) {
}

//文件以时间戳 重命名     
date_default_timezone_set('PRC'); //中国时区
$datename = time() . mt_rand(0, 999); //时间戳
$datename = iconv("UTF-8","GBK", $datename);
$filetype = substr(strrchr($_FILES['temp_uploadinput']['name'], "."), 1); //获取文件后缀名
$base_path = GlobalConfig :: getInstance()->upload_src . 'temp/'; //接收文件目录  	
$target_path = $base_path . $datename . "." . $filetype; //文件新名字
//上传源文件
//检查文件路径是否存在，不存在则创建
if (!file_exists($base_path)) {
	if (!mkdir($base_path, 0777, true)) {
		die('无法建立路径');
	}
}
//开始上传文件  	
if (move_uploaded_file($_FILES['temp_uploadinput']['tmp_name'], $target_path)) {
	$target_path = iconv("GBK","UTF-8", $target_path);
	$datas = array (
		'head' => array (
			'code' => 1,
			'message' => ""
		),
		'value' => array (
			'filetype' => strtolower($filetype),
			'filename' => "php/yagl/downloadFile.php?nrmc=".$_FILES["temp_uploadinput"]["name"]."&url=".$target_path
		),
		'extend' => ''
	);
	echo json_encode($datas, JSON_UNESCAPED_UNICODE);
} else {
	$datas = array (
		'head' => array (
			'code' => 0,
			'message' => "上传失败"
		),
		'value' => "",
		'extend' => ''
	);
	echo json_encode($datas, JSON_UNESCAPED_UNICODE);
}

//类方法
?>