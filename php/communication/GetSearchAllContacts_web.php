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
 * 功能：获取巡逻组
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
	$content = Json_decode($content,true);
	$type = "";$key = "";
	if(isset($content['condition'])){
		$type = isset($content['condition']['type'])?$content['condition']['type']:"";
		$type = xssValidation($type);
		$key = isset($content['condition']['key'])?$content['condition']['key']:"";
		$key = xssValidation($key);
	}
	
	$params = $type.$key;
	if(!sqlInjectValidation($params)){
		$arr = array (
				'head' => array (
					'code' => 0,
					'message' => '输入的内容有误'
				),
				'value' => '',
				'extend' => ''
			);
			die(encodeJson($arr));
	}
	
	/*调试*/
	if (isDebug()) {
		$userId = '110101195209263558';
	}
		
		$communication = new Communication();//创建tpms数据库实例getImageInfo($img)
		$res=$communication->getSearchAllContact($type,$key,$_SESSION["orgCode"]);
		$res = array (
		'head' => array (
					'code' => 1,
					'message' => ''
					),
		'value' => $res,
		'extend' => ''
		);
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
		
  

?>