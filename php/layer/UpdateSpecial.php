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
 * 功能：平台根据警情ID查询警情详细
 * 	{head:{code:1/0,message=""},value:{},extend:""}
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
	$id = "";$zrqid="";
	if(isset($content['condition'])){
		$id = isset($content['condition']['id'])?$content['condition']['id']:"";
		$zrqid = isset($content['condition']['zrqid'])?$content['condition']['zrqid']:"";
	}
			
	/*调试*/
	if (isDebug()) {
		//$lastTime='2015-12-12 16:17:21';
	}
	
	if ($id==""||$zrqid=="") {
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
		
	$layer = new Layer();//创建调度类实例
	$bRet = $layer->updateSpecialById($id,$zrqid);//调用实例方法
	if($bRet){
		$res = $layer->GetSpecialTeam();
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => $res,
				'extend' => ''
			);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}else{
		$res = array (
				'head' => array (
							'code' => 0,
							'message' => '操作失败'
							),
				'value' => '',
				'extend' => ''
			);
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
	}
?>