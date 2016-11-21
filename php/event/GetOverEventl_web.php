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
	include_once('../class/EventMix.class.php');
	
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
	$content = Json_decode($content,true);
	$orgCode="";$jqclzt="";$xwh="";$jqjssj="";$bjsj="";$zdjq="";$jqbh_end4="";
	if(isset($content['condition'])){
		$orgCode = isset($content['condition']['orgCode'])?$content['condition']['orgCode']:"";
		$jqclzt = isset($content['condition']['jqclzt'])?$content['condition']['jqclzt']:"";
        $xwh = isset($content['condition']['xwh'])?$content['condition']['xwh']:"";
        $jqjssj = isset($content['condition']['jqjssj'])?$content['condition']['jqjssj']:"";
        $jqjssj = xssValidation($jqjssj);
        $bjsj = isset($content['condition']['bjsj'])?$content['condition']['bjsj']:"";
        $zdjq = isset($content['condition']['zdjq'][0])?$content['condition']['zdjq'][0]:"";
        $jqbh_end4 = isset($content['condition']['jqbh_end4'])?$content['condition']['jqbh_end4']:"";
     	$jqbh_end4 = xssValidation($jqbh_end4);
	}
	
	$params = $jqjssj.$jqbh_end4;
	if(!sqlInjectValidation($params)){
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
		$orgCode = '210200000000';
		$jqclzt ='5';
		$page ="1";
		$rows ="10";
	}
	if ($orgCode==""||$jqclzt=="") {
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
		
	$event = new EventMix();//创建调度类实例
	$res = $event->getOverEvent($orgCode,$jqclzt,$page,$rows,$xwh,$jqjssj,$bjsj,$zdjq,$jqbh_end4);//调用实例方法
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