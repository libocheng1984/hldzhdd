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
	 * 补充指令信息
	 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/EventMix.class.php');
	include_once('../class/DynamicPoint.class.php');
	include_once('../class/PolicePoints.class.php');
	
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
	/*传入参数*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	$jqid="";$mhjqzb="";$hphm="";$jqdd="";$orgCode="";$bjnr="";$zlnr="";
	if(isset($content['condition'])){
		$jqid = isset($content['condition']['jqid'])?$content['condition']['jqid']:"";
		$mhjqzb = isset($content['condition']['mhjqzb'])?$content['condition']['mhjqzb']:"";
		$hphm = isset($content['condition']['hphm'])?iconv("UTF-8","GBK",$content['condition']['hphm']):"";
		$jqdd = isset($content['condition']['jqdd'])?$content['condition']['jqdd']:"";
		$orgCode = isset($content['condition']['orgCode'])?$content['condition']['orgCode']:"";
		$bjnr = isset($content['condition']['bjnr'])?$content['condition']['bjnr']:"";
		$zlnr = isset($content['condition']['zlnr'])?$content['condition']['zlnr']:"";
	}
	
		
	/*调试*/
	if (isDebug()) {
		$bjnr = "人民广场打架纠纷";
		//$bjnr = iconv("UTF-8","GBK",$bjnr);
		$hphm= "辽B22222";
		$hphm = iconv("UTF-8","GBK",$hphm);
		$jqid= "6";
		$jqdd = "www";
		$zlnr="www";
		$mhjqzb= "POINT(121.61490633330813 38.90971375469072)";
		$orgCode= "210203440000";
		$fwjqdz="人民会场";
		$fwjqdz = iconv("UTF-8","GBK",$fwjqdz);

//		$orgCode = '210203440000';
//		$jqid = '1';
//		$mhjqzb = 'point(121.61761 38.913387)';
//		$hphm = "辽B22222";
//		$hphm = iconv("UTF-8","GBK",$hphm);
//		$bjnr = "人民广场发生抢劫";
		//$lastTime='2015-12-12 16:17:21';
	}
	if ($orgCode==""||$jqid==""||$hphm=="") {
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

	$event = new EventMix();//创建调度类实例
	$res = $event->addCommandEvent($_SESSION["userId"],$jqid,$jqdd,$hphm,$orgCode,$bjnr,$zlnr);//调用实例方法
	$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => $res,
				'extend' => ''
			);
	//$res = $event->GetMenByBmdm($orgCode,$hphm);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>