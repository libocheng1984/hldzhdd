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
	 * 添加和修改巡逻路线接口
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
		$featureId = isset($content['condition']['featureId'])?$content['condition']['featureId']:"";
		$featureName = isset($content['condition']['featureName'])?$content['condition']['featureName']:"";
		$geometry = isset($content['condition']['geometry'])?$content['condition']['geometry']:"";
		$type = isset($content['condition']['type'])?$content['condition']['type']:"";
	}

	/*调试*/
	if (isDebug()) {
		//$orgCode = '210200000000';
		//$featureId="6";
		$featureName ='中国';
		$featureName = iconv("UTF-8","GBK",$featureName);
		$type="1";
		$geometry = 'LINESTRING(3 4,10 50,20 25)';
		echo $featureName;
	}
	if ($featureName=="") {
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
		
	$equip = new Equip();//创建调度类实例
	$res = $equip->updateOrAddDutyFeature($featureId,$featureName,$geometry,$_SESSION["userId"],$type);//调用实例方法
	$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => array ('featureId'=>$res,'featureName'=>$featureName,'geometry'=>$geometry),
				'extend' => ''
			);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>