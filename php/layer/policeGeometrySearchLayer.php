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
	include_once('../class/Layer.class.php');
	
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
	$id="";$bmdm="";$username="";$sfzhm="";$sbbm="";$dhhm="";$hphm="";
	if(isset($content['condition'])){
		$id = isset($content['condition']['id'])?$content['condition']['id']:"";
		$bmdm = isset($content['condition']['bmdm'])?$content['condition']['bmdm']:"";
		$username = isset($content['condition']['username'])?$content['condition']['username']:"";
		$username = xssValidation($username);
		$sfzhm = isset($content['condition']['sfzhm'])?$content['condition']['sfzhm']:"";
		$sfzhm = xssValidation($sfzhm);
		$sbbm = isset($content['condition']['sbbm'])?$content['condition']['sbbm']:"";
		$sbbm = xssValidation($sbbm);
		$dhhm = isset($content['condition']['dhhm'])?$content['condition']['dhhm']:"";
		$dhhm = xssValidation($dhhm);
		$hphm = isset($content['condition']['hphm'])?$content['condition']['hphm']:"";
		$hphm = xssValidation($hphm);
	}
	
	$params = $username.$sfzhm.$sbbm.$dhhm.$hphm;
	if(!sqlInjectValidation($params)){
		$arr = array (
				'head' => array (
					'code' => 0,
					'message' => '输入的内容有误'
				),
				'value' => '',
				'extend' => ''
			);
			die(encodeJson($arr));
	}

		
	/*调试*/
	if (isDebug()) {
		$id = '2';
		$sfzhm = "210211197112261493";
		//$lastTime='2015-12-12 16:17:21';
	}
	if ($id=="1"&&$bmdm==""&&$username==""&&$sfzhm==""&&$sbbm==""&&$dhhm=="") {
		$arr = array (
				'head' => array (
					'code' => 0,
					'message' => '请输入查询条件!!'
				),
				'value' => '',
				'extend' => ''
			);
		die(encodeJson($arr));
	}else if ($id=="2"&&$bmdm==""&&$hphm==""&&$sfzhm==""&&$sbbm==""&&$dhhm=="") {
		$arr = array (
				'head' => array (
					'code' => 0,
					'message' => '请输入查询条件!!'
				),
				'value' => '',
				'extend' => ''
			);
		die(encodeJson($arr));
	}else if ($id=="3"&&$bmdm==""&&$sbbm=="") {
		$arr = array (
				'head' => array (
					'code' => 0,
					'message' => '请输入查询条件!!'
				),
				'value' => '',
				'extend' => ''
			);
		die(encodeJson($arr));
	}
	
	if($id=="1"){	
		$layer = new Layer();//创建调度类实例
		$res = $layer->searchPoliceLayer($bmdm,$username,$sfzhm,$sbbm,$dhhm,$_SESSION["orgCode"]);
		$result = array (
					'head' => array (
								'code' => 1,
								'message' => ''
								),
					'value' => $res,
					'extend' => ''
				);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}else if($id=="2"){
		$layer = new Layer();//创建调度类实例
		$res = $layer->searchPoliceGroupLayer($bmdm,$hphm,$sfzhm,$sbbm,$dhhm,$_SESSION["orgCode"]);
		$result = array (
					'head' => array (
								'code' => 1,
								'message' => ''
								),
					'value' => $res,
					'extend' => ''
				);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}else if($id=="3"){
		$layer = new Layer();//创建调度类实例
		$res = $layer->searchM350Layer($bmdm,$sbbm,$_SESSION["orgCode"]);
		$result = array (
					'head' => array (
								'code' => 1,
								'message' => ''
								),
					'value' => $res,
					'extend' => ''
				);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}
	
?>