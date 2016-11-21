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
	include_once ('../GlobalConfig.class.php');
        include_once ('../class/TpmsDB.class.php');
        include_once ('../class/SystemDB.class.php');
        include_once ('../class/Event.class.php');
        include_once ('../class/mrzb.class.php');
	
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
} else {
	$ryzh = $_SESSION["userId"];
}
	
	/*查询*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
//	if(isset($content['condition'])){
//		$zzzh = isset($content['condition']['zzzh'])?$content['condition']['zzzh']:"";
//	}
	
		
	/*调试*/

		
	$mrzb = new mrzb();//创建调度类实例
	$res = $mrzb->getMyPaiban($ryzh);//调用实例方法
	//$res = $event->insertZDTPeProcess($jqid);
	$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => $res,
				'extend' => ''
			);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>