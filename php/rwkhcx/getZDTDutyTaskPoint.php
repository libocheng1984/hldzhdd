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
	include_once('../class/rwkh.class.php');
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
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
        $page = isset($_REQUEST['page'])?$_REQUEST['page'] : "1";
	$rows = isset($_REQUEST['rows'])?$_REQUEST['rows'] : "10";
	//$content = Json_decode($content, true);
	
         $rwid = isset($_REQUEST['rwid'])?$_REQUEST['rwid'] : "";
         $gid = isset($_REQUEST['gid'])?$_REQUEST['gid'] : "";
         $zhdksj = isset($_REQUEST['zhdksj'])?$_REQUEST['zhdksj'] : "";
	if(isset($content['condition'])){
		$rwid = isset($content['condition']['rwid'])?$content['condition']['rwid']:"";
                $gid = isset($content['condition']['gid'])?$content['condition']['gid']:"";
                $zhdksj = isset($content['condition']['zhdksj'])?$content['condition']['zhdksj']:"";
               
                
	}
	if ($rwid==""||$gid==""||$zhdksj=="") {
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

	/*调试*/
	if (isDebug()) {
		
	}
	
		
	$rwkh = new rwkh();//创建调度类实例
	$res = $rwkh->getZDTDutyTaskPointList($rwid,$gid,$zhdksj,$page,$rows);//调用实例方法
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