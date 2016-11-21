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
 * 功能：终端根据警情ID查询警情详细
 * 	{result:"true或false",errmsg:"操作信息",records:[]}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Layer.class.php');
	
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
	$id="";$jgmc="";
	if(isset($content['condition'])){
		$id = isset($content['condition']['id'])?$content['condition']['id']:"";
		$jgmc = isset($content['condition']['jgmc'])?$content['condition']['jgmc']:"";
		$jgmc = xssValidation($jgmc);
	}
	if(!sqlInjectValidation($jgmc)){
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
		$id = '3';
		$jgmc = "工作";
		//$lastTime='2015-12-12 16:17:21';
	}
//	$jgmc ="00";
//	$id="1";
	if ($jgmc=="") {
		$arr = array (
				'head' => array (
					'code' => 0,
					'message' => '请输入查询条件!!'
				),
				'value' => '',
				'extend' => ''
			);
		die(encodeJson($arr));
	}
	
	if($id=="1"){	
		$layer = new Layer();//创建调度类实例
		$res = $layer->searchXzjgLayer($jgmc);
		$result = array (
					'head' => array (
								'code' => 1,
								'message' => ''
								),
					'value' => $res,
					'extend' => ''
				);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}else if($id=="2"){
		$layer = new Layer();//创建调度类实例
		$res = $layer->searchLsjddLayer($jgmc);
		$result = array (
					'head' => array (
								'code' => 1,
								'message' => ''
								),
					'value' => $res,
					'extend' => ''
				);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}else if($id=="3"){
		$layer = new Layer();//创建调度类实例
		$res = $layer->searchJwgzzLayer($jgmc);
		$result = array (
					'head' => array (
								'code' => 1,
								'message' => ''
								),
					'value' => $res,
					'extend' => ''
				);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}else if($id=="4"){
		$layer = new Layer();//创建调度类实例
		$res = $layer->searchLskdLayer($jgmc);
		$result = array (
					'head' => array (
								'code' => 1,
								'message' => ''
								),
					'value' => $res,
					'extend' => ''
				);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}
	
?>