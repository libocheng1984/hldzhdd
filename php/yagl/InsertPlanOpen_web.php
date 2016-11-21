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
	$id="";$jsrId="";$yabh="";
	if(isset($content['value'])){
		$id = isset($content['value']['id'])?$content['value']['id']:"";
		$yabh = isset($content['value']['yabh'])?$content['value']['yabh']:"";
		$jsrId = isset($content['value']['jsrId'])?$content['value']['jsrId']:"";
	}

		
	/*调试*/
	if (isDebug()) {
		$page=1;
		$rows=10;
	}
	
	if($jsrId!=""){
		$plan = new Plan();//创建调度类实例
		$arr =  explode(",",$jsrId);
		$params_promoter = 'operation=PlanManagement_ModifyPromoter_v001&license=' . $license.'&content=';
		$params_open = 'operation=PlanManagement_InsertOpen_v001&license=' . $license.'&content=';
		$params_receive = 'operation=PlanManagement_InsertReceive_v001&license=' . $license.'&content=';
		$time = date('Y-m-d h:i:s',time());
		$res = $plan->updatePlanPromoter($interfaceUrl,$params_promoter,$id,$yabh,"1",$_SESSION["userId"]);//调用实例方法
		$res = $plan->insertPlanOpen($interfaceUrl,$params_open,$id,$yabh,$time,"","0",$_SESSION["userId"]);//调用实例方法
		$res = $plan->insertOrUpdatePlanReciver($interfaceUrl,$params_receive,$id,$yabh,$jsrId,"0",$time);//调用实例方法
//		for ( $i = 0; $i < count($arr); $i++ ) {
//			$data = $plan->getPlanReciverByPromoter($yabh,$arr[$i]);//调用实例方法
//			if($data&&$data['jsbh']){
//				$plan->updatePlanReciver($interfaceUrl,$params_receive,$data['jsbh'],$yabh,$arr[$i],"0",$time);//调用实例方法
//			}else{
//				$plan->insertPlanReciver($interfaceUrl,$params_receive,$yabh,$arr[$i],"0",$time);//调用实例方法
//			}
//			
//		}
	}
	if($res['code']=="1"){
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