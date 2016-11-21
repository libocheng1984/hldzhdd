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
 * 功能：终端查询警情
 * 	{result:"true或false",errmsg:"操作信息",points:[]}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Event.class.php');
	include_once('../class/DynamicPoint.class.php');
	include_once('../class/PolicePoints.class.php');
	
	/*查询*/
	$orgCode = isset( $_REQUEST['orgCode'] ) ? $_REQUEST['orgCode'] : "";
	$jqclzt = isset( $_REQUEST['jqclzt'] ) ? $_REQUEST['jqclzt'] : "";
	$rybh = isset( $_REQUEST['rybh'] ) ? $_REQUEST['rybh'] : "";

		
	/*调试*/
	if (isDebug()) {
		$orgCode = '210203440000';
		//$jqclzt = "'5'";
		$rybh="210211197112261493";
		//$lastTime='2015-12-12 16:17:21';
	}
	if ($orgCode==""||$rybh=="") {
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!!');
		die(encodeJson($arr));
	}
		
	$event = new Event();//创建调度类实例
	$res = $event->getEvent($orgCode,$rybh,$jqclzt);//调用实例方法
	//echo $res;
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>