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
	include_once('../class/mrzb.class.php');
	
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
	$page = isset($_REQUEST['page'])?$_REQUEST['page'] : "1";
	$rows = isset($_REQUEST['rows'])?$_REQUEST['rows'] : "10";
	
	$content = Json_decode($content,true);
	$bzmc="";$zzxm="";
	$bzmc = isset($_REQUEST['bzmc'])?$_REQUEST['bzmc'] : "";
	$zzxm = isset($_REQUEST['zzxm'])?$_REQUEST['zzxm'] : "";
	if(isset($content['condition'])){	
        $bzmc = isset($content['condition']['bzmc'])?$content['condition']['bzmc']:"";
        $zzxm = isset($content['condition']['zzxm'])?$content['condition']['zzxm']:"";
	}
	
		
	$mrzb = new mrzb();//创建调度类实例
	$res =$mrzb->getZbzList($bzmc, $zzxm, $page, $rows,$_SESSION["orgCode"]);
	$code = $res['result']==true?1:0;
	$result = array (
				'head' => array (
							'code' => $code,
							'message' => $res['errmsg']
							),
				'value' => $res['records'],
				'extend' => ''
			);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>