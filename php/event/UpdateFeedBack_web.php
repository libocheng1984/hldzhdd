<?php
	error_reporting(E_ALL || ~E_NOTICE);
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
	session_start();
	session_commit();
?>
<?php
/**
 * 功能：平台根据警情ID查询警情详细
 * 	{head:{code:1/0,message=""},value:{},extend:""}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Event.class.php');
	
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
	$jqid="";$cjdbh="";$zlbh="";$cjld="";$jqjqzb="";$orgcode="";$jqdd="";$cljg="";$ssrs="";$swrs="";$zhrs="";$jjss="";$jzrs="";
	$jjfvrs="";$jjetrs="";$belong="";$scene="";
	if(isset($content['condition'])){
		$jqid = isset($content['condition']['jqid'])?$content['condition']['jqid']:"";
		$cjdbh = isset($content['condition']['cjdbh'])?$content['condition']['cjdbh']:"";
		$zlbh = isset($content['condition']['zlbh'])?$content['condition']['zlbh']:"";
		$cjld = isset($content['condition']['cjld'])?$content['condition']['cjld']:"";
		$jqjqzb = isset($content['condition']['jqjqzb'])?$content['condition']['jqjqzb']:"";
		$orgcode = isset($content['condition']['orgcode'])?$content['condition']['orgcode']:"";
		$jqdd = isset($content['condition']['jqdd'])?$content['condition']['jqdd']:"";
		$jqdd = xssValidation($jqdd);
		$cljg= isset($content['condition']['cljg'])?$content['condition']['cljg']:"";
		$cljg = xssValidation($cljg);
		$ssrs = isset($content['condition']['ssrs'])?$content['condition']['ssrs']:"";
		$ssrs = xssValidation($ssrs);
		$swrs = isset($content['condition']['swrs'])?$content['condition']['swrs']:"";
		$swrs = xssValidation($swrs);
		$zhrs = isset($content['condition']['zhrs'])?$content['condition']['zhrs']:"";
		$zhrs = xssValidation($zhrs);
		$jjss = isset($content['condition']['jjssqk'])?$content['condition']['jjssqk']:"";
		$jjss = xssValidation($jjss);
		$jzrs = isset($content['condition']['jzrs'])?$content['condition']['jzrs']:"";
		$jzrs = xssValidation($jzrs);
		$jjfvrs = isset($content['condition']['jjfvrs'])?$content['condition']['jjfvrs']:"";
		$jjfvrs = xssValidation($jjfvrs);
		$jjetrs = isset($content['condition']['jjetrs'])?$content['condition']['jjetrs']:"";
		$jjetrs = xssValidation($jjetrs);
		$belong = isset($content['condition']['belong'])?$content['condition']['belong']:"";
		$scene = isset($content['condition']['scene'])?$content['condition']['scene']:"";
	}
	$params = $jqdd.$cljg.$ssrs.$swrs.$zhrs.$jjss.$jzrs.$jjfvrs.$jjetrs;
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
	
		
	/*调试*/
	if (isDebug()) {
		$jqid = '6';
		//$lastTime='2015-12-12 16:17:21';
	}
	
	if ($jqid=="") {
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
		
	$event = new Event();//创建调度类实例
	$result1=false;$result2=false;$result3=false;$result4=false;$errmsg = "";
	$res = $event->insertPeFeedbackCase($jqid,$cjdbh, $zlbh, $cljg, "", "", $ssrs, $swrs, $zhrs, $jjss, $jzrs, $jjfvrs, $jjetrs); //调用实例方法
	if($res){
		$result2=$res;
		$errmsg ="反馈信息失败";
		$res = $event->insertPeFeedback($jqid,$cjdbh,$_SESSION["orgCode"],$_SESSION["userId"],$_SESSION["userName"],$belong,$scene,"1"); //调用实例方法
		if($res){
			$result3=$res;
			$errmsg ="处理指令信息失败";
			$res = $event->updateCommanMessage($zlbh, $cljg,$cjld);
			if($res){
				$result4=$res;
				$res=$event->overEventStatus($jqid,$cjdbh,"5",iconv("UTF-8","GBK",$jqdd),$jqjqzb,$_SESSION["userId"],iconv("UTF-8","GBK",$_SESSION["userName"]));
				if($res['result']=='true'){
					$result1=true;
					$errmsg ="";
				}else{
					$errmsg = $res['errmsg'];
				}
			}
		}
	}else{
		$errmsg ="反馈案件信息失败";
	}
	//$res = $event->insertZDTPeProcess($jqid);
	if($result1&&$result2&&$result3&&$result4){
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => true,
				'extend' => ''
			);
	}else{
		$result = array (
				'head' => array (
							'code' => 0,
							'message' => $errmsg
							),
				'value' => false,
				'extend' => ''
			);
	}
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>