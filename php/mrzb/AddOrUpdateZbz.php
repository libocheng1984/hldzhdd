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
 * 功能：平台查询预案列表
 * 	{head:{code:1/0,message=""},value:{},extend:""}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/mrzb.class.php');
	
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
	/*传入参数*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	$zzxm="";$zzzh="";$Gid="";$zcyzh="";$zbzdh="";
        $orgCode=$_SESSION["orgCode"];
	if(isset($content['value'])){
		$zzxm = isset($content['value']['zz_text'])?iconv("UTF-8","GBK",$content['value']['zz_text']):"";
		$zzzh = isset($content['value']['zz'])?$content['value']['zz']:"";
		$zcyzh = isset($content['value']['zcyzh'])?$content['value']['zcyzh']:"";
                $zbzdh= isset($content['value']['zbzdh'])?iconv("UTF-8","GBK",$content['value']['zbzdh']):"";
		$Gid= isset($content['condition']['id'])?$content['condition']['id']:"";
	}
        //print_r($zzzh);
        if($zzxm==""||$zzzh==""||$zcyzh=="")
        {
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
		
	
	;	
	$mrzb = new mrzb();//创建调度类实例
	$res =$mrzb->insertZbzcyb($zzxm, $zzzh, $zbzdh, $orgCode, $Gid, $zcyzh);
	
	if($res){
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => $res,
				'extend' => ''
			);	
	}else{
		$result = array (
				'head' => array (
							'code' => 0,
							'message' => ''
							),
				'value' => $res,
				'extend' => ''
			);
	}
	
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>