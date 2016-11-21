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
	 * 查询巡逻路线接口
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
	/*查询*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	$rwid = "";
	if(isset($content['condition'])){
		$rwid = isset($content['condition']['rwid'])?$content['condition']['rwid']:"";
	}

	/*调试*/
	if (isDebug()) {
		$orgCode ='210200000000';
	}
	if ($rwid=="") {
		$result = array (
				'head' => array (
					'code' => 1,
					'message' => ''
				),
				'value' => array(),
				'extend' => ''
			);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}else{
		$equip = new Equip();//创建调度类实例
		$res = $equip->getEquipFeatureByOrgCode($featureName,$orgCode,$page,$rows);//调用实例方法
		$code = $res['result']==true?1:0;
		$result = array (
					'head' => array (
								'code' => $code,
								'message' => $res['errmsg']
								),
					'value' => $res['records'],
					'extend' => ''
				);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}
		
	
?>