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
	include_once('../class/Event.class.php');
	
	/*查询*/
	$cjdbh = isset( $_REQUEST['cjdbh'] ) ? $_REQUEST['cjdbh'] : "";
	$zlbh = isset( $_REQUEST['zlbh'] ) ? $_REQUEST['zlbh'] : "";

		
	/*调试*/
	if (isDebug()) {
		$cjdbh = '93';
		$zlbh = '25';
		//$lastTime='2015-12-12 16:17:21';
	}
	if ($cjdbh==""||$zlbh=="") {
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!!');
		die(encodeJson($arr));
	}
	
	$event = new Event();//创建调度类实例
	$res = $event->getFileByCjdbh($cjdbh,$zlbh);//调用实例方法
	//$res = $event->insertZDTPeProcess($jqid);
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>