<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: 0");
header("Cache-Control: no-cache");
header("Pragma content: no-cache");
session_start();
session_commit();
?>
<?php

/**
 * 功能：获取巡逻组
 * 
 */
include_once ('../GlobalConfig.class.php');
include_once ('../class/TpmsDB.class.php');
include_once ('../class/Communication.class.php');

/*查询*/
$qzmc = isset ($_REQUEST['qzmc']) ? $_REQUEST['qzmc'] : ""; //群组名称
$cjrId = isset ($_REQUEST['cjrId']) ? $_REQUEST['cjrId'] : ""; //创建人ID
$cjrName = isset ($_REQUEST['cjrName']) ? $_REQUEST['cjrName'] : ""; //创建人ID
$qzcy = isset ($_REQUEST['qzcy']) ? $_REQUEST['qzcy'] : ""; //群组成员
$gids = isset ($_REQUEST['gids']) ? $_REQUEST['gids'] : ""; //巡逻组成员
/*调试*/
if (isDebug()) {
	$qzmc = '群组1';
	$cjrId = '110101195209263558';
	$qzcy = '210225197512280456,210203195411060019';
	//$gids = "104";
}
/*参数校验*/
if ($qzmc == "" || ($qzcy == "" && $gids == "")) {
	$arr = array (
				'result' => 'false',
				'errmsg' =>  '缺少参数!!',
			);
	die(encodeJson($arr));
}

$communication = new Communication(); //创建tpms数据库实例getImageInfo($img)
$res = $communication->createGroups($qzmc, $cjrId, $qzcy, $gids, "", "");
if ($res['result'] == 'true') {
	//发送消息
	$msg = $cjrName . "邀请您加入" . $qzmc;
	$resSend = $communication->sendOtherMsg($res['groupid'], $msg, $cjrId, $cjrName, "9");
	$res_get = $communication->getGroupByGroupid("", $res['groupid']);
	$userList = $res_get['records']["userlist"];
	for ($i = 0; $i < count($userList); $i++) {
		if ($userList[$i]['userId'] == $cjrId)
			continue;
		$msgData = array (
			//"msg"=>"",
			"msg" => array (
				"groupid" => $res_get['records']['groupid'],
				"type" => "9",
				"chat" => $msg
			)
		);
		$userStr = json_encode($msgData, JSON_UNESCAPED_UNICODE);
		$userid = $userList[$i]['userId'];
		$filename = GlobalConfig :: getInstance()->message_src . $userid . '.txt';
		//判断新号文件是否存在,不存在则创建
		$file_path = GlobalConfig :: getInstance()->message_src;
		$file = GlobalConfig :: getInstance()->message_src . $userid . '.txt';
		if (!file_exists($file_path)) {
			$TpmsDB = new TpmsDB(); //创建tpms数据库实例
			$res1 = $TpmsDB->mkdirs($file_path);
			$file = fopen($file, 'w');
			fclose($file);
		}

		$filename = fopen($filename, 'a');
		fwrite($filename, $userStr);
		fwrite($filename, "\r\n");
		fclose($filename);
	}

}
echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>