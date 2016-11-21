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
	$interfaceUrl = GlobalConfig :: getInstance()->interfaceUrl;
	$license = GlobalConfig :: getInstance()->license;
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	$id="";$jsrId="";$yabh="";$qdsj="";$yazt="";$qdrId="";
	if(isset($content['value'])){
		$id = isset($content['value']['id'])?$content['value']['id']:"";
		$yabh = isset($content['value']['yabh'])?$content['value']['yabh']:"";
		$jsrId = isset($content['value']['jsrId'])?$content['value']['jsrId']:"";
		$yazt = isset($content['value']['yazt'])?$content['value']['yazt']:"";
	}

		
	/*调试*/
	if (isDebug()) {
		$page=1;
		$rows=10;
	}
	
	if($id!=""){
		$time = date('Y-m-d h:i:s',time());
		$plan = new Plan();//创建调度类实例
		$params_promoter = 'operation=PlanManagement_ModifyPromoter_v001&license=' . $license.'&content=';
		$params_open = 'operation=PlanManagement_ModifyOpen_v001&license=' . $license.'&content=';
		$res = $plan->updatePlanPromoter($interfaceUrl,$params_promoter,$id,$yabh,"0",$_SESSION["userId"]);//调用实例方法
		$res = $plan->closePlanOpen($interfaceUrl,$params_open,$id,"0",$yabh,"",$time,"1",$_SESSION["userId"]);//调用实例方法
	}
	if($res['code']=="1"){
		 if(is_file($url)){
		 	@unlink ($url);
		 }
		$result = array (
				'head' => array (
							'code' => $res['code'],
							'message' => $res['msg']
							),
				'value' => '',
				'extend' => ''
			);
	}else{
		$result = array (
				'head' => array (
							'code' => 0,
							'message' => $res['msg']
							),
				'value' => '',
				'extend' => ''
			);
	}
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>