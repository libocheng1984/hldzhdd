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
 * ���ܣ�ƽ̨��ѯԤ���б�
 * 	{head:{code:1/0,message=""},value:{},extend:""}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/mrzb.class.php');
	
	/*��½��ʱУ��*/
	if (!isset ($_SESSION["userId"])) {
	$arr = array (
		'head' => array (
					'code' => 9,
					'message' => '��¼��ʱ'
					),
		'value' => '',
		'extend' => ''
	);
		die(encodeJson($arr));
	}
	
	/*��ѯ*/
	/*�������*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	$zbqsrq="";$zbjgts="";$Gid="";$kaishishijian="";$jieshushijian="";
	if(isset($content['value'])){
		$zbqsrq = isset($content['value']['PAIBANGUIZHE']['startDay'])?iconv("UTF-8","GBK",$content['value']['PAIBANGUIZHE']['startDay']):"";
		$zbjgts = isset($content['value']['PAIBANGUIZHE']['loopStep'])?$content['value']['PAIBANGUIZHE']['loopStep']:"";
		$jieshushijian = isset($content['value']['JIESHUSHIJIAN'])?$content['value']['JIESHUSHIJIAN']:"";
                $kaishishijian= isset($content['value']['KAISHISHIJIAN'])?iconv("UTF-8","GBK",$content['value']['KAISHISHIJIAN']):"";
		$Gid= isset($content['condition']['id'])?$content['condition']['id']:"";
	}
        //print_r($zzzh);
        if($zbqsrq==""||$zbjgts==""||$jieshushijian==""||$kaishishijian==""||$Gid=="")
        {
            $arr = array (
				'head' => array (
					'code' => 0,
					'message' => 'ȱ�ٲ���!!'
				),
				'value' => '',
				'extend' => ''
			);
		die(encodeJson($arr));
        }
		
		
	$mrzb = new mrzb();//����������ʵ��
	$res =$mrzb->insertMrzb($Gid, $zbqsrq, $zbjgts, $kaishishijian, $jieshushijian);
	
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