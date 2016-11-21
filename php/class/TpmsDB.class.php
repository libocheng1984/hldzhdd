<?php

class TpmsDB extends DbBaseClass {
	public $dbconn = null;
	
	public function __construct($dbconn=null) {
		$this->dbconn = $dbconn;
	}
	
	function __destruct() {
		if ($this->dbconn)
			$this->Logoff($this->dbconn);
	}
	/**************************************/
	/***************系统管理模块***************/
	/**************************************/
	/**
	 * functioncreate_folders
	 * 创建多级目录
	 */
	 public function  mkdirs($dir){  
		if(!is_dir($dir)){  
			if(!$this->mkdirs(dirname($dir))){  
				return false;  
			}  
			if(!mkdir($dir,0777)){  
				return false;  
			}  
		}  
		return true;  
	 }
	 /*
	public  function create_folders($dir){
		  
  return is_dir($dir) or (create_folders(dirname($dir)) and mkdir($dir, 0777)); 
  	 
  }
  */
	/**
	 * getUserInfo
	 * 获取用户警员编号
	 */
	 public function updatePidsFunction($pids,$groupid){
	 	
	 		$members_arr=explode(',',$pids);	 			 			
			$jybh_arr=$this->getUserInfo();
			$sendDB_jybh_arr=array();
			$sendDB_jybh_str="";
			for($i=0;$i<count($members_arr);$i++){
				if(in_array($members_arr[$i], $jybh_arr['records'])){
					array_push($sendDB_jybh_arr, $members_arr[$i]);
				}					 					
			}			
      $sendDB_jybh_str=implode(',',$sendDB_jybh_arr);       
			//更新数据库中的pids
			$res=$this->updatePids($groupid,$sendDB_jybh_str); 
		
			return  $sendDB_jybh_str;
	 }
	 
	/**
	 * updatePids
	 * 更新组成员
	 */
	 public  function updatePids($id,$pids){
	 	$bRet = true;
		$errMsg = "";
		$data=array();
		$datas=array();
		$datas1=array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();	
	//	$pids=iconv("GBK","UTF-8",$row["PIDS"]);
		$sql="update t_group t  set t.pids='$pids' where groupid='$id'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			$bRet = false;
		} 
		oci_free_statement($stmt);
		if (!$bRet)
				$arr = array('result' =>'false');
		else
				$arr = array('result' =>'true');

		return $arr;	
		
	 }
	
	 public function getContactsAll($orgCode){
	 	$bRet = true;
		$errMsg="";
  		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$datas=array();
		$sql="select t.alarm,t.userid,t.username, o.orgname,o.orgcode, '1' as flag
  from zdb_user t, zdb_organization o
 where t.bz in (select orgcode from zdb_organization start with orgcode = '$orgCode' connect by prior id = parentid)
   and t.bz = o.orgcode";
   	$sql="select k.* from  ($sql)k order by  k.orgcode asc";
		$stmt = oci_parse($this->dbconn, $sql);
		$r = @oci_execute($stmt); 
		if($r){
			while (($row = oci_fetch_assoc($stmt)) != false){
	  			$data = array(
  					'userId' =>iconv("GBK","UTF-8", $row["USERID"]),
					'userName' => iconv("GBK","UTF-8",$row["USERNAME"]),
					'alarm' =>iconv("GBK","UTF-8", $row["ALARM"]),
					'orgCode' => $row["ORGCODE"],
					'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"]),
					'flag' => $row["FLAG"]
				);
				array_push($datas, $data);
			}
		}else{
				$bRet = false;
				$errMsg="获取部门通讯录失败";
		}
	 	oci_free_statement($stmt);
	 	if (!$bRet)
			$arr = array('result' =>'false', 'errmsg' =>$errMsg);
		else
			$arr = array('result'=>'true','errmsg'=>'','records'=>$datas);
			return $arr;
	 }
		
	 
	
	/**
	 * pagingSixTable
	 * 6表分页 综合查询用
	 */
	function pagingSixTable($arrayValue, $start, $limit) {
	    $sixTableDate=array();
	   
	    if($start+$limit<=count($arrayValue)){
			for ($i=$start;$i<$start+$limit;$i++) {
				array_push($sixTableDate, $arrayValue[$i]);
	        }
	    }
	    else {
	    	if($start<count($arrayValue)){
	    	    for ($i=$start;$i<count($arrayValue);$i++) {
					array_push($sixTableDate, $arrayValue[$i]);
	          }
	    	}
	    	
	    }

		return $sixTableDate;
	}
	/**
	 * memSetInfo
	 * 存入memcache时值进行序列化
	 */
	public function memSetInfoSerialize32($key,$value){
		$mem = new Memcache;
		$mem->connect(GlobalConfig::getInstance()->memcache_ip,GlobalConfig::getInstance()->memcache_port);	
		$mem->set($key, serialize($value), 32);
	}

	/**
	*
	*
	*
	*/
	public function memSetInfoToJsonserialize32($key,$dynamicPoint){
		$mem = new Memcache;
		$mem->connect(GlobalConfig::getInstance()->memcache_ip,GlobalConfig::getInstance()->memcache_port);	
	
		$arr = array(
		'locateTime' => $dynamicPoint->locateTime,
		'xh' => $dynamicPoint->xh,
		'location' => $dynamicPoint->location,
		'onLine' => $dynamicPoint->onLine,
		'speed' => $dynamicPoint->speed,
		'road' => $dynamicPoint->road,
		'status' => $dynamicPoint->status,
		'direction' => $dynamicPoint->direction,
	    'jybh' => $dynamicPoint->jybh,
		'message' => $dynamicPoint->message,
	    'recvTime' => $dynamicPoint->recvTime,
		'deviceId' => $dynamicPoint->deviceId

		);
		//转化成json格式
		
		$mem->set($key, serialize(encodeJson($arr)), 32);
		//echo  $dynamicPoint->road;
		//return  $arr;
	
	}


	/**
	 * getCarInfoById
	 * 根据id查询警车信息
	 */
	public function getCarInfoById($id) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$bRet = false;
		
		$sql = "select car.*,org.* from zdb_equip_car car, zdb_organization org where car.id='$id' and car.dwdm=org.orgCode";
		//echo $sql;
		//print $sql;
		
		$stmt = oci_parse($this->dbconn, $sql);
		if(@oci_execute($stmt)){
			if( ($row = oci_fetch_assoc($stmt)) != false) {
				$data = array(
					'hphm' => iconv("GBK","UTF-8",$row["HPHM"]),	
					'hpzl' => iconv("GBK","UTF-8",$row["HPZL"]),
					'pp' => iconv("GBK","UTF-8",$row["PP"]),
					'orgCode' => iconv("GBK","UTF-8",$row["DWDM"]),
					'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"])
					);
				$bRet = $data;
			}
		}
		

		return $bRet;
	}

	/**
	 * getUserInfoById
	 * 根据id查询警员信息
	 */
	public function getUserInfoById($id) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$bRet = false;

		$sql = "select usr.*,org.orgcode,org.orgname from zdb_user usr, zdb_organization org where usr.userid='$id' and usr.bz=org.orgCode";
		$stmt = oci_parse($this->dbconn, $sql);
		if(@oci_execute($stmt)){
	  		if( ($row = oci_fetch_assoc($stmt)) != false) {
	  			$data = array(
	  				'userName' => iconv("GBK","UTF-8",$row["USERNAME"]),	
			   		'alarm' => iconv("GBK","UTF-8",$row["ALARM"]),
			   		'orgCode' => iconv("GBK","UTF-8",$row["ORGCODE"]),
			   		'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"]),
			   		'hphm' => "",
			   		'hpzl' => "",
			   		'pp' => ""
					);
				$bRet = $data;
			}
		}

		return $bRet;
	}
	
	/**
	 * 根据警情编号查询所有警情
	 * @param $tableName表名
	 * @return
	 */
	public function getSequenceByTable($tableName) {
		$bRet = true;
		$result = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		//select SENDMESSAGELOG_SEQ.Nextval as seq from dual
		$sql = "select $tableName"."_SEQ.Nextval as SEQ from dual";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$result = iconv("GBK", "UTF-8", $row["SEQ"]);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		return $result;
	}
	
	public function clearAllMemcache($bmdm,$sjbm){
		$mem = new Memcache;
		$mem->connect(GlobalConfig::getInstance()->memcache_ip,GlobalConfig::getInstance()->memcache_port);		
		if($bmdm){
			$mem->delete('g'.$bmdm);
			$mem->delete('g350'.$bmdm);	
			$mem->delete('gmobile'.$bmdm);	
		}
		if($sjbm){
			$bmArr = explode("_",$sjbm);
			for($i=0;$i<count($bmArr);$i++){
				$sjbmRecord = $bmArr[$i];
				if($sjbmRecord){
	   				$mem->delete('g'.$sjbmRecord);
					$mem->delete('g350'.$sjbmRecord);	
					$mem->delete('gmobile'.$sjbmRecord);	
				}
	   	
			} 	
		}
	}
	
	/**
	 * 访问外部接口
	 */
	public function outEventFeedBack($url,$params,$AlarmID,$AlarmCode,$PoliceType,$ReceiveType,$ReceiveMethod,$AlarmType,$AlarmTime,$AlarmAddress,
	$AlarmPlace,$AlarmContent,$AlarmTypeCode1,$AlarmTypeCode2,$AlarmDetailTypeCode,$AlarmLevelCode,$AlarmSituation,$AlarmStatus,$PoliceDeptCode,$PoliceDeptName,$IsAlarmForward,$AlarmOccurrenceTime,$ArrivalTime,$AlarmFinishTime,$PoliceAssignTime,
	$DistrictCode,$FeedbackDeptCode,$FeedbackPoliceCode,$FeedbackPoliceName,$OnStreet,$IsCreated,$FireMaterialCode,$FireBuildingStructureCode,$CapturedNum,$RescueNum,$InjuryNum,$DeathNum,
	$AlarmResult,$RescueWomenNum,$RescueChildrenNum,$RescuePeopleNum,$VehicleNum,$AlarmDetailType,$PoliceNum,$ShipNum,$SetOutTime,$CrimePlace,$CrimeMeans,$CrimeObject,$CrimeCharacter,$CrimeItem,
	$Result,$EconomicLoss,$X,$Y,$IsReturn,$IsDelete,$CreateTime,$UpdateTime,$zrqCode,$zrqName,$pjfs,$jqzbly,$jsjqly,$xjbmbm,$xjbmmc)
	//,$ssqkms,$tqdm,$hzyydm,$zhsglxdm,$hzcsdm,$qhwdm,$qhjzjgdm,$hzdjdm,$dycd_sj,$dydc_sj,$hcpm_sj,
	//$cl_sj,$xczzh,$cdsqs,$sfzddw,$zd_dwmc,$xlbm_rs,$dljtsgxtdm,$sfwhp,$sgccyydm,$njddm,$dllmzkdm,$shjdcs,$shfjdcs,
	//$xzqhdm,$dllxdm)
	{
		//$url = 'http://192.168.0.74:8080/bls?operation=FeedbackPolicePlatformRest&content=';
		//$url = 'http://10.78.17.154:9999/lbs?operation=FeedbackInfo_AddFeedbackInfo_v001&license=a756244eb0236bdc26061cb6b6bdb481&content=';
		$post_data['AlarmID'] =	isset($AlarmID)	?	urlencode($AlarmID) : ""	;
		$post_data['AlarmCode'] =	isset($AlarmCode)	?	urlencode($AlarmCode) : ""	;
		$post_data['PoliceType'] =	isset($PoliceType)	?	urlencode($PoliceType) : ""	;
		$post_data['ReceiveType'] =	isset($ReceiveType)	?	urlencode($ReceiveType) : ""	;
		$post_data['ReceiveMethod'] =	isset($ReceiveMethod)	?	urlencode($ReceiveMethod) : ""	;
		$post_data['AlarmType'] =	isset($AlarmType)	?	urlencode($AlarmType) : ""	;
		$post_data['AlarmTime'] =	isset($AlarmTime)	?	urlencode($AlarmTime) : ""	;
		$post_data['AlarmAddress'] =	isset($AlarmAddress)	?	urlencode($AlarmAddress) : ""	;
		$post_data['AlarmPlace'] =	isset($AlarmPlace)	?	urlencode($AlarmPlace) : ""	;
		$post_data['AlarmContent'] =	isset($AlarmContent)	?	urlencode($AlarmContent) : ""	;
		$post_data['AlarmTypeCode1'] =	isset($AlarmTypeCode1)	?	urlencode($AlarmTypeCode1) : ""	;
		$post_data['AlarmTypeCode2'] =	isset($AlarmTypeCode2)	?	urlencode($AlarmTypeCode2) : ""	;
		$post_data['AlarmDetailTypeCode'] =	isset($AlarmDetailTypeCode)	?	urlencode($AlarmDetailTypeCode) : ""	;
		$post_data['AlarmLevelCode'] =	isset($AlarmLevelCode)	?	urlencode($AlarmLevelCode) : ""	;
		$post_data['AlarmSituation'] =	isset($AlarmSituation)	?	urlencode($AlarmSituation) : ""	;
		$post_data['AlarmStatus'] =	isset($AlarmStatus)	?	urlencode($AlarmStatus) : ""	;
		$post_data['PoliceDeptCode'] =	isset($PoliceDeptCode)	?	urlencode($PoliceDeptCode) : ""	;
		$post_data['PoliceDeptName'] =	isset($PoliceDeptName)	?	urlencode($PoliceDeptName) : ""	;
		$post_data['IsAlarmForward'] =	isset($IsAlarmForward)	?	urlencode($IsAlarmForward) : ""	;
		$post_data['AlarmOccurrenceTime'] =	isset($AlarmOccurrenceTime)	?	urlencode($AlarmOccurrenceTime) : ""	;
		$post_data['ArrivalTime'] =	isset($ArrivalTime)	?	urlencode($ArrivalTime) : ""	;
		$post_data['AlarmFinishTime'] =	isset($AlarmFinishTime)	?	urlencode($AlarmFinishTime) : ""	;
		$post_data['PoliceAssignTime'] =	isset($PoliceAssignTime)	?	urlencode($PoliceAssignTime) : ""	;
		$post_data['DistrictCode'] =	isset($DistrictCode)	?	urlencode($DistrictCode) : ""	;
		$post_data['FeedbackDeptCode'] =	isset($FeedbackDeptCode)	?	urlencode($FeedbackDeptCode) : ""	;
		$post_data['FeedbackPoliceCode'] =	isset($FeedbackPoliceCode)	?	urlencode($FeedbackPoliceCode) : ""	;
		$post_data['FeedbackPoliceName'] =	isset($FeedbackPoliceName)	?	urlencode($FeedbackPoliceName) : ""	;
		$post_data['OnStreet'] =	isset($OnStreet)	?	urlencode($OnStreet) : ""	;
		$post_data['IsCreated'] =	isset($IsCreated)	?	urlencode($IsCreated) : ""	;
		$post_data['FireMaterialCode'] =	isset($FireMaterialCode)	?	urlencode($FireMaterialCode) : ""	;
		$post_data['FireBuildingStructureCode'] =	isset($FireBuildingStructureCode)	?	urlencode($FireBuildingStructureCode) : ""	;
		$post_data['CapturedNum'] =	isset($CapturedNum)	?	urlencode($CapturedNum) : ""	;
		$post_data['RescueNum'] =	isset($RescueNum)	?	urlencode($RescueNum) : ""	;
		$post_data['InjuryNum'] =	isset($InjuryNum)	?	urlencode($InjuryNum) : ""	;
		$post_data['DeathNum'] =	isset($DeathNum)	?	urlencode($DeathNum) : ""	;
		$post_data['AlarmResult'] =	isset($AlarmResult)	?	urlencode($AlarmResult) : ""	;
		$post_data['RescueWomenNum'] =	isset($RescueWomenNum)	?	urlencode($RescueWomenNum) : ""	;
		$post_data['RescueChildrenNum'] =	isset($RescueChildrenNum)	?	urlencode($RescueChildrenNum) : ""	;
		$post_data['RescuePeopleNum'] =	isset($RescuePeopleNum)	?	urlencode($RescuePeopleNum) : ""	;
		$post_data['VehicleNum'] =	isset($VehicleNum)	?	urlencode($VehicleNum) : ""	;
		$post_data['AlarmDetailType'] =	isset($AlarmDetailType)	?	urlencode($AlarmDetailType) : ""	;
		$post_data['PoliceNum'] =	isset($PoliceNum)	?	urlencode($PoliceNum) : ""	;
		$post_data['ShipNum'] =	isset($ShipNum)	?	urlencode($ShipNum) : ""	;
		$post_data['SetOutTime'] =	isset($SetOutTime)	?	urlencode($SetOutTime) : ""	;
		$post_data['CrimePlace'] =	isset($CrimePlace)	?	urlencode($CrimePlace) : ""	;
		$post_data['CrimeMeans'] =	isset($CrimeMeans)	?	urlencode($CrimeMeans) : ""	;
		$post_data['CrimeObject'] =	isset($CrimeObject)	?	urlencode($CrimeObject) : ""	;
		$post_data['CrimeCharacter'] =	isset($CrimeCharacter)	?	urlencode($CrimeCharacter) : ""	;
		$post_data['CrimeItem'] =	isset($CrimeItem)	?	urlencode($CrimeItem) : ""	;
		$post_data['Result'] =	isset($Result)	?	urlencode($Result) : ""	;
		$post_data['EconomicLoss'] =	isset($EconomicLoss)	?	urlencode($EconomicLoss) : ""	;
		$post_data['X'] =	isset($X)	?	urlencode($X) : ""	;
		$post_data['Y'] =	isset($Y)	?	urlencode($Y) : ""	;
		$post_data['IsReturn'] =	isset($IsReturn)	?	urlencode($IsReturn) : ""	;
		$post_data['IsDelete'] =	isset($IsDelete)	?	urlencode($IsDelete) : ""	;
		$post_data['CreateTime'] =	isset($CreateTime)	?	urlencode($CreateTime) : ""	;
		$post_data['UpdateTime'] =	isset($UpdateTime)	?	urlencode($UpdateTime) : ""	;
		$post_data['zrqmc'] =	isset($zrqName)	?	urlencode($zrqName) : ""	;
		$post_data['zrqbm'] =	isset($zrqCode)	?	urlencode($zrqCode) : ""	;
		$post_data['pjfs'] =	isset($pjfs)	?	urlencode($pjfs) : ""	;
		$post_data['jqzbly'] =	isset($jqzbly)	?	urlencode($jqzbly) : ""	;
		$post_data['jsjqly'] =	isset($jsjqly)	?	urlencode($jsjqly) : ""	;
		$post_data['xjbmbm'] =	isset($xjbmbm)	?	urlencode($xjbmbm) : ""	;
		$post_data['xjbmmc'] =	isset($xjbmmc)	?	urlencode($xjbmmc) : ""	;
		$data['data']=	array($post_data);	
		$param = json_encode($data, JSON_UNESCAPED_UNICODE);
		//$url = $url.urlencode($param);
		$params = $params.$param;
		//echo $url.$params;
		//$url = $url.$param;
		error_reporting(0);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT,20);   //只需要设置一个秒的数量就可以  
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $result = curl_exec($ch);//运行curl
        curl_close($ch);
		//echo $result."66666";
		/*
		$fh=true;
		//echo $url;
		error_reporting(0);
		$opts = array( 
			'http'=>array( 
			'method'=>"GET", 
			'timeout'=>30, 
			) 
			); 
		$context = stream_context_create($opts); 
		$fh= file_get_contents($url,false,$context);
		$fh = urldecode($fh);
		//echo $fh;		
		 */
		/*
		$o = "";
        foreach ( $params as $k => $v ) 
        { 
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $params = substr($o,0,-1);
		echo $params;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $result = curl_exec($ch);//运行curl
        curl_close($ch);
		echo $result;
		*/
	}
	
	/**
	 * 访问外部接口
	 */
	public function outEventReceiveDisposal($url,$params,$AlarmID,$AlarmCode,$ReceiveType,$ReceiveMethod,$AlarmType,$AlarmTime,
	$AlarmPlace,$AlarmContent,$AlarmTypeCode1,$AlarmTypeCode2,$AlarmDetailTypeCode,$AlarmLevelCode,$PoliceAssignTime,
	$CommendReceiverTime,$StationHouseCode,$StationHouseName,$PoliceDeptCode,$AlarmResultStatus,$AlarmStatus,$IsAlarmForward)
	//,$ssqkms,$tqdm,$hzyydm,$zhsglxdm,$hzcsdm,$qhwdm,$qhjzjgdm,$hzdjdm,$dycd_sj,$dydc_sj,$hcpm_sj,
	//$cl_sj,$xczzh,$cdsqs,$sfzddw,$zd_dwmc,$xlbm_rs,$dljtsgxtdm,$sfwhp,$sgccyydm,$njddm,$dllmzkdm,$shjdcs,$shfjdcs,
	//$xzqhdm,$dllxdm)
	{
		//$url = 'http://192.168.0.74:8080/bls?operation=FeedbackPolicePlatformRest&content=';
		//$url = 'http://10.78.17.154:9999/lbs?operation=FeedbackInfo_AddFeedbackInfo_v001&license=a756244eb0236bdc26061cb6b6bdb481&content=';
		$post_data['AlarmID'] =	isset($AlarmID)	?	urlencode($AlarmID) : ""	;
		$post_data['AlarmCode'] =	isset($AlarmCode)	?	urlencode($AlarmCode) : ""	;
		$post_data['ReceiveType'] =	isset($ReceiveType)	?	urlencode($ReceiveType) : ""	;
		$post_data['ReceiveMethod'] =	isset($ReceiveMethod)	?	urlencode($ReceiveMethod) : ""	;
		$post_data['AlarmType'] =	isset($AlarmType)	?	urlencode($AlarmType) : ""	;
		$post_data['AlarmTime'] =	isset($AlarmTime)	?	urlencode($AlarmTime) : ""	;
		$post_data['AlarmPlace'] =	isset($AlarmPlace)	?	urlencode($AlarmPlace) : ""	;
		$post_data['AlarmContent'] =	isset($AlarmContent)	?	urlencode($AlarmContent) : ""	;
		$post_data['AlarmTypeCode1'] =	isset($AlarmTypeCode1)	?	urlencode($AlarmTypeCode1) : ""	;
		$post_data['AlarmTypeCode2'] =	isset($AlarmTypeCode2)	?	urlencode($AlarmTypeCode2) : ""	;
		$post_data['AlarmDetailTypeCode'] =	isset($AlarmDetailTypeCode)	?	urlencode($AlarmDetailTypeCode) : ""	;
		$post_data['AlarmLevelCode'] =	isset($AlarmLevelCode)	?	urlencode($AlarmLevelCode) : ""	;
		$post_data['PoliceAssignTime'] =	isset($PoliceAssignTime)	?	urlencode($PoliceAssignTime) : ""	;
		
		$post_data['CommendReceiverTime'] =	isset($CommendReceiverTime)	?	urlencode($CommendReceiverTime) : ""	;
		$post_data['StationHouseCode'] =	isset($StationHouseCode)	?	urlencode($StationHouseCode) : ""	;
		$post_data['StationHouseName'] =	isset($StationHouseName)	?	urlencode($StationHouseName) : ""	;
		$post_data['PoliceDeptCode'] =	isset($PoliceDeptCode)	?	urlencode($PoliceDeptCode) : ""	;
		$post_data['AlarmResultStatus'] =	isset($AlarmResultStatus)	?	urlencode($AlarmResultStatus) : ""	;
		$post_data['AlarmStatus'] =	isset($AlarmStatus)	?	urlencode($AlarmStatus) : ""	;
		$post_data['IsAlarmForward'] =	isset($IsAlarmForward)	?	urlencode($IsAlarmForward) : ""	;
		$data['data']=	array($post_data);	
		$param = json_encode($data, JSON_UNESCAPED_UNICODE);
		//$url = $url.urlencode($param);
		$params = $params.$param;
		//$url = $url.$param;
		error_reporting(0);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT,20);   //只需要设置一个秒的数量就可以  
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $result = curl_exec($ch);//运行curl
        curl_close($ch);
		/*
		$fh=true;
		//echo $url;
		error_reporting(0);
		$opts = array( 
			'http'=>array( 
			'method'=>"GET", 
			'timeout'=>30, 
			) 
			); 
		$context = stream_context_create($opts); 
		$fh= file_get_contents($url,false,$context);
		$fh = urldecode($fh);
		//echo $fh;	
		 */	
		/*
		$o = "";
        foreach ( $params as $k => $v ) 
        { 
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $params = substr($o,0,-1);
		echo $params;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $result = curl_exec($ch);//运行curl
        curl_close($ch);
		echo $result;
		*/
	}
	
	/**
	 * 访问外部接口
	 */
	public function outEventFeedBackPicture($picInfo)
	{
		//$url = 'http://192.168.0.74:8080/bls?operation=FeedbackPolicePlatformRest&content=';
		$url = 'http://192.168.20.215:9999/lbs';
		$dataPictures = array();$dataAudios=array();
		$sysTime = date('Y-m-d h:i:s',time());
		for ( $i = 0; $i < count($picInfo); $i++ ) {
			$AlarmID = $picInfo[$i]['jqid'];
			$zylx = $picInfo[$i]['zylx'];
			$zydz = $picInfo[$i]['zydz'];
			$file = fopen($zydz, "r");
			$mysqlPicture = fread($file, filesize($zydz));  
							fclose($file);
			//$mysqlPicture = addslashes(fread(fopen($zydz, "r"), filesize($zydz)));  
			//$post_data['AlarmID'] =	isset($AlarmID)	?	urlencode($AlarmID) : ""	;
			//$post_data['Photo'] =	isset($mysqlPicture)	?	urlencode($mysqlPicture) : ""	;
			
			if($zylx=="1"){
				$data = array(
	  				'AlarmID' => isset($AlarmID)	?	urlencode($AlarmID) : "",
	  				'Photo' => isset($mysqlPicture)	?	chunk_split(base64_encode($mysqlPicture)) : "",
	  				'Comment' => "",
	  				'IsDelete' => "1",
	  				'CreateTime' => $sysTime,
	  				'UpdateTime' => $sysTime
					);
				array_push($dataPictures, $data);
			}else{
				$data = array(
	  				'AlarmID' => isset($AlarmID)	?	urlencode($AlarmID) : "",
	  				'Audio' => isset($mysqlPicture)	?	chunk_split(base64_encode($mysqlPicture)) : "",
	  				'Comment' => "",
	  				'IsDelete' => "1",
	  				'CreateTime' => $sysTime,
	  				'UpdateTime' => $sysTime
					);
				array_push($dataAudios, $data);
			}
			
			//$uploadSrc = $zydz;
		}
		if(count($dataPictures)>0){
			$data_fianl['data']=$dataPictures;
			$param_picture = json_encode($data_fianl, JSON_UNESCAPED_UNICODE);
//			$url_picture = $url_picture.$param_picture;
//			$fh=true;
//			echo $url_picture;
//			error_reporting(0);
//			$fh= file_get_contents($url_picture);
//			$fh = urldecode($fh);
			
//			$o = "";
//	        foreach ( $params as $k => $v ) 
//	        { 
//	            $o.= "$k=" . urlencode( $v ). "&" ;
//	        }
//	        $params = substr($o,0,-1);
//			echo $params;
			$params = 'operation=FeedbackInfo_AddFeedbackPhotoInfo_v001&license=a756244eb0236bdc26061cb6b6bdb481&content=';
			$params = $params.$param_picture;
			error_reporting(0);
			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $url );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch, CURLOPT_TIMEOUT,20);   //只需要设置一个秒的数量就可以  
			curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
			curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	        $result = curl_exec($ch);//运行curl
	        curl_close($ch);
			
		}
		if(count($dataAudios)>0){
			$data_final['data']=$dataAudios;
			$param_audio = json_encode($data_final, JSON_UNESCAPED_UNICODE);
//			$url_audio = $url_audio.$param_audio;
//			$fh=true;
//			echo $url_audio;
//			error_reporting(0);
//			$fh= file_get_contents($url_audio);
//			$fh = urldecode($fh);
			
			$params = 'operation=FeedbackInfo_AddFeedbackAudioInfo_v001&license=a756244eb0236bdc26061cb6b6bdb481&content=';
			$params = $params.$param_audio;
			error_reporting(0);
			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $url );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch, CURLOPT_TIMEOUT,20);   //只需要设置一个秒的数量就可以 
			curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
			curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	        $result = curl_exec($ch);//运行curl
	        curl_close($ch);
		}
	}
	
	/**
	 * getUserInfoByIds
	 * 通过userId查询多个人员信息
	 */
	public function getUserInfoByIds($ids) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$bRet = false;
		$datas = array ();
		$sql = "select usr.*,org.orgcode,org.orgname from zdb_user usr, zdb_organization org where org.orgcode=usr.bz and usr.userid in ($ids) order by org.orgcode asc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if(@oci_execute($stmt)){
	  		while( ($row = oci_fetch_assoc($stmt)) != false) {
	  			$data = array(
	  				'userId' => iconv("GBK","UTF-8",$row["USERID"]),	
	  				'userName' => iconv("GBK","UTF-8",$row["USERNAME"]),	
			   		'alarm' => iconv("GBK","UTF-8",$row["ALARM"]),
			   		'orgCode' => iconv("GBK","UTF-8",$row["ORGCODE"]),
			   		'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"])
					);
				array_push($datas, $data);
			}
		}
		return $datas;
	}
	
		public function getMembersByIds($ids) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$bRet = false;
		$datas = array ();
		$sql = "select usr.*,org.orgcode,org.orgname from zdb_user usr, zdb_organization org where org.orgcode=usr.bz and usr.userid in ($ids)";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if(@oci_execute($stmt)){
	  		while( ($row = oci_fetch_assoc($stmt)) != false) {
	  			$data = array(
	  				'id' => iconv("GBK","UTF-8",$row["USERID"]),	
	  				'userName' => iconv("GBK","UTF-8",$row["USERNAME"]),	
			   		'userCode' => iconv("GBK","UTF-8",$row["ALARM"]),
			   		'orgCode' => iconv("GBK","UTF-8",$row["ORGCODE"]),
			   		'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"])
					);
				array_push($datas, $data);
			}
		}
		return $datas;
	}
	
	
	public function getParentOrg($bmdm){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$bRet = false;
		$data = array ();
		$sql = "select t.parenttreepath from zdb_organization t where t.orgcode='$bmdm'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if(@oci_execute($stmt)){
	  		if( ($row = oci_fetch_assoc($stmt)) != false) {
	  			$data = array(
	  				'parenttreepath' => iconv("GBK","UTF-8",$row["PARENTTREEPATH"])
					);
			}
		}
		return $data;
	}
	
}
?>
