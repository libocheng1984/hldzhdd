<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
?>
<?php
/**
 * ���ܣ��ն˲�ѯ����
 * 	{result:"true��false",errmsg:"������Ϣ",points:[]}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Event.class.php');
	
	$event = new Event();//����������ʵ��
	$res = $event->getOrgbyAir("SZZRQ","121.68832631333417","38.89187926782469");//����ʵ������
	echo "code==".$res['orgcode'];
	echo "name==".$res['orgname'];
?>