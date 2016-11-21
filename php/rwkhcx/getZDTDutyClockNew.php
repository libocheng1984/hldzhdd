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
 * 功能：查询线索列表
 * 	{result:"true或false",errmsg:"操作信息",points:[]}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/rwkhNew.class.php');
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
	//$lastTime = isset( $_REQUEST['lastTime'] ) ? $_REQUEST['lastTime'] : "";
        $rwid="";$userid="";$zhdksj="";$kdid="";$xlid="";;$tbid="";
	if(isset($content['condition'])){
		$rwid = isset($content['condition']['rwid'])?$content['condition']['rwid']:"";
                $userid= isset($content['condition']['userid'])?$content['condition']['userid']:"";
                $zhdksj = isset($content['condition']['zhdksj'])?$content['condition']['zhdksj']:"";
                $kdid = isset($content['condition']['kdid'])?$content['condition']['kdid']:"";
                $xlid = isset($content['condition']['xlid'])?$content['condition']['xlid']:"";
				$tbid = isset($content['condition']['tbid'])?$content['condition']['tbid']:"";
                
	}
	if ($rwid==""||$userid==""||$zhdksj==""||$kdid==""||$xlid=="") {
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
	$rwkh = new rwkhNew();//创建调度类实例
	$res = $rwkh->getZDTDutyClockList($rwid,$userid,$zhdksj,$kdid,$xlid,$tbid);//调用实例方法 
	//echo $res;
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