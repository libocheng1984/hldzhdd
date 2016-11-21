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
 * 功能：终端上传图片信息
 * 	{result:"true或false",errmsg:"操作信息",records:[]}
 */
include_once ('../GlobalConfig.class.php');
include_once ('../class/TpmsDB.class.php');
include_once ('../class/CommandDB.class.php');
include_once ('../class/Event.class.php');

/*获取参数*/
$userId = isset( $_REQUEST['userId'] ) ? $_REQUEST['userId'] : "";
$userName = isset( $_REQUEST['userName'] ) ? $_REQUEST['userName'] : "";
$orgCode = isset( $_REQUEST['orgCode'] ) ? $_REQUEST['orgCode'] : "";
$jqid = isset ($_REQUEST['jqid']) ? $_REQUEST['jqid'] : "";
$type = isset ($_REQUEST['type']) ? $_REQUEST['type'] : "";
$jqcljg = isset ($_REQUEST['jqcljg']) ? $_REQUEST['jqcljg'] : "";
$cjdbh = isset ($_REQUEST['cjdbh']) ? $_REQUEST['cjdbh'] : "";
$zlbh = isset ($_REQUEST['zlbh']) ? $_REQUEST['zlbh'] : "";
$ssrs="";$swrs="";$zhrs="";$jjss="";$jzrs="";$jjfvrs="";$jjetrs="";$cjld="";
$ssrs = isset ($_REQUEST['ssrs']) ? $_REQUEST['ssrs'] : "";
$ssrs = xssValidation($ssrs);
$swrs = isset ($_REQUEST['swrs']) ? $_REQUEST['swrs'] : "";
$swrs = xssValidation($swrs);
$zhrs = isset ($_REQUEST['zhrs']) ? $_REQUEST['zhrs'] : "";
$zhrs = xssValidation($zhrs);
$jjss = isset ($_REQUEST['jjss']) ? $_REQUEST['jjss'] : "";
$jjss = xssValidation($jjss);
$jzrs = isset ($_REQUEST['jzrs']) ? $_REQUEST['jzrs'] : "";
$jzrs = xssValidation($jzrs);
$jjfvrs = isset ($_REQUEST['jjfvrs']) ? $_REQUEST['jjfvrs'] : "";
$jjfvrs = xssValidation($jjfvrs);
$jjetrs = isset ($_REQUEST['jjetrs']) ? $_REQUEST['jjetrs'] : "";
$jjetrs = xssValidation($jjetrs);
$belong = isset ($_REQUEST['belong']) ? $_REQUEST['belong'] : "";
$scene = isset ($_REQUEST['scene']) ? $_REQUEST['scene'] : "";
$cjld = isset ($_REQUEST['cjld']) ? $_REQUEST['cjld'] : "";
$cjld = xssValidation($cjld);

$params = $ssrs.$swrs.$zhrs.$jjss.$jzrs.$jjfvrs.$jjetrs.$cjld;
if(!sqlInjectValidation($params)){
	$arr = array (
			'result' => 'false',
			'errmsg' => '输入的内容有误!'
		);
	die(encodeJson($arr));
}

/*调试*/
if (isDebug()) {
	$jqid = '5';
	$cjdbh = '208';
	$type = '1';
}

if ($jqid == "" || $type == "" || $cjdbh == "") {
	$arr = array (
		'result' => 'false',
		'errmsg' => '缺少参数!'
	);
	die(encodeJson($arr));
}

$year = date('Y');
$month = date('m');
$day = date('d');
$event = new Event(); //创建调度类实例
$res = $event->getEventSimpleByid($jqid); //调用实例方法
if($res['jqclzt']=="5"){
	$arr = array('result' =>'false', 'errmsg' =>'警情已结束，不能再进行上传');
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	return;
}
if ($type == '1') { //上传图片
	$width = '';
	$height = '';
	//echo "图片是否存在：".!is_uploaded_file($_FILES["data"]['name']);
	//echo "sdfasdfasd".basename($_FILES['data']['name']);
	$files_datas = isset($_FILES['data'])?$_FILES['data']:"";
	if(!$files_datas){
		$ret ="操作成功";
		$res = $event->insertPeFeedbackCase($jqid,$cjdbh, $zlbh, $jqcljg, $type, "", $ssrs, $swrs, $zhrs, $jjss, $jzrs, $jjfvrs, $jjetrs); //调用实例方法
		if(!$res)$ret="插入案件数据失败";
		$res = $event->insertPeFeedback($jqid,$cjdbh,$orgCode,$userId,$userName,$belong,$scene,"0"); //调用实例方法
		if(!$res)$ret="插入反馈数据失败";
			$event->updateCommanMessage($zlbh, $jqcljg,$cjld);
		$result = $event->getPeFeedbackByid($jqid); //调用实例方法
		$result_jqcljg = isset($result['jqcljg'])?$result['jqcljg']:"";
		$result_cjld = isset($result['cjld'])?$result['cjld']:"";
		$datas = array (
			'result' => 'true',
			'errmsg' => $ret,
			'jqcljg' => $result_jqcljg,
			'cjld' => $result_cjld
		);
		echo json_encode($datas, JSON_UNESCAPED_UNICODE);
		return;
	}
	
	$base_path = GlobalConfig :: getInstance()->upload_src . 'picture/'; //接收文件目录  	
	$base_path_year = $base_path . $year . '/';
	$base_path_month = $base_path_year . $month . "/";
	$base_path_day = $base_path_month . $day . "/";
	$base_path_cjdbh = $base_path_day . $cjdbh . "/";
	$target_path = $base_path_cjdbh . basename($_FILES['data']['name']);
		

	//上传源文件
	//检查文件路径是否存在，不存在则创建
	if (!file_exists($base_path)) {
		if (!mkdir($base_path, 0777, true)) {
			die('无法建立路径');
		}
	}
	if (!file_exists($base_path_year)) {
		if (!mkdir($base_path_year, 0777, true)) {
			die('无法建立路径');
		}
	}
	if (!file_exists($base_path_month)) {
		if (!mkdir($base_path_month, 0777, true)) {
			die('无法建立路径');
		}
	}

	if (!file_exists($base_path_day)) {
		if (!mkdir($base_path_day, 0777, true)) {
			die('无法建立路径');
		}
	}

	if (!file_exists($base_path_cjdbh)) {
		if (!mkdir($base_path_cjdbh, 0777, true)) {
			die('无法建立路径');
		}
	}
	//开始上传文件
	if (move_uploaded_file($_FILES['data']['tmp_name'], $target_path)) {
		$ret = "操作成功";
		$dest_dir = $base_path_cjdbh;
		$src_file = $target_path;
		if (function_exists("zip_open")) {
			if (is_resource($zip = zip_open($src_file))) {
				while ($zip_entry = zip_read($zip)) {
					if (zip_entry_open($zip, $zip_entry, "r")) {
						$file_name = $dest_dir . zip_entry_name($zip_entry);
						$fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
						if (!is_dir($file_name)) {
							file_put_contents($file_name, $fstream);
						}
						if (file_exists($file_name)) {
							chmod($file_name, 0777);
							//$ret = "文件地址:" . $file_name;
						}
						zip_entry_close($zip_entry);
						$zlType = substr($file_name,strlen($file_name)- 3,3);
						if($zlType=="mp3"){
							$res = $event->insertPeFeedbackResource($cjdbh, $zlbh, "2", $file_name); //调用实例方法
						}else{
							$res = $event->insertPeFeedbackResource($cjdbh, $zlbh, "1", $file_name); //调用实例方法
						}
					}
				}
				zip_close($zip);
				unlink($src_file);
			} else {
				$ret = "ZIP扩展未支持";
			}
			$res = $event->insertPeFeedbackCase($jqid,$cjdbh, $zlbh, $jqcljg, $type, $target_path, $ssrs, $swrs, $zhrs, $jjss, $jzrs, $jjfvrs, $jjetrs); //调用实例方法
			if(!$res)$ret="插入案件数据失败";
			$res = $event->insertPeFeedback($jqid,$cjdbh,$orgCode,$userId,$userName,$belong,$scene,"0"); //调用实例方法
			if(!$res)$ret="插入反馈数据失败";
				$event->updateCommanMessage($zlbh, $jqcljg,$cjld);
		} else {
			if (version_compare(phpversion(), "5.2.0", "<"))
				$infoVersion = "(use PHP 5.2.0 or later)";
			$ret = "需要最新的zip扩展支持";
		}

	}
	$result = $event->getPeFeedbackByid($jqid); //调用实例方法
	$result_jqcljg = isset($result['jqcljg'])?$result['jqcljg']:"";
	$result_cjld = isset($result['cjld'])?$result['cjld']:"";
	$datas = array (
		'result' => 'true',
		'errmsg' => $ret,
		'jqcljg' => $result_jqcljg,
		'cjld' => $result_cjld
	);
	echo json_encode($datas, JSON_UNESCAPED_UNICODE);
} else {
	$datas = array (
		'result' => 'false',
		'errmsg' => '上传图片失败'
	);
	echo json_encode($datas, JSON_UNESCAPED_UNICODE);
}

//类方法
?>