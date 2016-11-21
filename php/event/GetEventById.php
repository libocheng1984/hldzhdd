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
	include_once('../class/Event.class.php');
	
	/*查询*/
	$jqid = isset( $_REQUEST['jqid'] ) ? $_REQUEST['jqid'] : "";
	$rybh = isset( $_REQUEST['rybh'] ) ? $_REQUEST['rybh'] : "";

		
	/*调试*/
	if (isDebug()) {
		$jqid = '3';
		$rybh = "210225197512280456";
		//$lastTime='2015-12-12 16:17:21';
	}
	if ($jqid=="") {
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!!');
		die(encodeJson($arr));
	}
		
	$event = new Event();//创建调度类实例
	$res = $event->getEventByid($jqid,$rybh);//调用实例方法
	//$res = $event->insertZDTPeProcess($jqid);
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>