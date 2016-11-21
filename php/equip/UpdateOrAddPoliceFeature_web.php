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
		$id = isset($content['condition']['id'])?$content['condition']['id']:"";		//警员或警车ID
		$roleType = isset($content['condition']['roleType'])?$content['condition']['roleType']:"";//角色类型.警车：1，警员：2
		$featureId = isset($content['condition']['featureId'])?$content['condition']['featureId']:"";//特征物ID
	}

	/*调试*/
	if (isDebug()) {
		$id="41";
		$roleType="2";
		$featureId="22";
	}
	if ($id==""||$roleType==""||$featureId=="") {
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
	$res = $equip->updateOrAddPoliceFeature($id,$roleType,$featureId,$_SESSION["userId"]);//调用实例方法
	$result ="";
	if($res){
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => "操作成功",
				'extend' => ''
			);	
	}else{
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => "操作失败",
				'extend' => ''
			);
	}
	
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>