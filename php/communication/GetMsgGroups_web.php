<?php
	try{
	session_start();
	session_commit();
	}catch(Exception $e){
		$result = array (
			'head' => array (
						'code' => 1,
						'message' => ''
						),
			'value' => '',
			'extend' => ''
		);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
	return;
	}
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");

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
	$userId = "";
	if(isset($content['condition'])){
		$userId = isset($content['condition']['userId'])?$content['condition']['userId']:"";
	}
	
	/*调试*/
	if (isDebug()) {
		$userId = '110101195209263558';
	}
	/*参数校验*/
	if ($userId=="") {
		$userId = $_SESSION["userId"];
	}	
		
		$communication = new Communication();//创建tpms数据库实例getImageInfo($img)
		$res=$communication->getMsgGroupsById($userId);
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