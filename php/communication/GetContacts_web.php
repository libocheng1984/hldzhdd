<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
	session_start();
	session_commit();
?>
<?php
/**
 *获取通信录
 *
 *
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Communication.class.php');
	
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
	
	/*查询*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$orgCode = "";$userName="";$id="";
	if(isset($content['condition'])){
		$orgCode = isset($content['condition']['orgCode'])?$content['condition']['orgCode']:"";
		$userName = isset($content['condition']['userName'])?$content['condition']['userName']:"";
		$id = isset($content['condition']['id'])?$content['condition']['id']:"";
	}
	/*调试*/
	if (isDebug()) {
		$orgCode = '210200000000';
		//$id = '210203000000';
	}
	
	if ($orgCode=="") {
		$arr = array (
				'head' => array (
					'code' => 0,
					'message' => '缺少参数!!'
				),
				'value' => '',
				'extend' => ''
			);
		die(encodeJson($arr));
	}	
		
	
		
	$communication = new Communication();//创建tpms数据库实例getImageInfo($img)
	$res=$communication->getContactsByOrg($orgCode,$id);
	$res = array (
		'head' => array (
					'code' => 1,
					'message' => ''
					),
		'value' => $res,
		'extend' => ''
	);
	//判断新号文件是否存在,不存在则创建
	$file_path=GlobalConfig::getInstance()->message_src;
	$file =GlobalConfig::getInstance()->message_src.$_SESSION["userId"].'.txt'; 
	if(!file_exists($file_path)){
		
		$TpmsDB = new TpmsDB();//创建tpms数据库实例
		$res1=$TpmsDB->mkdirs($file_path);
		
		$file=fopen ($file,'w');
		fclose($file);			
  	}
	echo json_encode($res, JSON_UNESCAPED_UNICODE);

?>