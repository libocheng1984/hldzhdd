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
	include_once('../class/getUserWeb.class.php');
	/**
	 * 添加和修改巡逻路线接口
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
	$content = Json_decode($content,true);
	$id="";$parentOrgCode="";
	if(isset($content['condition'])){
		$id = isset($content['condition']['id'])?$content['condition']['id']:"";
		$parentOrgCode = isset($content['condition']['parentOrgCode'])?$content['condition']['parentOrgCode']:"";
		
	}

	/*调试*/
	if (isDebug()) {
		
	}
	if ($id==""||$parentOrgCode=="") {
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
		
	$getUser = new getUserWeb();//创建调度类实例
	$res = $getUser->updateParentOrg($id,$parentOrgCode);//调用实例方法
	if($res){
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => "操作成功",
				'extend' => ''
			);	
	}else{
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => "操作失败",
				'extend' => ''
			);
	}
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>

