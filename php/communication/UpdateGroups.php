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
$gid = isset ($_REQUEST['gid']) ? $_REQUEST['gid'] : ""; //群组名称
$qzmc = isset ($_REQUEST['qzmc']) ? $_REQUEST['qzmc'] : ""; //群组名称
$qzcy = isset ($_REQUEST['qzcy']) ? $_REQUEST['qzcy'] : ""; //群组成员
$gids = isset ($_REQUEST['gids']) ? $_REQUEST['gids'] : ""; //巡逻组成员
$userId = isset ($_REQUEST['userId']) ? $_REQUEST['userId'] : ""; //巡逻组成员

/*调试*/
if (isDebug()) {
	$qzmc = '群组1';
	$cjrId = '110101195209263558';
	$qzcy = '210225197512280456,210203195411060019';
	$gid = '922';
	$userId='210225197512280456';
	//$gids = "104";
}
/*参数校验*/
if ($gid == "" || $qzmc == "" || ($qzcy == "" && $gids == "")) {
	$arr = array (
				'result' => 'false',
				'errmsg' =>  '缺少参数!!',
			);
	die(encodeJson($arr));
}

$communication = new Communication(); //创建tpms数据库实例getImageInfo($img)
$res = $communication->AndroidUpdateGroup($gid, $qzmc, $qzcy, $gids, $userId);
echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>