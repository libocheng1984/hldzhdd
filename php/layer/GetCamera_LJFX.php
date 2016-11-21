<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
	session_start();
	session_commit();
?>
<?php
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
	/*传入参数*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	$event_x="";$event_y="";$group_x="";$group_y="";
	if(isset($content['condition'])){
		$event_x = isset($content['condition']['event_x'])?$content['condition']['event_x']:"";
		$event_y = isset($content['condition']['event_y'])?$content['condition']['event_y']:"";
		$group_x = isset($content['condition']['group_x'])?$content['condition']['group_x']:"";
		$group_y = isset($content['condition']['group_y'])?$content['condition']['group_y']:"";
	}
	if ($event_x==""||$event_y==""||$group_x==""||$group_y=="") {
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
	$res = $layer->GetCameraByljfx($event_x,$event_y,$group_x,$group_y);
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