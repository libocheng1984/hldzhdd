<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-cache");
header("Pragma content: no-cache");
session_start();
session_commit();
?>
<?php


include_once ('../GlobalConfig.class.php');
include_once ('../class/TpmsDB.class.php');
include_once ('../class/Communication.class.php');

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
$content = Json_decode($content, true);
$id = "";$qzmc = "";$cjrId = "";$qzcy = "";$gids = "";
if (isset ($content['condition'])) {
	$id = isset ($content['condition']['id']) ? $content['condition']['id'] : "";
	$qzmc = isset ($content['condition']['qzmc']) ? $content['condition']['qzmc'] : "";
	$cjrId = isset ($content['condition']['cjrId']) ? $content['condition']['cjrId'] : "";
	$qzcy = isset ($content['condition']['qzcy']) ? $content['condition']['qzcy'] : "";
}
if (isDebug()) {
	$event = "select";
}
if ($event == "create") {
	/*调试*/
	if (isDebug()) {
		$qzmc = '群组1';
		$cjrId = '110101195209263558';
		$qzcy = '210225197512280456,210203195411060019';
		//$gids = "104";
	}
	/*参数校验*/
	if ($qzmc == "" || $qzcy == "") {
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

	$communication = new Communication(); //创建tpms数据库实例getImageInfo($img)
	$res = $communication->createCommonGroups($qzmc, $cjrId, $qzcy);
	$res = array (
		'head' => array (
			'code' => 1,
			'message' => ''
		),
		'value' => $res,
		'extend' => ''
	);
	echo json_encode($res, JSON_UNESCAPED_UNICODE);

} else	if ($event == "getlist") {
		$communication = new Communication(); //创建tpms数据库实例getImageInfo($img)
		$res = $communication->getCommonGroups($_SESSION["orgCode"],$_SESSION["userId"]);
		$res = array (
			'head' => array (
				'code' => 1,
				'message' => ''
			),
			'value' => $res,
			'extend' => ''
		);
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
} else if ($event == "delete") {
		/*参数校验*/
		if ($id == "") {
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
		$communication = new Communication(); //创建tpms数据库实例getImageInfo($img)
		$res = $communication->deleteCommonGroupsById($id);
		$res = array (
			'head' => array (
				'code' => 1,
				'message' => ''
			),
			'value' => $res,
			'extend' => ''
		);
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
} else if ($event == "getuser") {
		/*参数校验*/
		if ($id == "") {
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
		$communication = new Communication(); //创建tpms数据库实例getImageInfo($img)
		$res = $communication->getCommonGroupsById($id);
		$res = array (
			'head' => array (
				'code' => 1,
				'message' => ''
			),
			'value' => $res,
			'extend' => ''
		);
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
} else if ($event == "savegroup") {
		$users = isset ($content['value']['users']) ? $content['value']['users'] : "";
		$groupname = isset ($content['value']['groupname']) ? $content['value']['groupname'] : "";
		/*调试*/
		if (isDebug()) {
			$qzmc = '群组1';
			$cjrId = '110101195209263558';
			$qzcy = '210225197512280456,210203195411060019';
			//$gids = "104";
		}
		//echo json_encode($users);
		$userIds = "";
		for ($i = 0; $i < count($users); $i++) {
			if ($i != 0) {
				$userIds = $userIds . "," . $users[$i]['userId'];
			} else {
				$userIds = $users[$i]['userId'];
			}
		}
		/*参数校验*/
		if ($groupname == "" || $userIds == "") {
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

		$communication = new Communication(); //创建tpms数据库实例getImageInfo($img)
		$res = $communication->createCommonGroups($groupname, $_SESSION["userId"], $userIds);
		$res = array (
			'head' => array (
				'code' => 1,
				'message' => ''
			),
			'value' => $res,
			'extend' => ''
		);
		echo json_encode($res, JSON_UNESCAPED_UNICODE);

}
?>