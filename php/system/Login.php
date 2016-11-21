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

/*传入参数*/
$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
$content = Json_decode($content,true);
$userId="";$password="";$clientType="";
if(isset($content['condition'])){
	$userId = array_key_exists('userId',$content['condition'])?xssValidation($content['condition']['userId']):"";
	$password = array_key_exists('password',$content['condition'])?xssValidation($content['condition']['password']):"";
	$clientType = array_key_exists('clientType',$content['condition'])?xssValidation($content['condition']['clientType']):"";
}
$params = $userId.$password.$clientType;
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
/*调试参数*/
if (isDebug()) {
	$userId = '210225197512280456';
	$password = '123456';
	$clientType = '1';
	$event="login";
}

if($event=="logout"){
	$mem = new Memcache;
	$mem->connect(GlobalConfig::getInstance()->memcache_ip,GlobalConfig::getInstance()->memcache_port);	
	$userId = isset ($_SESSION["userId"])?$_SESSION["userId"]:"";
	if($userId!="")$mem->delete($userId . '_webOnline');
	$_SESSION["isLayout"] = "1";
	unset($_SESSION["userId"]);
	unset($_SESSION["clientType"]);
	unset($_SESSION["userName"]);
	unset($_SESSION["userCode"]);
	unset($_SESSION["orgCode"]);
	unset($_SESSION["orgName"]);
	session_destroy();
	$arr = array (
		'head' => array (
					'code' => 1,
					'message' => ''
					),
		'value' => '',
		'extend' => ''
	);
	session_commit();
	die(encodeJson($arr));
}else{
	/*必传参数校验*/
	if ($userId=="" || $password=="" || $clientType="") {
		$arr = array (
			'head' => array (
				'code' => 1,
				'message' => '连接成功'
			),
			'value' => array (
				'status' => '缺少参数!'
			),
			'extend' => ''
		);
		die(encodeJson($arr));
	}
	$mem = new Memcache;
	$mem->connect(GlobalConfig::getInstance()->memcache_ip,GlobalConfig::getInstance()->memcache_port);	
	$sessionId = session_id();
	$time = time();
	$ip = isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:"" ; 
		$userdata = $mem->get($userId . '_webOnline');
		if (!($userdata==false||$userdata == "" || $userdata == '[]')) {
			$userdata = json_decode($userdata,true);
			$oldSessionId = $userdata['sessionId'];
			$oldTime = $userdata['lastTime'];
			$strTime = $time-$oldTime;
			//echo $strTime;
			//echo $oldSessionId;
			//echo $sessionId;
			if($strTime<120&&$oldSessionId!=$sessionId){
				$result = array (
					'head' => array (
								'code' => 0,
								'message' => '该用户已经登录'
								),
					'value' => '',
					'extend' => ''
				);
				die(encodeJson($result));	
			}
		}

	$systemdb = new SystemDB(); //创建系统类实例
	$res = $systemdb->LoginSystem($userId, $password); //调用实例方法LoginSystem登陆系统
	$result="";
	if ($res == false) {
			$result = array (
				'head' => array (
							'code' => 1,
							'message' => '连接成功!'
							),
				'value' => array (
							'status' => '用户名或密码错误,请重新输入！'
							),
				'extend' => ''
			);
	} else {
		//登陆信息写入session
		$_SESSION["userId"] = $userId;
		$_SESSION["clientType"] = $clientType;
		$_SESSION["userName"] = $res["userName"];
		$_SESSION["userCode"] = iconv("GBK", "UTF-8", $res["userCode"]);
		$_SESSION["orgCode"] = iconv("GBK", "UTF-8", $res["orgCode"]);
		$_SESSION["orgName"] = $res["orgName"];
		
		$userdata = array("sessionId"=>$sessionId,"lastTime"=>$time,"ip"=>$ip);
		$mem->set($userId . '_webOnline', encodeJson($userdata));
		//构建登陆成功返回数据	
		$res['status']='ok';
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => $res,
				'extend' => ''
			);	
	}
	session_commit();
	echo encodeJson($result);
}


?>