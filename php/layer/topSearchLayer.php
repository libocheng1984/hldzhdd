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
	$key="";$type="";
	if(isset($content['condition'])){
		$key = isset($content['condition']['key'])?$content['condition']['key']:"";
		$key = xssValidation($key);
		$type = isset($content['condition']['type'])?$content['condition']['type']:"";
		$type = xssValidation($type);
	}
	$params = $key.$type;
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
		$jqid = '3';
		$rybh = "210225197512280456";
		//$lastTime='2015-12-12 16:17:21';
	}
	if ($key==""||$type=="") {
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
		
	$layer = new Layer();//创建调度类实例
	$res = $layer->searchLayer($key,$type,$_SESSION["orgCode"]);
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