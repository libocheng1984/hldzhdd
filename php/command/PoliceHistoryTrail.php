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
 * 功能：查询警员历史轨迹
 * 
 * 输入参数：
 * mode=1	(必输项)
 * xh	  警员序号
 * startTime	最新定位开始时间
 * endTime	最新定位结束时间
 * 返回值：
 * 	{result:"true或false",errmsg:"错误信息",records:"[]"}
 *********************************************************
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/CommandDB.class.php');
	
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
	$userId="";$starttime="";$endtime="";
	if(isset($content['condition'])){
		$userId = isset($content['condition']['userId'])?$content['condition']['userId']:"";
		$starttime = isset($content['condition']['starttime'])?$content['condition']['starttime']:"";
		$endtime = isset($content['condition']['endtime'])?$content['condition']['endtime']:"";
	}

	/*调试*/
	if (isDebug()) {
		$jqid = '3';
		$rybh = "210225197512280456";
		//$lastTime='2015-12-12 16:17:21';
	}
//	$jgmc ="00";
	//$userId="210212196811011000";
	//$userId="210202196103024911";
	//$starttime="2015-07-28 18:06:25";
	//$endtime="2015-07-28 20:05:30";
	//echo "1111111";
	//exit;
	if ($userId==""||$starttime==""||$endtime=="") {
		$arr = array (
				'head' => array (
					'code' => 0,
					'message' => '请输入查询条件!!'
				),
				'value' => '',
				'extend' => ''
			);
		die(encodeJson($arr));
	}
	//"id":"1","userId":"210202196103024911","userId_text":"王毅","starttime":"2015-07-28 18:06:25","endtime":"2015-07-28 20:05:30"
	$tpmsdb = new CommandDB();//创建tpms数据库实例
	$res = $tpmsdb->getPoliceHistoryTrail($userId,$starttime,$endtime);//调用实例方法
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