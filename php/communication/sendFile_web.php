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
 * 功能：发送文件
 * 
 * 输入参数：
 * 返回值：
 * 	{result:"true或false",errmsg:"错误信息",records:"[]"}
 */
include_once ('../GlobalConfig.class.php');
include_once ('../class/TpmsDB.class.php');
include_once ('../class/CommandDB.class.php');
include_once ('../class/Communication.class.php');
include_once('../class/mp3file.class.php');

/*登陆超时校验*/
if (!isset ($_SESSION["userId"])) {
	$arr = array (
		'head' => array (
			'code' => 9,
			'message' => '登录超时'
		),
		'value' => '',
		'extend' => ''
	);
	die(encodeJson($arr));
}

/*获取参数*/
//$target_path = $base_path . basename($_FILES['sendPic']['name']);
//$target_path1 = $base_path . basename('ys' . $_FILES['sendPic']['name']);

//文件以时间戳 重命名     
date_default_timezone_set('PRC'); //中国时区
$datename = date("YmdHis") . mt_rand(0, 999); //时间戳
$filetype = substr(strrchr($_FILES['sendPic']['name'], "."), 1); //获取文件后缀名

$uptypes = GlobalConfig :: getInstance()->uptypes;
$max_file_size = GlobalConfig :: getInstance()->max_file_size;
$imgType = GlobalConfig :: getInstance()->imgType;
$audioType = GlobalConfig :: getInstance()->audioType;
$videoType = GlobalConfig :: getInstance()->videoType;
$outType = GlobalConfig :: getInstance()->outType;

$type = "";

$file = $_FILES["sendPic"];
if ($max_file_size < $file["size"]) {
	//检查文件大小  
	$datas = array (
		'head' => array (
			'code' => 0,
			'message' => '文件太大!'
		),
		'value' => '',
		'extend' => ''
	);
	die(encodeJson($datas));
}
if (in_array(strtolower($filetype), $outType)) {
	$datas = array (
		'head' => array (
			'code' => 0,
			'message' => "该文件不允许上传"
		),
		'value' => '',
		'extend' => ''
	);
	die(encodeJson($datas));
} else if (in_array(strtolower($filetype), $imgType)) {
	if ($filetype == "gif") {
		$datas = array (
			'head' => array (
				'code' => 0,
				'message' => "系统不支持gif图片格式"
			),
			'value' => '',
			'extend' => ''
		);
		die(encodeJson($datas));
	} else {
		$type = "2";
	}
} else if (in_array(strtolower($filetype), $audioType)) {
			$type = "3";
} else if (in_array(strtolower($filetype), $videoType)) {
				$type = "4";
} else {
	$type = "5";
}
//上传图片
if($type=="2"){
	$base_path = GlobalConfig :: getInstance()->upload_src . 'picture/'; //接收文件目录  	
	$target_path = $base_path . $datename . "." . strtolower($filetype); //文件新名字
	$target_path1 = $base_path . 'ys' . $datename . "." . strtolower($filetype); //文件压缩新名字
	//上传源文件
	//检查文件路径是否存在，不存在则创建
	if (!file_exists($base_path)) {
	
		if (!mkdir($base_path, 0777, true)) {
			$datas = array (
				'head' => array (
					'code' => 0,
					'message' => "无法建立路径"
				),
				'value' => '',
				'extend' => ''
			);
			die(encodeJson($datas));
		}
	}
	
	//开始上传文件  	
	if (move_uploaded_file($_FILES['sendPic']['tmp_name'], $target_path)) {
	
		/*创建数据库实例*/
		$tpmsdb = new CommandDB(); //创建tpms数据库实例
	
		//获取文件大小
		$Info = $tpmsdb->getImageInfo($target_path); //调用实例方法
	
		//获取文件宽高
		$width = $Info['width'];
		$height = $Info['height'];
	
		//判断大小  
		if ($width >= $height) {
	
			$lwidth = floor($width / 150) + 1;
			$width = $width / $lwidth;
			$height = $height / $lwidth;
	
		} else {
	
			$lwidth = floor($height / 150) + 1;
			$width = $width / $lwidth;
			$height = $height / $lwidth;
		}
	
		/* 压缩图片 */
		$Info = $tpmsdb->img2thumb($target_path, $target_path1, $width, $height, $cut = 0, $proportion = 0);
	
		/* 文件路径 */
		$PHP_SELF = $_SERVER['PHP_SELF'];
		$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
		$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
		$url = 'http://' . $_SERVER['HTTP_HOST'] . substr($PHP_SELF, 0, strrpos($PHP_SELF, '/') + 1);
		$src1 = $url . str_replace("..", "php", $target_path);
		$src2 = $url . str_replace("..", "php", $target_path1);
		$datas = array (
			'head' => array (
				'code' => 1,
				'message' => ''
			),
			'value' => array (
				'filename' => $src1,
				'filename2' => $src2,
				'type' => '2',
				'size' => $file["size"],
				'sendName' => $_FILES['sendPic']['name']
			),
			'extend' => ''
		);
		echo json_encode($datas, JSON_UNESCAPED_UNICODE);
	} else {
		$datas = array (
			'result' => 'false',
			'success' => 'false'
		);
		echo json_encode($datas, JSON_UNESCAPED_UNICODE);
	}
}else{
	$size = "";
	if($type=="3"){
		$base_path = GlobalConfig :: getInstance()->upload_src . 'audio/'; //接收文件目录  
	}else if($type=="4"){
		$base_path = GlobalConfig :: getInstance()->upload_src . 'video/'; //接收文件目录  	
	}else{
		$base_path = GlobalConfig :: getInstance()->upload_src . 'file/'; //接收文件目录  	
	}
		$target_path = $base_path . $datename . "." . $filetype; //文件新名字
	//上传源文件
	//检查文件路径是否存在，不存在则创建
	if (!file_exists($base_path)) {
	
		if (!mkdir($base_path, 0777, true)) {
			$datas = array (
				'head' => array (
					'code' => 0,
					'message' => "无法建立路径"
				),
				'value' => '',
				'extend' => ''
			);
			die(encodeJson($datas));
		}
	}
	
	//开始上传文件  	
	if (move_uploaded_file($_FILES['sendPic']['tmp_name'], $target_path)) {
		$tpmsdb = new CommandDB(); //创建tpms数据库实例
		if($type=="3"){
			$size = $tpmsdb->mp3_len($target_path);	
			//print_r($size);
		}else{
			$size =  "";
		}
		/* 文件路径 */
		$PHP_SELF = $_SERVER['PHP_SELF'];
		$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
		$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
		$url = 'http://' . $_SERVER['HTTP_HOST'] . substr($PHP_SELF, 0, strrpos($PHP_SELF, '/') + 1);
		$src1 = $url . str_replace("..", "php", $target_path);
		$datas = array (
			'head' => array (
				'code' => 1,
				'message' => ''
			),
			'value' => array (
				'filename' => $src1,
				'filename2' => $src1,
				'type' => $type,
				'size' => $size,
				'sendName' => $_FILES['sendPic']['name']
			),
			'extend' => ''
		);
		echo json_encode($datas, JSON_UNESCAPED_UNICODE);
	} else {
		$datas = array (
			'head' => array (
				'code' => 0,
				'message' => '上传失败'
			),
			'value' => '',
			'extend' => ''
		);
		echo json_encode($datas, JSON_UNESCAPED_UNICODE);
	}
}


?>