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
 * 功能：获取巡逻组
 * 
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Communication.class.php');
	
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
	$orgCode = "";$userName="";$id="";$groupArray = array();
	if(isset($content['condition'])){
		$orgCode = isset($content['condition']['orgCode'])?$content['condition']['orgCode']:"";
		$id = isset($content['condition']['id'])?$content['condition']['id']:"";
		$groupArray  = isset($content['condition'])?$content['condition']:array();
	}
	
	/*调试*/
	if (isDebug()) {
		$orgCode = '210200000000';
		//$id = '210203440000';
	}
		if($event=="getlist"){
			/*参数校验*/
			if ($orgCode=="") {
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
			$communication = new Communication();//创建tpms数据库实例getImageInfo($img)
			$res=$communication->getContactsGroupsByOrg($orgCode);
		}else if($event=="getuser"){
			/*参数校验*/
			if ($id=="") {
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
			$communication = new Communication();//创建tpms数据库实例getImageInfo($img)
			$res=$communication->getContactsGroupsById($id);
		}else if($event=="getalluser"){
			/*参数校验*/
			if (count($groupArray)<=0) {
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
			$communication = new Communication();//创建tpms数据库实例getImageInfo($img)
			$res=$communication->getAllContactsGroupsById($groupArray);
		}
		$res = array (
		'head' => array (
					'code' => 1,
					'message' => ''
					),
		'value' => $res,
		'extend' => ''
		);
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
		
  

?>