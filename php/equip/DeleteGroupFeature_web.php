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
	include_once('../class/Equip.class.php');
	/**
	 * 巡逻路线绑定接口
	 */
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
	$featureId="";$featureName="";$geometry="";$type="";
	if(isset($content['condition'])){
		$featureId = isset($content['condition']['featureId'])?$content['condition']['featureId']:"";//特征物ID
	}

	/*调试*/
	if (isDebug()) {
		$id="41";
		$roleType="2";
		$featureId="22";
	}
	if ($featureId=="") {
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
	//echo("dddd");	
	$equip = new Equip();//创建调度类实例
	
	$res = $equip->deleteGroupFeature($featureId);//调用实例方法
	$code = $res['result']==true?1:0;
	$result = array (
				'head' => array (
							'code' => $code,
							'message' => $res['errmsg']
							),
				'value' => $res['result'],
				'extend' => ''
			);
	
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>