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
	include_once('../class/Equip.class.php');
	
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
	$kdmc="";$kdsj="";$jgsj="";$ydcs="";$geometry="";$timePartArray=array();
	if(isset($content['value'])){
		$kdid = isset($content['value']['kdid'])?$content['value']['kdid']:"";
		$kdmc = isset($content['value']['kdmc'])?$content['value']['kdmc']:"";
		$kdsj = isset($content['value']['kdsj'])?$content['value']['kdsj']:"";
		$jgsj = isset($content['value']['jgsj'])?$content['value']['jgsj']:"";
		$ydcs = isset($content['value']['ydcs'])?$content['value']['ydcs']:"";
		$geometry = isset($content['value']['geometry'])?$content['value']['geometry']:"";
		$timePartArray = isset($content['value']['timePartArray'])?$content['value']['timePartArray']:array();
	}

		
	/*调试*/
	if (isDebug()) {
		$id="41";
		$roleType="2";
		$featureId="22";
	}
	if (count($timePartArray)==0||$geometry=="") {
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
	if($kdsj!=""){
		$sjArr = explode("至",$kdsj);
		$qsdksj =$sjArr[0];
		$zzdksj = $sjArr[1]=="00:00"?"24:00":$sjArr[1];
	}else{
		$qsdksj = "00:00:00";
		$zzdksj = "24:00:00";
	}
	//echo("dddd");	
	$equip = new Equip();//创建调度类实例
	$res = $equip->updateOrAddRwd($kdid,$kdmc,$timePartArray,$geometry,$_SESSION["userId"],$_SESSION["orgCode"]);//调用实例方法
	$result ="";
	if($res){
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => "操作成功",
				'extend' => ''
			);	
	}else{
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => "操作失败",
				'extend' => ''
			);
	}
	
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>