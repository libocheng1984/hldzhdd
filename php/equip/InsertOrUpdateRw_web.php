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
	$rwid="";$jgsj="";$ydcs="";$geometry="";
	if(isset($content['value'])){
		$rwid = isset($content['value']['rwid'])?$content['value']['rwid']:"";
		$rwmc = isset($content['value']['rwmc'])?$content['value']['rwmc']:"";
		$dianwei = isset($content['value']['dianwei'])?$content['value']['dianwei']:"";
		$xunluozhu = isset($content['value']['xunluozhu'])?$content['value']['xunluozhu']:"";
	}

		
	/*调试*/
	if (isDebug()) {
		$id="41";
		$roleType="2";
		$featureId="22";
	}
	//echo("dddd");	
	$equip = new Equip();//创建调度类实例
	$res = $equip->updateOrAddRw($rwid,$rwmc,$dianwei,$xunluozhu,$_SESSION["userId"],$_SESSION["orgCode"]);//调用实例方法
	$result ="";
	if($res['bRet']){
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => $res,
				'extend' => ''
			);	
	}else{
		$result = array (
				'head' => array (
							'code' => 0,
							'message' => ''
							),
				'value' => $res,
				'extend' => ''
			);
	}
	ini_set('memory_limit','3036M');
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>