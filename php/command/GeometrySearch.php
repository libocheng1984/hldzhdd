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
		$operation = isset($content['condition']['operation'])?$content['condition']['operation']:"";
		$license = isset($content['condition']['license'])?$content['condition']['license']:"";
		$AddressName = isset($content['condition']['AddressName'])?$content['condition']['AddressName']:"";
		$AddressName = xssValidation($AddressName);
		$mp = isset($content['condition']['mp'])?$content['condition']['mp']:"";
		$mp = xssValidation($mp);
		$sc = isset($content['condition']['sc'])?$content['condition']['sc']:"";
		$sc = xssValidation($sc);
		$th = isset($content['condition']['th'])?$content['condition']['th']:"";
		$th = xssValidation($th);
	}
	
	$params = $AddressName.$mp.$sc.$th;
	
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

	
	if ($operation=="FullTextRetrieval_GetPointInfoByAddressName_v001") {
		if($AddressName!=""){
			$AddressName = urlencode($AddressName);
			$url = 'http://192.168.20.215:9999/lbs?operation='.$operation.'&license=' . $license . '&content={"data":[{"AddressName":"'.$AddressName. '"}],"pageindex":0,"pagesize":200}';
			$roleInfo = file_get_contents($url);
			//echo $url;
			$result = json_decode($roleInfo, true);
			$resultInfo = array (
				'head' => array (
					'code' => 1,
					'message' => ''
				),
				'value' => $result,
				'extend' => ''
			);
			echo json_encode($resultInfo, JSON_UNESCAPED_UNICODE);
		}else{
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
	}else if($operation=="FullTextRetrieval_IntersectionByStreetName_v001"){
		if($mp!=""){
			$mp = urlencode($mp);
			$sc = urlencode($sc);
			$th = urlencode($th);
			$url = 'http://192.168.20.215:9999/lbs?operation='.$operation.'&license=' . $license . '&content={"data":[{"Km":"0","mp":"'.$mp.'","sc":"' . $sc .'","th":"' . $th . '"}],"pageindex":0,"pagesize":200}';
			echo $url;
			$roleInfo = file_get_contents($url);
			$result = json_decode($roleInfo, true);
			$resultInfo = array (
				'head' => array (
					'code' => 1,
					'message' => ''
				),
				'value' => $result,
				'extend' => ''
			);
			echo json_encode($resultInfo, JSON_UNESCAPED_UNICODE);
		}else{
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
	
	}
	
	
?>