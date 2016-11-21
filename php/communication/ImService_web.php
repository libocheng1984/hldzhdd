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
date_default_timezone_set('Asia/Shanghai'); //设置时区
// 设置请求运行时间不限制，解决因为超过服务器运行时间而结束请求
ini_set("max_execution_time", "270");

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
}else{
	$mem = new Memcache;
	$mem->connect(GlobalConfig::getInstance()->memcache_ip,GlobalConfig::getInstance()->memcache_port);	
	$sessionCookeId = session_id();
	
	$userId = isset($_SESSION["userId"])?$_SESSION["userId"]:"";
	$userdata = $mem->get($userId. '_webOnline');
	if (!($userdata==false||$userdata == "" || $userdata == '[]')) {
		$userdata = json_decode($userdata,true);
		$oldSessionId = $userdata['sessionId'];
		if($oldSessionId!=$sessionCookeId){
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
	}else{
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
}

/*查询*/
$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
$content = Json_decode($content, true);

if (isDebug()) {
	$event = 'newgroup';
}

if ($event == "newgroup") {
	$qzmc = "";
	$cjrId = "";
	$qzcy = "";
	$gids = "";
	$groupid = "";
	if (isset ($content['condition'])) {
		$qzmc = isset ($content['condition']['qzmc']) ? $content['condition']['qzmc'] : "";
		$qzmc = xssValidation($qzmc);
		$cjrId = isset ($content['condition']['cjrId']) ? $content['condition']['cjrId'] : "";
		$qzcy = isset ($content['condition']['qzcy']) ? $content['condition']['qzcy'] : "";
		$gids = isset ($content['condition']['gids']) ? $content['condition']['gids'] : "";
		$jqbh = isset ($content['condition']['jqbh']) ? $content['condition']['jqbh'] : "";
		$groupid = isset ($content['condition']['groupid']) ? $content['condition']['groupid'] : "";
	}
	
	if(!sqlInjectValidation($qzmc)){
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

	/*调试*/
	if (isDebug()) {
		$qzmc = "常用组";
		$cjrId = "110101195209263558";
		$qzcy = "110101195209263558";
		$jqbh = "";
		$groupid = "11";
		$groupname = "常用组";
		$jingqing = "";
		$gids = "";
	}
	/*参数校验*/
	if ($qzmc == "" || ($qzcy == "" && $gids == "" && $groupid == "")) {
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
	$res = $communication->createGroups($qzmc, $cjrId, $qzcy, $gids, $jqbh, $groupid);
	if ($res['result'] == 'true') {
		//发送消息
		$msg = $_SESSION["userName"] . "邀请您加入" . $qzmc;
		$resSend = $communication->sendOtherMsg($res['groupid'], $msg, $_SESSION["userId"], $_SESSION["userName"], "9");
		$res = $communication->getGroupByGroupid("", $res['groupid']);
		$userList = $res['records']["userlist"];
		for ($i = 0; $i < count($userList); $i++) {
			if ($userList[$i]['userId'] == $_SESSION["userId"])
				continue;
			$msgData = array (
				//"msg"=>"",
	"msg" => array (
					"groupid" => $res['records']['groupid'],
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
		if ($res['records'] && $res['records']['userId'] == $_SESSION["userId"]) {
			$res['records']['iscreator'] = true;
		} else {
			$res['records']['iscreator'] = false;
		}
		$res = array (
			'head' => array (
				'code' => 1,
				'message' => ''
			),
			'value' => $res['records'],
			'extend' => ''
		);
	} else {
		$res = array (
			'head' => array (
				'code' => 0,
				'message' => $res['errmsg']
			),
			'value' => '',
			'extend' => ''
		);
	}
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
} else
	if ($event == "getgroup") {
		$gid = "";
		if (isset ($content['condition'])) {
			$groupid = isset ($content['condition']['groupid']) ? $content['condition']['groupid'] : "";
		}

		/*调试*/
		if (isDebug()) {
			$groupid = '389';
		}
		/*参数校验*/
		if ($groupid == "") {
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
		$res = $communication->getGroupByGroupid($_SESSION["userId"], $groupid);
		if ($res['records'] && $res['records']['userId'] == $_SESSION["userId"]) {
			$res['records']['iscreator'] = true;
		} else {
			$res['records']['iscreator'] = false;
		}
		$res = array (
			'head' => array (
				'code' => 1,
				'message' => ''
			),
			'value' => $res['records'],
			'extend' => ''
		);
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
	} else if ($event == "addren") {
	$groupid = "";
	$qzmc = "";
	$qzcy = "";
	$gids = "";
	if (isset ($content['condition'])) {
		$users = $content['condition']['users'];
		if (isset ($users)) {
			$groupid = isset ($users['groupid']) ? $users['groupid'] : "";
			$qzmc = isset ($users['qzmc']) ? $users['qzmc'] : "";
			$qzcy = isset ($users['qzcy']) ? $users['qzcy'] : "";
			$gids = isset ($users['gids']) ? $users['gids'] : "";
		}
	}

	/*调试*/
	if (isDebug()) {
		$qzmc = '群组2';
		$cjrId = '110101195209263558';
		$qzcy = '210225197512280456,210203195411060019';
		$groupid = '381';
		$gids = "107";
	}
	/*参数校验*/
	if ($groupid == "" || ($qzcy == "" && $gids == "")) {
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
	$res = $communication->addMemberGroups($groupid, $qzmc, $qzcy, $gids, $_SESSION["userId"]);
	$res = array (
		'head' => array (
			'code' => 1,
			'message' => ''
		),
		'value' => $res,
		'extend' => ''
	);
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
} else if ($event == "delren") {
		$groupid = "";
		$qzcy = "";
		if (isset ($content['condition'])) {
			$groupid = isset ($content['condition']['groupid']) ? $content['condition']['groupid'] : "";
			$qzcy = isset ($content['condition']['guid']) ? $content['condition']['guid'] : "";
		}

		/*调试*/
		if (isDebug()) {
			$qzcy = '210225195009060211';
			$groupid = '388';
		}
		/*参数校验*/
		if ($groupid == "" || $qzcy == "") {
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
		$res = $communication->delMemberGroups($groupid, $qzcy, $_SESSION["userId"]);
		if ($res['result'] == 'true') {
			$res = array (
				'head' => array (
					'code' => 1,
					'message' => '操作成功'
				),
				'value' => $res['records'],
				'extend' => ''
			);
		} else {
			$res = array (
				'head' => array (
					'code' => 0,
					'message' => $res['errmsg']
				),
				'value' => '',
				'extend' => ''
			);
		}

		echo json_encode($res, JSON_UNESCAPED_UNICODE);
	} else if ($event == "quit") {
			$groupid = "";
			$qzcy = "";
			if (isset ($content['condition'])) {
				$groupid = isset ($content['condition']['groupid']) ? $content['condition']['groupid'] : "";
				$iscreator = isset ($content['condition']['delroup']) ? $content['condition']['delroup'] : "";
			}

			/*调试*/
			if (isDebug()) {
				$qzcy = '210225197512280456,210203195411060019';
				$groupid = '348';
				$iscreator = true;
			}
			/*参数校验*/
			if ($groupid == "") {
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
			$res = $communication->quitMemberGroups($groupid, $iscreator, $_SESSION["userId"]);
			if ($res['result'] == 'true') {
				$res = array (
					'head' => array (
						'code' => 1,
						'message' => '操作成功'
					),
					'value' => '',
					'extend' => ''
				);
			} else {
				$res = array (
					'head' => array (
						'code' => 0,
						'message' => $res['errmsg']
					),
					'value' => '',
					'extend' => ''
				);
			}

			echo json_encode($res, JSON_UNESCAPED_UNICODE);
		} else if ($event == "msg") {

	/*ceshi................................................*/
	//$testname = GlobalConfig :: getInstance()->test_src . $_SESSION["userId"] . '.txt';
	//$test_fp = fopen($testname, 'a');
	$time = time();
	$ip = isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:"" ;
	$userdata = array("sessionId"=>$sessionCookeId,"lastTime"=>$time,"ip"=>$ip);
	$mem->set($userId . '_webOnline', encodeJson($userdata));
	
	$datas = array ();
	$dataList = array ();
	$groups = array ();
	$filename = GlobalConfig :: getInstance()->message_src . $_SESSION["userId"] . '.txt';
	//$filename="//192.168.20.92/tpmsSocket/message/210211195904207015.txt";
	//echo $filename;	
	if (!file_exists($filename)) {
		$filename = fopen($filename, 'w');
		fclose($filename);
	}
	$currentmodif = date("Y-m-d H:i:s", filemtime($filename));
	$lastmodif = isset ($_SESSION['lasttime_msg']) ? $_SESSION['lasttime_msg'] : 0;
	$timer_start = microtime(true);
	clearstatcache(); // 清除文件状态缓存
	$sum = 0;
	//读取文件内容
	$line = 0;
	$response = array ();
	$fp = fopen($filename, "r");
	$txt_arr = file($filename);
	$content = @ fread($fp, filesize($filename));
	$content = trim($content);
	$clearContent = $content;
	if ($content=="") {
		while ($currentmodif <= $lastmodif) {
			//$data_time = date('y-m-d h:i:s',time());
			//fwrite($test_fp, $lastmodif.";currentmodif:".$currentmodif."sum:".$sum."--".$data_time."\r\n" );
			$timer_past = microtime(true)-$timer_start;
			session_start();
			session_commit();
			$sessionId = $_SESSION["isLayout"];
			if ($timer_past>60||$sessionId=="1") {
				$res = array (
					'head' => array (
						'code' => 1,
						'message' => ''
					),
					'value' => $datas,
					'extend' => $isContinContent
				);
				session_start();
				$_SESSION["lasttime_msg"] = $currentmodif;
				$_SESSION["isLayout"]="0";
				session_commit();
				//$arr = array('result' =>'true', 'errmsg' =>'','records'=>$datas);
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
				exit;
			}
			
//			if ($sum == 100||$sessionId=="1") {
//				$res = array (
//					'head' => array (
//						'code' => 1,
//						'message' => ''
//					),
//					'value' => $datas,
//					'extend' => ''
//				);
//				session_start();
//				$_SESSION["lasttime_msg"] = $currentmodif;
//				$_SESSION["isLayout"]="0";
//				session_commit();
//				//$arr = array('result' =>'true', 'errmsg' =>'','records'=>$datas);
//				echo json_encode($res, JSON_UNESCAPED_UNICODE);
//				exit;
//			}
			usleep(600000); // 休眠10ms释放cpu的占用
			clearstatcache(); // s清除文件状态缓存
			$currentmodif = date("Y-m-d H:i:s", filemtime($filename));
			$sum = $sum +1;
		}
	}

	//echo json_encode($txt_arr, JSON_UNESCAPED_UNICODE);
	//echo json_encode($content, JSON_UNESCAPED_UNICODE);
	if ($content != "" || $content != null) {
		//$test2 = json_encode($content, JSON_UNESCAPED_UNICODE);
		//fwrite($test_fp, $test2."1"."\r\n" );
		$content = explode("\r\n", $content);
		//文件最多显示200条
		$start = count($content)>100?count($content)-100:0;
		for ($i = $start; $i < count($content); $i++) {
			//$test1 = json_encode($content, JSON_UNESCAPED_UNICODE);
			//fwrite($test_fp, $test1."2"."start:".$start."trim:".trim($content[$i])."\r\n" );
		//for ($i = 0; $i < 200; $i++) {
			if (trim($content[$i]) != "") {
				$data = json_decode(trim($content[$i]), true);
				//$test_data = json_encode($data, JSON_UNESCAPED_UNICODE);
				//fwrite($test_fp, $test_data."3"."\r\n" );
				if (isset ($data['msg'])) {
					if (isset ($data['msg']['type']) && ($data['msg']['type'] == "2" || $data['msg']['type'] == "3"|| $data['msg']['type'] == "4"|| $data['msg']['type'] == "5")) {
						$PHP_SELF = $_SERVER['PHP_SELF'];
						$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
						$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
						$url = 'http://' . $_SERVER['HTTP_HOST'] . substr($PHP_SELF, 0, strrpos($PHP_SELF, '/') + 1) . 'php/uploads/';
						$data['msg']['chat'] = $url . $data['msg']['chat'];
						$data['msg']['src2'] = $url . $data['msg']['src2'];
					}
					array_push($datas, $data['msg']);
				}
				if (isset ($data['userlist']))
					array_push($dataList, $data['userlist']);
				if (isset ($data['groups']))
					array_push($groups, $data['groups']);

				
				//$test_data = json_encode($datas, JSON_UNESCAPED_UNICODE);
				//fwrite($test_fp, $test_data."4"."\r\n" );
			}
		}
	}
	flush();

	$sessionId = $_SESSION["isLayout"];
	if($sessionId=="1"){
		//fwrite($test_fp,"5"."\r\n" );
		$res = array (
				'head' => array (
					'code' => 1,
					'message' => ''
				),
				'value' => array (),
				'extend' => ''
			);
		session_start();
		$_SESSION["lasttime_msg"] = $lastmodif;
		$_SESSION["isLayout"]="0";
		session_commit();
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
	}else{
		//判断有无锁文件，有则删除 
		$lockname = GlobalConfig :: getInstance()->message_src . $_SESSION["userId"] . '.txt.lock';
		$lock_v = file_exists($lockname);
		//fwrite($test_fp, "6"."\r\n" );
		if ($lock_v) {
			//fwrite($test_fp, "7"."\r\n" );
			$res = array (
				'head' => array (
					'code' => 1,
					'message' => ''
				),
				'value' => array (),
				'extend' => ''
			);
			session_start();
			$_SESSION["lasttime_msg"] = $lastmodif;
			session_commit();
			echo json_encode($res, JSON_UNESCAPED_UNICODE);
		} else {
			//$test_data = json_encode($datas, JSON_UNESCAPED_UNICODE);
			//fwrite($test_fp, $test_data."8"."\r\n" );
			session_start();
			$_SESSION["lasttime_msg"] = $currentmodif;
			session_commit();
			$res = array (
				'head' => array (
					'code' => 1,
					'message' => ''
				),
				'value' => array (
					'msg' => $datas,
					'userlist' => $dataList,
					'groups' => $groups,
					'lasttime_new' => $_SESSION["lasttime_msg"],
					'lasttime_old' => $lastmodif
				),
				'extend' => $clearContent
			);
			try{
				//$test_data = json_encode($res, JSON_UNESCAPED_UNICODE);
				//fwrite($test_fp, $test_data."9"."\r\n" );
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
				//删除行同修改行一样，只不过是将原来行替换为同等长度的空格串
				if($clearContent!=""){
					$f = fopen($filename, 'r+');
					for($i=0;$i<count($content)&&!feof($f);$i++){
				        $row = fgets($f);
				        $len = strlen($row);
				        //echo ftell($f)-$len."";
				        fseek($f, ftell($f)-$len);
				        fwrite($f, str_pad(' ', $len, ' '));
				        //fwrite($f,"");
			   		 }
				    fclose($f);
				}
			    //$test_tatol = json_encode($content, JSON_UNESCAPED_UNICODE);
				//fwrite($test_fp, $test_tatol."\r\n");
				//fwrite($test_fp, "\r\n");
				//fclose($test_fp);
			}catch(Exception $e){
				$res = array (
					'head' => array (
						'code' => 0,
						'message' => '未知网络异常'
					),
					'value' => '',
					'extend' => ''
				);
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
			}
			
		}
	}
	
	fclose($fp); //关闭文件
} else if ($event == "send") {
	$groupid = "";
	$msg = "";$sendName="";
	if (isset ($content['condition'])) {
		$groupid = isset ($content['condition']['groupid']) ? $content['condition']['groupid'] : "";
		$msg = isset ($content['condition']['msg']) ? $content['condition']['msg'] : "";
		$msg = xssValidation($msg);
		$filename = isset ($content['condition']['filename']) ? $content['condition']['filename'] : "";
		$filename2 = isset ($content['condition']['filename2']) ? $content['condition']['filename2'] : "";
		$type = isset ($content['condition']['type']) ? $content['condition']['type'] : "";
		$size = isset ($content['condition']['size']) ? $content['condition']['size'] : "";
		$sendName = isset ($content['condition']['sendName']) ? $content['condition']['sendName'] : "";
	}
	
//	if(!sqlInjectValidation($msg)){
//		$arr = array (
//				'head' => array (
//					'code' => 0,
//					'message' => '输入的内容有误'
//				),
//				'value' => '',
//				'extend' => ''
//			);
//			die(encodeJson($arr));
//	}	

	/*调试*/
	if (isDebug()) {
		$qzcy = '210225197512280456,210203195411060019';
		$groupid = '348';
		$iscreator = true;
	}
	/*参数校验*/
	if ($groupid == "") {
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
	//发送图片	
	if ($type == "2") {
		$communication = new Communication(); //创建tpms数据库实例getImageInfo($img)
		$res = $communication->sendPicture($groupid, $_SESSION["userId"], $_SESSION["userName"], $size, $filename, $filename2,$sendName,$_SESSION["orgCode"],$_SESSION["orgName"]);
	} else if($type == "3"||$type == "4"||$type == "5"){
		$communication = new Communication(); //创建tpms数据库实例getImageInfo($img)
		$res = $communication->sendOthers($groupid, $_SESSION["userId"], $_SESSION["userName"], $size, $filename, $filename2,$type,$sendName,$_SESSION["orgCode"],$_SESSION["orgName"]);
	}else {
		$communication = new Communication(); //创建tpms数据库实例getImageInfo($img)
		$res = $communication->sendMemberGroups($groupid, $msg, $_SESSION["userId"], $_SESSION["userName"],$_SESSION["orgCode"],$_SESSION["orgName"]);
	}

	if ($res['result'] == 'true') {
		$res = array (
			'head' => array (
				'code' => 1,
				'message' => ''
			),
			'value' => '',
			'extend' => ''
		);
	} else {
		$res = array (
			'head' => array (
				'code' => 0,
				'message' => $res['errmsg']
			),
			'value' => '',
			'extend' => ''
		);
	}

	echo json_encode($res, JSON_UNESCAPED_UNICODE);
}else if($event="renamegroup"){
	$groupid = "";
	$qzmc = "";
	if (isset ($content['condition'])) {
		$groupid = isset ($content['condition']['groupid']) ? $content['condition']['groupid'] : "";
		$qzmc = isset ($content['condition']['newgroupname']) ? $content['condition']['newgroupname'] : "";
	}

	/*调试*/
	if (isDebug()) {
		$qzmc = '群组2';
		$groupid = '381';
	}
	/*参数校验*/
	if ($groupid == "" || $qzmc=="") {
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
	$res = $communication->reGroupName($groupid, $qzmc, $_SESSION["userId"]);
	if ($res['result'] == 'true') {
		$res = array (
			'head' => array (
				'code' => 1,
				'message' => ''
			),
			'value' => array(),
			'extend' => ''
		);
	} else {
		$res = array (
			'head' => array (
				'code' => 0,
				'message' => $res['errmsg']
			),
			'value' => '',
			'extend' => ''
		);
	}
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
}
?>