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
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Equip.class.php');
	
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
/*
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	$orgCode="";$userId="";
	if(isset($content['condition'])){
		$orgCode = isset($content['condition']['orgCode'])?$content['condition']['orgCode']:$_SESSION["orgCode"];
		$userId = isset($content['condition']['userId'])?$content['condition']['userId']:"";
	}

*/		
	/*调试*/
	if (isDebug()) {
		//$orgCode = '210200000000';
		$userId ='210225197512280456';
	}
		
	$equip = new Equip();//创建调度类实例
	$res = $equip->getGroudCount($_SESSION["orgCode"],"");//调用实例方法
	$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => $res,
				'extend' => ''
			);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>