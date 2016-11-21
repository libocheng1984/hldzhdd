<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
?>
<?php
/**
 * 功能：终端查询警情
 * 	{result:"true或false",errmsg:"操作信息",points:[]}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Event.class.php');
	
	$event = new Event();//创建调度类实例
	$res = $event->getOrgbyAir("SZZRQ","121.68832631333417","38.89187926782469");//调用实例方法
	echo "code==".$res['orgcode'];
	echo "name==".$res['orgname'];
?>