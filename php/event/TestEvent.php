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
 * 功能：终端处警更新状态接口
 * 
 * 	{result:"true或false",errmsg:"操作信息"}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Event.class.php');
	
	/*获取参数*/	

	$jqid= "";
	$res=$event->testFeedBack($jqid);
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
		

?>