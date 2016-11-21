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
	include_once('../class/ZHDD.class.php');
	include_once('../class/DynamicPoint.class.php');
	include_once('../class/PolicePoints.class.php');
	
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
	/*传入参数*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	$lastTime ="";$orgCode ="";	
	if(isset($content['condition'])){
		$lastTime = isset($content['condition']['lastTime'])?$content['condition']['lastTime']:null;
		$orgCode = isset($content['condition']['orgCode'])?$content['condition']['orgCode']: "";
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
	
	//$orgCode='210200000000';	
	$zhdd = new ZHDD();//创建调度类实例
	$res = $zhdd->getGPSMobile($orgCode,$lastTime);//调用实例方法
	//echo encodeJson($res);
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