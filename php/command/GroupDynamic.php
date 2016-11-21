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
	
	/*查询*/
	$lastTime = isset( $_REQUEST['lastTime'] ) ? $_REQUEST['lastTime'] : null;
	$orgCode = isset( $_REQUEST['orgCode'] ) ? $_REQUEST['orgCode'] : "";
		
	/*调试*/
	if (isDebug()) {
		$orgCode = '210200000000';
		$lastTime = "";
		//$lastTime='2015-12-12 16:17:21';
	}
	if ($orgCode=="") {
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!!');
		die(encodeJson($arr));
	}
		
	$zhdd = new ZHDD();//创建调度类实例
	$res = $zhdd->getGroupDynamicLocation($orgCode,$lastTime);
	//$res = $zhdd->getDeviceLocation('210225197512280456');
	//$res = $zhdd->GetDutyGroupByOrg($orgCode);
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>