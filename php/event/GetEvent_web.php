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
 * 功能：平台查询警情
 * 	{head:{code:1/0,message=""},value:{},extend:""}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Event.class.php');
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
	$jqclzt="";$orgCode="";$jjrbh="";
	if(isset($content['condition'])){
		$lastTime = isset($content['condition']['lastTime'])?$content['condition']['lastTime']:null;
		$orgCode = isset($content['condition']['orgCode'])?$content['condition']['orgCode']:"";
		$jqclzt = isset($content['condition']['jqclzt'])?$content['condition']['jqclzt']:"";
		$jjrbh = isset($content['condition']['jjrbh'])?$content['condition']['jjrbh']:"";
		$jjrbh = xssValidation($jjrbh);
	}
	
	if(!sqlInjectValidation($jjrbh)){
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
		$orgCode = '210200000000';
		$jqclzt = "1";
		//$rybh="210225197512280456";
		$lastTime='';
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
		
	$event = new Event();//创建调度类实例
	$res = $event->getWebEvent($orgCode,$lastTime,$jqclzt,$jjrbh);//调用实例方法
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