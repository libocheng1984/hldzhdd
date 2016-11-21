<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
?>
<?php
	/**
	 * 补充指令信息
	 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Event.class.php');
	

	
	/*查询*/
	/*传入参数*/
	$airType = isset ($_REQUEST['airType']) ? $_REQUEST['airType'] : "";
	$x = isset ($_REQUEST['x']) ? $_REQUEST['x'] : "";
	$y = isset ($_REQUEST['y']) ? $_REQUEST['y'] : "";
	if ($airType==""||$x==""||$y=="") {
		$arr = array (
				'orgcode' =>	"",
                                'orgname' =>	""
			);
		die(encodeJson($arr));
	}       
	$url='http://10.80.8.204:8090/JYYW/'.$airType.'.ashx?x='.$x.'&y='.$y.'&includeGeometry=0';
		
		//$url='http://10.80.8.204:8090/JYYW/SZXLQ.ashx?x=121.68832631333417&y=38.89187926782469&includeGeometry=0';
		$orgcode=""; 
		$orgname="";
		try{
		$xml = file_get_contents($url);
		$res = simplexml_load_string($xml);
		
		if($airType=='SZZRQ')
		{
			if(property_exists($res,'Result')&&property_exists($res->Result,'features')&&property_exists($res->Result->features,'feature')&&isset($res->Result->features->feature[0])&&property_exists($res->Result->features->feature[0],'attributes')&&property_exists($res->Result->features->feature[0]->attributes,'ORGCODE'))
			{
			$orgcode=$res->Result->features->feature[0]->attributes->ORGCODE;
			}
			if(property_exists($res,'Result')&&property_exists($res->Result,'features')&&property_exists($res->Result->features,'feature')&&isset($res->Result->features->feature[0])&&property_exists($res->Result->features->feature[0],'attributes')&&property_exists($res->Result->features->feature[0]->attributes,'ORGNAME'))
			{
				$orgname=$res->Result->features->feature[0]->attributes->ORGNAME;
				//echo $orgname;
			}
		}
		if($airType=='SZXLQ')
		{
			if(property_exists($res,'Result')&&property_exists($res->Result,'features')&&property_exists($res->Result->features,'feature')&&isset($res->Result->features->feature[0])&&property_exists($res->Result->features->feature[0],'attributes')&&property_exists($res->Result->features->feature[0]->attributes,'XQM'))
			{
				$orgname=$res->Result->features->feature[0]->attributes->XQM;
				//echo $orgname;
			}
			if(property_exists($res,'Result')&&property_exists($res->Result,'features')&&property_exists($res->Result->features,'feature')&&isset($res->Result->features->feature[0])&&property_exists($res->Result->features->feature[0],'attributes')&&property_exists($res->Result->features->feature[0]->attributes,'CODE'))
			{
				$orgcode=$res->Result->features->feature[0]->attributes->CODE;
				//echo $orgname;
			}
		}
          
					$men = array (
						'orgcode' =>	iconv("GBK", "UTF-8", $orgcode),
                        'orgname' =>	iconv("UTF-8","UTF-8",$orgname)
				); 
                    die(encodeJson($men));
		}catch(Exception $e)
		{
                    $men = array (
						'orgcode' =>	"",
                        'orgname' =>	""
				); 
                   die(encodeJson($men));
		}
	
?>