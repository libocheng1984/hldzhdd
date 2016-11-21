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
	include_once('../class/DynamicPoint.class.php');
	include_once('../class/PolicePoints.class.php');
	
	/*查询*/
	$jqid = isset( $_REQUEST['jqid'] ) ? $_REQUEST['jqid'] : "";
	$mhjqzb = isset( $_REQUEST['mhjqzb'] ) ? $_REQUEST['mhjqzb'] : "";
	$hphm = isset( $_REQUEST['hphm'] ) ? iconv("UTF-8","GBK",$_REQUEST['hphm']) : "";
	$orgCode = isset( $_REQUEST['orgCode'] ) ? $_REQUEST['orgCode'] : "";
	$bjnr = isset( $_REQUEST['bjnr'] ) ? $_REQUEST['bjnr'] : "";

		
	/*调试*/
	if (isDebug()) {
		$bjnr = "人民广场打架纠纷";
		//$bjnr = iconv("UTF-8","GBK",$bjnr);
		$hphm= "辽B22222";
		$hphm = iconv("UTF-8","GBK",$hphm);
		$jqid= "1";
		$mhjqzb= "POINT(121.61490633330813 38.90971375469072)";
		$orgCode= "210203440000";

//		$orgCode = '210203440000';
//		$jqid = '1';
//		$mhjqzb = 'point(121.61761 38.913387)';
//		$hphm = "辽B22222";
//		$hphm = iconv("UTF-8","GBK",$hphm);
//		$bjnr = "人民广场发生抢劫";
		//$lastTime='2015-12-12 16:17:21';
	}
	if ($orgCode=="") {
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!!');
		die(encodeJson($arr));
	}

	$event = new Event();//创建调度类实例
	$res = $event->updateEventCar($_SESSION["userId"],$jqid,$mhjqzb,$hphm,$orgCode,$bjnr);//调用实例方法
	//$res = $event->GetMenByBmdm($orgCode,$hphm);
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>