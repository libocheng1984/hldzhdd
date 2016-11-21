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
 * 功能：平台查询预案列表
 * 	{head:{code:1/0,message=""},value:{},extend:""}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Plan.class.php');
	
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
	$orgCode = isset ($_REQUEST['orgCode']) ? $_REQUEST['orgCode'] : $_SESSION["orgCode"];
	$ajbt = isset ($_REQUEST['ajbt']) ? $_REQUEST['ajbt'] : "";
	$ajbt = xssValidation($ajbt);
	$page = isset($_REQUEST['page'])?$_REQUEST['page'] : "1";
	$rows = isset($_REQUEST['rows'])?$_REQUEST['rows'] : "10";

	if(!sqlInjectValidation($ajbt)){
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
		$page=1;
		$rows=10;
	}
	if ($orgCode=="") {
		$orgCode = $_SESSION["orgCode"];
	}
		
	$plan = new Plan();//创建调度类实例
	$res = $plan->getEventPlan($orgCode,$ajbt,$page,$rows);//调用实例方法
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