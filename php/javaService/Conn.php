<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: 0");
header("Cache-Control: no-cache");
header("Pragma content: no-cache");
session_start();
?>
<?php


/**
 * 功能：登陆系统
 * 
 * 输入参数：
 * 	userId: 警员编号
 *  password: 密码
 *  clientType: 客户端类别
 * 
 * 返回值：
 * 	{"result":false,"errmsg":"错误信息"} : 登陆失败
 * 	json串 : 成功
 */
include_once ('../GlobalConfig.class.php');
include_once ('../class/TpmsDB.class.php');
include_once ('../class/SystemDB.class.php');
include_once ('../class/javaServiceDB.class.php');

/*传入参数*/
$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
$operation = isset ($_REQUEST['operation']) ? $_REQUEST['operation'] : "";
$operation = "getOrganization";
if ($operation == "login") {
	$content = Json_decode($content,true);
	$userId="";$password="";$clientType="";
	if(isset($content['data'])){
		$userId = $content['data']['userId'];
		$password = $content['data']['password'];
		$clientType = $content['data']['clientType'];
	}
	/*必传参数校验*/
	if ($userId == "" || $password == "" || $clientType = "") {
		$result = array (
			'code' => "0",
			'message' => '缺少参数！',
			'value' => ''
		);
		die(encodeJson($result));
	}
	$systemdb = new SystemDB(); //创建系统类实例
	$res = $systemdb->LoginSystem($userId, $password); //调用实例方法LoginSystem登陆系统
	$result = "";
	if ($res == false) {
		$result = array (
			'code' => "0",
			'msg' => '用户名或密码错误,请重新输入！',
			'data' => $res
		);

	} else {
		$result = array (
			'code' => "1",
			'msg' => '',
			'data' => $res
		);
	}

	echo encodeJson($result);
}else if ($operation == "getOrganization") {

	//$content = Json_decode($content,true);
	$orgCode="210200210000";
	//if(isset($content['data'])){
	//	$orgCode = $content['data']['orgCode'];
	//}
	$javaService = new javaServiceDB(); //创建调度类实例
	$res = $javaService->getOrganization($orgCode); //调用实例方法
	$result = "";
		if ($res == false) {
			$result = array (
				'code' => "0",
				'msg' => '用户名或密码错误,请重新输入！',
				'data' => $res
			);
		
		} else {
			$result = array (
				'code' => "1",
				'msg' => '',
				'data' => $res
				);
		}
	echo encodeJson($result);
}
?>