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
include_once('../class/Communication.class.php');
include_once('../class/mp3file.class.php');

/*获取参数*/
$codeId = isset ($_REQUEST['codeId']) ? $_REQUEST['codeId'] : "";
$type = isset ($_REQUEST['type']) ? $_REQUEST['type'] : "";
$gid = isset ($_REQUEST['gid']) ? $_REQUEST['gid'] : "";
$receiveIds = isset ($_REQUEST['receiveIds']) ? $_REQUEST['receiveIds'] : "";

$userId = isset ($_REQUEST['userId']) ? $_REQUEST['userId'] : "";
$userName = isset ($_REQUEST['userName']) ? $_REQUEST['userName'] : "";
$orgCode = isset ($_REQUEST['orgCode']) ? $_REQUEST['orgCode'] : "";
$orgName = isset ($_REQUEST['orgName']) ? $_REQUEST['orgName'] : "";
$sendName = isset ($_REQUEST['fileName']) ? $_REQUEST['fileName'] : "";


//文件以时间戳 重命名     
date_default_timezone_set('PRC'); //中国时区
$datename = date("YmdHis") . mt_rand(0, 999); //时间戳
$filetype = substr(strrchr($_FILES['data']['name'], "."), 1); //获取文件后缀名
//echo strtolower($filetype)."...........".$type;
$uptypes = GlobalConfig :: getInstance()->uptypes;
$max_file_size = GlobalConfig :: getInstance()->max_file_size;
$imgType = GlobalConfig :: getInstance()->imgType;
$audioType = GlobalConfig :: getInstance()->audioType;
$videoType = GlobalConfig :: getInstance()->videoType;
$outType = GlobalConfig :: getInstance()->outType;

$sysType = "";

$file = $_FILES["data"];
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
		$sysType = "2";
	}
} else if (in_array(strtolower($filetype), $audioType)) {
		$sysType = "3";
} else if (in_array(strtolower($filetype), $videoType)) {
		$sysType = "4";
} else {
		$sysType = "5";
}


/*调试*/
if (isDebug()) {
	$gid = '108';
	$type = '3';
	$codeId = '15456';
}

$width = '';
$height = '';
if ($sysType == '2') { //上传图片
	$base_path = GlobalConfig :: getInstance()->upload_src . 'picture/'; //接收文件目录  		

	$target_path = $base_path . $datename . "." . $filetype; //文件新名字
	$target_path1 = $base_path . 'ys' . $datename . "." . $filetype; //文件压缩新名字

	//上传源文件
	//检查文件路径是否存在，不存在则创建
	if (!file_exists($base_path)) {

		if (!mkdir($base_path, 0777, true)) {
			$datas = array (
				'result' => 'false',
				'success' => 'false',
				'errmsg' => "无法建立路径"
			);
			die(encodeJson($datas));
		}
	}

	//开始上传文件  	
	if (move_uploaded_file($_FILES['data']['tmp_name'], $target_path)) {

		/*创建实例*/
		$tpmsdb = new CommandDB();
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

		$src1 = $datename . "." . $filetype;
		$src2 = 'ys' . $datename . "." . $filetype;

		/* 发送消息 */
		$communication = new Communication(); //创建对象
		$res = $communication->sendPicture($gid, $userId, $userName, $file["size"], "picture/".$src1, "picture/".$src2,$_FILES['data']['name'],$orgCode,$orgName);
		$datas = array (
			'result' => 'true',
			'success' => 'true',
			'src' => $src1,
			'width' => $width,
			'height' => $height
		);
		echo json_encode($datas, JSON_UNESCAPED_UNICODE);
	} else {
		$datas = array (
			'result' => 'false',
			'success' => 'false'
		);
		echo json_encode($datas, JSON_UNESCAPED_UNICODE);
	}
} else { //上传语音
	if($sysType=="3"){
		$base_path = GlobalConfig :: getInstance()->upload_src . 'audio/'; //接收文件目录 
	}else if($sysType=="4"){
		$base_path = GlobalConfig :: getInstance()->upload_src . 'video/'; //接收文件目录  	
	}else{
		$base_path = GlobalConfig :: getInstance()->upload_src . 'file/'; //接收文件目录  	
	}
		//文件以时间戳 重命名     
		$target_path = $base_path . $datename . "." . $filetype; //文件新名字

		//上传源文件
		//检查文件路径是否存在，不存在则创建
		if (!file_exists($base_path)) {

			if (!mkdir($base_path, 0777, true)) {
				$datas = array (
					'result' => 'false',
					'success' => 'false',
					'errmsg' => "无法建立路径"
				);
				die(encodeJson($datas));
			}
		}

		//上传源文件
		if (move_uploaded_file($_FILES['data']['tmp_name'], $target_path)) {
			$tpmsdb = new CommandDB(); //创建tpms数据库实例
			$src1 = $datename . "." . $filetype;	
			$lenTime="";
			if($sysType=="3"){
				$lenTime = $tpmsdb->mp3_len($target_path);
				$path_src =  "audio/".$src1;	
			}else if($sysType=="4"){
				$path_src =  "video/".$src1;
			}else if($sysType=="5"){
				$path_src =  "file/".$src1;
			}
			
			/* 发送消息 */
			$communication = new Communication();
			//$res = $communication->sendOthers($groupid, $_SESSION["userId"], $_SESSION["userName"], $size, $filename, $filename2,$type);
			$res = $communication->sendOthers($gid, $userId, $userName, $lenTime, $path_src, $path_src,$sysType,$sendName,$orgCode,$orgName);
			$datas = array (
				'result' => 'true',
				'success' => 'true',
				'src' => $src1,
				'sendName' => $sendName
			);
			echo json_encode($datas, JSON_UNESCAPED_UNICODE);
		} else {
			$datas = array (
				'result' => 'false',
				'success' => 'false',
				'errmsg' => '上传失败'
			);
			echo json_encode($datas, JSON_UNESCAPED_UNICODE);
		}

}
?>