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
	include_once('../class/ZHDD.class.php');
	include_once('../class/DynamicPoint.class.php');
	include_once('../class/PolicePoints.class.php');
	
	$qzcy = isset ($_REQUEST['qzcy']) ? $_REQUEST['qzcy'] : ""; //群组名称
	$lastTime = isset ($_REQUEST['lastTime']) ? $_REQUEST['lastTime'] : ""; //群组名称
		
	/*调试*/
	if (isDebug()) {
		$qzcy = '210211197112261493,210225197512280456';
		//$lastTime='2015-12-12 16:17:21';
	}
	if ($qzcy=="") {
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
		
	$zhdd = new ZHDD();//创建调度类实例
	
	$res = $zhdd->getAndroidDynamicLocation($qzcy,$lastTime);//调用实例方法
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>