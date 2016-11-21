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
	include_once('../class/TextRetrieval.class.php');
	
	
	/*查询*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	$address;
  $orgcode;
        
	if(isset($content['condition'])){
		$address = isset($content['condition']['address'])?$content['condition']['address']:"";
                $orgcode = isset($content['condition']['orgcode'])?$content['condition']['orgcode']:"";
	}
	
		
	/*调试*/
	if (isDebug()) {
		$jqid = '7';
		//$lastTime='2015-12-12 16:17:21';
	}
	
	if ($address==""||$orgcode=="") {
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
		
	$TextRetrieval = new TextRetrieval();//创建调度类实例
	$res = $TextRetrieval->getDefaultLocation($address, $orgcode);//调用实例方法
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