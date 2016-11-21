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
	$params = 'operation=PlanManagement_InsertEvent_v001&license=' . $license.'&content=';
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	$ajbt="";$ajlb="";$ajjb="";$yalb="";$czjb="";$yatx="";$userId="";$userId="";$yanr="";
	$nrmc="";$url="";
	if(isset($content['value'])){
		$ajbt = isset($content['value']['ajbt'])?$content['value']['ajbt']:"";
		$ajlb = isset($content['value']['ajlb'])?$content['value']['ajlb']:"";
		$ajjb = isset($content['value']['ajjb'])?$content['value']['ajjb']:"";
		$yalb = isset($content['value']['yalb'])?$content['value']['yalb']:"";
		$czjb = isset($content['value']['czjb'])?$content['value']['czjb']:"";
		$yatx = isset($content['value']['yatx'])?$content['value']['yatx']:"";
		$userId = isset($content['value']['userId'])?$content['value']['userId']:"";
		$yanr = isset($content['value']['yanr'])?$content['value']['yanr']:"";
	}

		
	/*调试*/
	if (isDebug()) {
		$page=1;
		$rows=10;
	}
	
	if($yanr){
		$arr =  explode("&amp;",substr(strrchr($yanr, "?"),1));
		for ( $i = 0; $i < count($arr); $i++ ) {
			$obj = explode("=",$arr[$i]);
			if($obj[0]=="nrmc"){
				$nrmc = $obj[1];
			}else if($obj[0]=="url"){
				$url = $obj[1];
			}
		}
	}
	$fileData = "";
	if($url){
		clearstatcache();
		$file = fopen($url, "r");
		$fileData = fread($file, filesize($url));  
		fclose($file);
		
	}
	$plan = new Plan();//创建调度类实例
	$res = $plan->insertEventPlan($interfaceUrl,$params,$ajbt,$ajlb,$ajjb,$yalb,$czjb,$yatx,$_SESSION["userId"],$_SESSION["orgCode"],$fileData,$nrmc,$userId);//调用实例方法
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