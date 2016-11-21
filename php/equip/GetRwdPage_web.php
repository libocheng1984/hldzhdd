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
	include_once('../class/Equip.class.php');
	/**
	 * 查询巡逻路线接口
	 */
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
	/*查询*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$kdmc = isset ($_REQUEST['kdmc']) ? $_REQUEST['kdmc'] : "";
	$kdsj = isset ($_REQUEST['kdsj']) ? $_REQUEST['kdsj'] : "";
	$page = isset($_REQUEST['page'])?$_REQUEST['page'] : "1";
	$rows = isset($_REQUEST['rows'])?$_REQUEST['rows'] : "10";
	$content = Json_decode($content,true);
	$kdmc = xssValidation($kdmc);
	if(!sqlInjectValidation($kdmc)){
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
		$orgCode ='210200000000';
	}
	$qsdksj = ""; $zzdksj = "";
	if($kdsj!=""){
		$sjArr = explode("至",$kdsj);
		$qsdksj =$sjArr[0];
		$zzdksj = $sjArr[1];
	}
	$equip = new Equip();//创建调度类实例
	$res = $equip->getRwdPage($kdmc,$qsdksj,$zzdksj,$_SESSION["orgCode"],$page,$rows);//调用实例方法
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