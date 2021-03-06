<?php


/**
 * class Equip
 * version: 1.0
 * 勤务类
 * author: carl
 * 2014/6/17
 * 
 * 此类定义勤务模块全部方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class Equip extends TpmsDB {

	/**
	 * getEquip350M
	 * 根据部门编号查询350兆装备列表
	 * @param orgCode,$rybh,$jqclzt
	 * @return装备列表数组
	 */
	public function getEquip350M($orgCode) {
		$bRet = true;
		$errMsg = "";
		$mens = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.id,t.type,t.dwdm,o.orgName from zdb_equip_350m t left join zdb_organization o on t.dwdm = o.orgcode where 1=1";
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		$sql = $sql . " order by o.orgCode";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'id' => iconv("GBK", "UTF-8", $row["ID"]),
					'type' => iconv("GBK", "UTF-8", $row["TYPE"]),
					'dwdm' => iconv("GBK", "UTF-8", $row["DWDM"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"])
				);

				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		return $mens;
	}
	
	/**
	 * getDutyGroud
	 * 根据部门编号查询组合列表
	 * 参数若为userId则查询该用户所属组合
	 * @param $orgCode,$userId
	 * @return组合列表数组
	 */
	public function getDutyGroud($orgCode,$userId){
		$bRet = true;
		$errMsg = "";
		$mens = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.gid,t.carid,t.userid,t.leaderid,t.orgcode,t.orgcode,t.status,to_char(t.createtime,'yyyy-MM-dd hh24:mi:ss') as createtime,to_char(t.dismisstime,'yyyy-MM-dd hh24:mi:ss') as dismisstime,t.commander,t.m350id,o.orgname," .
				" c.hphm from zdt_duty_group t left join zdb_organization o on t.orgcode = o.orgcode left join zdb_equip_car c on c.id = t.carid" .
				" where t.status != '3' ";
		if($orgCode!=""){
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		if($userId!=""){
		$sql = $sql . " and t.userid like '%$userId%'";
		}
		$sql = $sql . " order by t.createtime desc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$gp_userids = iconv("GBK", "UTF-8", $row["USERID"]);
				$leaderId = iconv("GBK", "UTF-8", $row["LEADERID"]);
				$commanderId = iconv("GBK", "UTF-8", $row["COMMANDER"]);
				$leaderName = $this->getNameByUserid($leaderId);
				$commanderName = $this->getNameByUserid($commanderId);
				$array = explode(",", $gp_userids);
				$names = "";
				for ($i=0;$i<count($array);$i++) {
					$name = $this->getNameByUserid($array[$i]);
					$p = $i==0 ? "" : ",";
					$names .= $p.$name;
				}
				$men = array (
					'gid' => iconv("GBK", "UTF-8", $row["GID"]),
					'value' => iconv("GBK", "UTF-8", $row["GID"]),
					'text' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'carid' => iconv("GBK", "UTF-8", $row["CARID"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'userid' => $gp_userids,
					'userNames' => $names,
					'orgcode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'leaderid' => $leaderId,
					'leaderName' =>$leaderName,
					'status' => iconv("GBK", "UTF-8", $row["STATUS"]),
					'createtime' => iconv("GBK", "UTF-8", $row["CREATETIME"]),
					'dismisstime' => iconv("GBK", "UTF-8", $row["DISMISSTIME"]),
					'commander' => $commanderId,
					'm350id' => iconv("GBK", "UTF-8", $row["M350ID"]),
					'commanderName' => $commanderName
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		return $mens;
	}
	
	public function getGroudCount($orgCode,$userId){
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from zdt_duty_group t left join zdb_organization o on t.orgcode = o.orgcode left join zdb_equip_car c on c.id = t.carid" .
				" where t.status != '3' ";
		if($orgCode!=""){
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		if($userId!=""){
		$sql = $sql . " and t.userid like '%$userId%'";
		}
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		oci_close($this->dbconn);
		return $count;
	}
	
	/**
	 * getNameByUserid
	 * 根据用户ID查询用户对象
	 * @param $id
	 * @return 警员对象
	 */
	public function getNameByUserid($id) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select * from zdb_user where userid='$id'";
		$stmt = oci_parse($this->dbconn, $sql);
		$res = "";
		if (!@ oci_execute($stmt)) {
			oci_close($this->dbconn);
			exit;
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$res = iconv("GBK", "UTF-8", $row["USERNAME"]);
			}
		}
		oci_free_statement($stmt);

		return $res;
	}
	
	/**
	 * getFeatureByOrgCode
	 * 根据部门编码查询巡逻路线
	 * @param $orgCode
	 * @return 巡逻路线组
	 */
	public function getFeatureByOrgCode($orgCode){
		$bRet = true;
		$errMsg = "";
		$mens = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select usr.username, t.featureid,t.featurename,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.geometry) as geometry,t.producerid,t.type from zdt_duty_feature t" .
				" left join zdb_user usr on t.producerid=usr.userid" .
				" left join zdb_organization o on o.orgcode=usr.bz where 1=1 " ;
		if($orgCode!=""){
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'featureId' => iconv("GBK", "UTF-8", $row["FEATUREID"]),
					'featureName' => iconv("GBK", "UTF-8", $row["FEATURENAME"]),
					'value' => iconv("GBK", "UTF-8", $row["FEATUREID"]),
					'text' => iconv("GBK", "UTF-8", $row["FEATURENAME"]),
					'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"]),
					'producerId' => iconv("GBK", "UTF-8", $row["PRODUCERID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'type' => iconv("GBK", "UTF-8", $row["TYPE"])
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		return $mens;
	}
	
	/**
	 * updateOrAddDutyFeature
	 * 新增或修改巡逻路线数据
	 * @param $featureId,$featureName,$geometry,$userId,$type
	 * @return true or false
	 */
	public function updateOrAddDutyFeature($featureId,$featureName,$geometry,$userId,$type)
	{
		$featureName = iconv("UTF-8","GBK",$featureName);
		$bRet = true;
		$errMsg = '';
		if($featureId!=""){
			$sql = "update ZDT_Duty_Feature set featureName='$featureName' where featureId='$featureId' ";
		}else{
			$seq = $this->getSequenceByTable("ZDT_Duty_Feature");
			$sql = "insert into ZDT_Duty_Feature(featureId,featureName,geometry,producerId,type)values('$seq','$featureName',sdo_geometry('$geometry',4326),'$userId','$type')";
		}
		
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		$res = isset($seq) ? $seq : $featureId;//modify by carl  
		return $res;
	}
	
	/**
	 * updateOrAddPoliceFeature
	 * 巡逻路线绑定
	 * @param $id,$roleType,$featureId,$userId
	 * @return true or false
	 */
	public function updateOrAddPoliceFeature($id,$roleType,$featureId,$userId){
		$policeFeatureCount = $this->getPoliceFeatureCount($id);
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		if($policeFeatureCount){
			$sql = "update ZDT_Duty_PoliceFeature set RoleType='$roleType',FeatureId='$featureId' where id='$id' ";
		}else{
			$sql = "insert into ZDT_Duty_PoliceFeature(id,roleType,featureId) values('$id','$roleType','$featureId')";
		}
		
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '新增失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		
		//$features = $this->getPoliceFeatureById($id);
		//echo $features['geometry'];
		//$ret = $this->sendFeatureToTerminal($userId,$features['leaderId'],$features['featureId'],$features['featureName'],$features['type'],$features['geometry']);

		return $bRet;
	}
	
	/**
	 * updateOrAddPoliceFeature
	 * 巡逻路线绑定
	 * @param $id,$roleType,$featureId,$userId
	 * @return true or false
	 */
	public function updateOrAddGroupFeature($gids,$roleType,$featureId,$userId){
		$bRet = true;
		$errMsg = '';
		$insetSql = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			for ($i=0;$i<count($gids);$i++) {
		 		$gid = $gids[$i]['gid'];
		 		$insetSql = $insetSql."  insert into ZDT_Duty_PoliceFeature(id,roleType,featureId) values('$gid','$roleType','$featureId');";
			}
		if($insetSql!=""){
			$sql = "begin ".$insetSql." end;";
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@ oci_execute($stmt)) {
				$bRet = false;
				$errMsg = '新增失败';
			}
			oci_free_statement($stmt);
			oci_close($this->dbconn);
		}
		//$features = $this->getPoliceFeatureById($id);
		//echo $features['geometry'];
		//$ret = $this->sendFeatureToTerminal($userId,$features['leaderId'],$features['featureId'],$features['featureName'],$features['type'],$features['geometry']);

		return $bRet;
	}
	
	/**
	 * getPoliceFeatureCount
	 * 根据警员或警车ID查询巡逻绑定表count
	 * @param $id
	 * @return true or false
	 */
	public function getPoliceFeatureCount($id) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from ZDT_Duty_PoliceFeature where id='$id'";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		oci_close($this->dbconn);
		if ($count > 0) {
			return true;
		} else {
			return false;
		}

	}
	
	/**
	 * getPoliceFeatureById
	 * 根据组合ID查询巡逻绑定组合对象
	 * @param $id
	 * @return 绑定对象数据
	 */
	public function getPoliceFeatureById($id){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select f.featureid,f.featurename,g.leaderid,f.type,MDSYS.Sdo_Util.to_wktgeometry_varchar(f.geometry) as geometry from zdt_duty_policefeature t" .
				" left join zdt_duty_feature f on t.featureid=f.featureid" .
				" left join zdt_duty_group g on t.id=g.gid where t.id='$id'";
		$stmt = oci_parse($this->dbconn, $sql);
		//echo $sql;
		if (!@ oci_execute($stmt)) {
			oci_close($this->dbconn);
			exit;
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'featureId' => iconv("GBK", "UTF-8", $row["FEATUREID"]),
					'featureName' => iconv("GBK", "UTF-8", $row["FEATURENAME"]),
					'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"]),
					'leaderId' => iconv("GBK", "UTF-8", $row["LEADERID"]),
					'type' => iconv("GBK", "UTF-8", $row["TYPE"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $men;
	}
	
	/** 
	* sendFeatureToTerminal 
	* 向终端发送巡逻路线信息 
	*/
	public function sendFeatureToTerminal($sendPid, $receiveId, $featureId, $featureName, $type, $geometry) {

		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$resArr = array ();
		$resArrTrue = array ();
		$strPoliceIdMessage1 = '';

		//判断是不是系统消息 
		if ($sendPid == "指挥中心") {
			$sendPid = "000000";
		}

		//解析群发警员编号	
		$receiveId = str_replace("\n", "", $receiveId);

		$str = '{"message":{"comCode":"20","codeId":"' . $date . '"},"result":{"sendPid":"' . $sendPid . '","receivePid":"' . $receiveId . '","featureId":"' . $featureId . '","featureName":"' . $featureName . '","type":"' . $type . '","geometry":"' . $geometry . '"}}';
		//echo $str;
		$length = mb_strlen($str, 'UTF8');
		//$length=100;
		switch (strlen($length)) {
			case 0 :
				$len = '00000000';
				break;
			case 1 :
				$len = '0000000' . $length;
				break;
			case 2 :
				$len = '000000' . $length;
				break;
			case 3 :
				$len = '00000' . $length;
				break;
			case 4 :
				$len = '0000' . $length;
				break;
			case 5 :
				$len = '000' . $length;
				break;
			case 6 :
				$len = '00' . $length;
				break;
			case 7 :
				$len = '0' . $length;
				break;
			default :
				echo '超出上限';
				exit;
		}
		$word = $len . $str;
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('could  not create socket');

		$connect = @ socket_connect($socket, GlobalConfig :: getInstance()->socket_ip, GlobalConfig :: getInstance()->socket_port) or die('could not connect socket	server');

		//向服务端发送数据
		socket_write($socket, $word . "\n");

		//接受服务端返回数据
		$str = @ socket_read($socket, 1024, PHP_NORMAL_READ);
		//判断接收返回数据长度是否一致

		$lastStr = trim($str);

		$lenfan = mb_strlen($lastStr, 'UTF8');

		$str3 = substr($lastStr, 8);
		$str1 = substr($lastStr, 0, 8);

		$s1 = '';

		for ($i = 0; $i < 8; $i++) {
			$s = substr($str1, $i, 1);

			if ($s != '0') {

				$s1 = substr($str1, $i, 8);
				break;
			}

		}

		$obj = json_decode($str3);
		//echo encodeJson($obj);
		
		//$message= $obj->result->message;
		$message = $obj->message->message;
		$value = '';
		$message1 = '';

		//判断返回结果
		if ($message == 00 && mb_strlen($str3, 'UTF8') == $s1) {
			$value = 'true';
			$message1 = '发送成功！';
		} else
			if ($message == 02) {
				$value = 'faile';
				$message1 = '非法字符串';
			} else
				if ($message == 03) {
					$value = 'faile';
					$message1 = '命令码异常';
				} else
					if ($message == 04) {
						$value = 'faile';
						$message1 = '设备校验异常';
					} else
						if ($message == 05) {
							$value = 'faile';
							$message1 = '设备不在线';
						}

		//判断发送失败并记录
		if ($message == 02 || $message == 03 || $message == 04 || $message == 05) {
			$strPoliceIdMessage1 .= $message1;

			$datas = array (
				'result' => $value
			);
			array_push($resArr, $datas);
		}
		socket_close($socket);
		if (count($resArr) > 0) {
			$datas = array (
				'result' => 'false',
				'errmsg' => $strPoliceIdMessage1
			);
			return $datas;
		} else {
			$datas = array (
				'result' => 'true',
				'errmsg' => '发送成功'
			);
			return $datas;
		}
	}
	
	/**
	 * getPoliceFeatureByOrgCode
	 * 根据根据部门ID查询绑定的组合列表
	 * @param $orgCode
	 * @return 组合对象数组
	 */
	public function getPoliceFeatureByOrgCode($orgCode){
		$bRet = true;
		$errMsg = "";
		$mens = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.id,o.orgcode,o.orgname,f.type, f.featureid,f.featurename,MDSYS.Sdo_Util.to_wktgeometry_varchar(f.geometry) as geometry,c.hphm,g.leaderid from zdt_duty_policefeature t " .
				" left join zdt_duty_feature f on f.featureid=t.featureid" .
				" left join zdt_duty_group g on g.gid=t.id" .
				" left join zdb_equip_car c on g.carid=c.id" .
				" left join zdb_organization o on o.orgcode=g.orgcode where 1=1 " ;
		if($orgCode!=""){
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'id' => iconv("GBK", "UTF-8", $row["ID"]),
					'featureId' => iconv("GBK", "UTF-8", $row["FEATUREID"]),
					'featureName' => iconv("GBK", "UTF-8", $row["FEATURENAME"]),
					'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'leaderId' => iconv("GBK", "UTF-8", $row["LEADERID"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'type' => iconv("GBK", "UTF-8", $row["TYPE"])
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		return $mens;
	}
	
	/**
	 * getPoliceFeatureByOrgCode
	 * 根据根据部门ID查询绑定的组合列表
	 * @param $orgCode
	 * @return 组合对象数组
	 */
	public function getPoliceFeatureByFeatureId($featuredId){
		$bRet = true;
		$errMsg = "";
		$mens = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.id,o.orgCode,o.orgname,f.type, f.featureid,f.featurename,MDSYS.Sdo_Util.to_wktgeometry_varchar(f.geometry) as geometry,c.hphm,g.leaderid from zdt_duty_policefeature t " .
				" left join zdt_duty_feature f on f.featureid=t.featureid" .
				" left join zdt_duty_group g on g.gid=t.id" .
				" left join zdb_organization o on o.orgcode=g.orgcode".
				" left join zdb_equip_car c on g.carid=c.id  where 1=1 and f.featureid='$featuredId' "  ;
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'id' => iconv("GBK", "UTF-8", $row["ID"]),
					'featureId' => iconv("GBK", "UTF-8", $row["FEATUREID"]),
					'featureName' => iconv("GBK", "UTF-8", $row["FEATURENAME"]),
					'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'leaderId' => iconv("GBK", "UTF-8", $row["LEADERID"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'type' => iconv("GBK", "UTF-8", $row["TYPE"])
				);
				array_push($mens, $men);
			}
		}
		return $mens;
	}
	
	/**
	 * getPoliceFeatureByGid
	 * 根据根据组合id查询绑定的组合列表
	 * @param $orgCode
	 * @return 组合对象数组
	 */
	public function getPoliceFeatureByGid($gid){
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$arr = array('result' =>'false','records' =>$datas);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select g.gid,f.type, f.featureid,f.featurename,MDSYS.Sdo_Util.to_wktgeometry_varchar(f.geometry) as geometry from zdt_duty_policefeature t " .
				" left join zdt_duty_feature f on f.featureid=t.featureid" .
				" left join zdt_duty_group g on g.gid=t.id where 1=1 ";
		if($gid!=""){
		$sql = $sql . " and g.gid = '$gid'";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'gid' => iconv("GBK", "UTF-8", $row["GID"]),
					'featureId' => iconv("GBK", "UTF-8", $row["FEATUREID"]),
					'featureName' => iconv("GBK", "UTF-8", $row["FEATURENAME"]),
					'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"]),
					'type' => iconv("GBK", "UTF-8", $row["TYPE"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if($bRet)
			$arr = array('result' =>'true','records' =>$datas,'errmsg' => $errMsg);
		else
			$arr = array('result' =>'false','records' =>$datas,'errmsg' => $errMsg);
		return $arr;
	}
	
	
	/**
	 * getPoliceFeatureByJqid
	 * 根据featureId查询警情位置信息表count
	 * @param $featureId
	 * @return true or false
	 */
	public function getPoliceFeatureByJqid($featureId) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from ZDT_Duty_PoliceFeature where featureId='$featureId'";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		if ($count > 0) {
			return true;
		} else {
			return false;
		}

	}
	
	/**
	 * deleteFeature
	 * 删除巡逻路线信息
	 */
	 
	public function deleteFeature($featureId){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$delname='';
		$isContain = $this->getPoliceFeatureByJqid($featureId);
		if ($isContain){
			$errMsg = "该数据已绑定组合";
			$bRet = false;
		} else{
			$sql = " delete from ZDT_Duty_Feature where featureId='$featureId' ";
			//echo  $sql;
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)){
				$bRet = false;
				$errMsg="删除信息失败";
			} 
			oci_free_statement($stmt);
		}
		oci_close($this->dbconn);
		if ($bRet)
		$arr = array('result' =>$bRet,'errmsg' =>'删除成功');
		else
		$arr = array('result' =>$bRet, 'errmsg' =>$errMsg);
		
		
		return $arr;
	}
	
	/**
	 * deletePoliceFeature
	 * 解除巡逻路线绑定信息
	 */
	 
	public function deletePoliceFeature($id,$roleType,$featureId){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$delname='';
		$sql = " delete from ZDT_Duty_PoliceFeature where featureId='$featureId' and id='$id'";
		//echo  $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)){
			$bRet = false;
			$errMsg="解绑失败";
		} 
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if ($bRet)
		$arr = array('result' =>$bRet,'errmsg' =>'解绑成功');
		else
		$arr = array('result' =>$bRet, 'errmsg' =>$errMsg);
		
		
		return $arr;
	}
	
	/**
	 * getModel
	 * 分页查询人或车
	 * @param $orgCode,$type,$page,$rows
	 * @return 结果数组
	 */
	public function getModel($orgCode,$type,$page,$rows){
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			
		 /*组成sql*/
		if($type=="1"){
			$sql = "select count(*) ROWCOUNT from zdb_equip_car t left join zdb_organization o on t.dwdm = o.orgcode where 1=1  " ;
		}else if($type=="2"){
			$sql = "select count(*) ROWCOUNT from zdb_user t left join zdb_organization o on t.bz = o.orgcode where 1=1 " ;
		}
		
		if($orgCode!=""){
		$sql = $sql . " and o.orgCode = '$orgCode' ";
		}
	    
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt,"ROWCOUNT",$row_count);
		if (!@oci_execute($stmt)) {
	  		$bRet = false;
	  		$errMsg="查询失败";
		}else{
			
		 /*处理分页*/
			oci_fetch($stmt);
			$total_rec = $row_count;
			oci_free_statement($stmt);
    	
			/*查询部门*/
			if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			if($type=="1"){
				$sql = "select t.id,t.hphm as name,o.orgcode,o.orgname from zdb_equip_car t left join zdb_organization o on t.dwdm = o.orgcode where 1=1  " ;
			}else if($type=="2"){
				$sql = "select t.userid as id,t.username as name,o.orgcode,o.orgname from zdb_user t left join zdb_organization o on t.bz = o.orgcode where 1=1 " ;
			}
			
			if($orgCode!=""){
			$sql = $sql . " and o.orgCode = '$orgCode' ";
			}
			//echo $sql;
			$sql = pageResultSet($sql, $page, $rows);
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
				$bRet = false;
				$errMsg="查询失败";
			}else{
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array(
						'id' => iconv("GBK", "UTF-8", $row["ID"]),
						'name' => iconv("GBK", "UTF-8", $row["NAME"]),
						'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
						'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
						'type' => $type
       				);
		   			array_push($datas, $data);
	  		}
	  			oci_free_statement($stmt);
	  			oci_close($this->dbconn);
				$arr=array('total'=>$total_rec,'rows' => $datas);
			}
		}
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	}

	/**
	 * getEquipment
	 * 根据部门ID查询装备列表
	 * @param $orgCode
	 * @return 结果数组
	 */
	public function getEquipment($orgCode){
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
                if($orgCode==""){
			$orgCode=GlobalConfig :: getInstance()->dsdm."00000000";
		}
		$sql = "select t.orgcode,t.orgname,t.lbmc,t.zblb,t.zbbm,t.lbdw,t.lbms,zbslx-nvl(ybsl,0) zbsl from (select o.orgcode,o.orgname,lb.lbmc,t.zblb,t.zbbm,lb.lbdw,lb.lbms,sum(t.zbsl) as zbslx from zb t left join dm_zblb lb on t.zblb=lb.zblb left join dm_ck ck on t.ckbm = ck.ckbm  left join zdb_organization o on o.orgcode=ck.ssbm where (lb.tszblxbs<> '3' or lb.tszblxbs is null) and  o.orgCode = '$orgCode' group by o.orgcode,o.orgname,lb.lbmc,t.zblb,t.zbbm,lb.lbdw,lb.lbms) t left join (select sum(zbsl) ybsl,orgid,equipid from zdt_duty_equip where orgid='$orgCode' group by orgid,equipid) ybd on t.orgcode=ybd.orgid and t.zbbm=ybd.equipid " ;
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'orgcode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'lbmc' => iconv("GBK", "UTF-8", $row["LBMC"]),
					'zblb' => iconv("GBK", "UTF-8", $row["ZBLB"]),
					'zbbm' => iconv("GBK", "UTF-8", $row["ZBBM"]),
					'lbdw' => iconv("GBK", "UTF-8", $row["LBDW"]),
                                        'zbsl' => iconv("GBK", "UTF-8", $row["ZBSL"]),
                                        'bdsl' => 0,
					'lbms' => iconv("GBK", "UTF-8", $row["LBMS"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $datas);
		return $result;
	}
	
	public function updateOrAddModelEquip($id,$type,$equips,$orgCode,$userName){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			$deleteRes = $this->deleteDutyEquip($id);
			$res = $this->checkEquipment($equips,$orgCode);
			$insetSql="";
			if($res['result']&&$deleteRes['result']){
				$userName = iconv("UTF-8","GBK",$userName);
				for ($i=0;$i<count($equips);$i++) {
		 		$zblb = $equips[$i]['zblb'];
		 		$zbbm = $equips[$i]['zbbm'];
                                $bdsl = $equips[$i]['bdsl'];
		 		$insetSql = $insetSql."  insert into ZDT_Duty_Equip(CarryId,CarryType,EquipType,EquipId,OrgId,Operater,CreateTime,UpdateTime,zbsl)" .
						" values('$id','$type','$zblb','$zbbm','$orgCode','$userName',sysdate,sysdate,$bdsl);";
				}
				
			}else{
				$bRet= $res['result'];
				$errMsg = $res['errmsg'];
				//$result = array('result' =>false,'errmsg' =>$res['errmsg'],'records' => '');
				
			}
			if($insetSql!=""){
				$sql = "begin ".$insetSql." end;";
				//echo $sql;
				$stmt = oci_parse($this->dbconn, $sql);
				if (!@oci_execute($stmt)){
					$bRet = false;
					$errMsg="绑定失败";
				} 
				oci_free_statement($stmt);
				if ($bRet)
				$result = array('result' =>$bRet,'errmsg' =>'绑定成功','records' =>$res['records'] );
				else
				$result = array('result' =>$bRet, 'errmsg' =>$errMsg,'records' =>$res['records']);
			}else{
				$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' =>$res['records']);
			}
			oci_close($this->dbconn);
			return $result;
		
	}
	
	/**
	 * checkEquipment
	 * 根据装备类型和装备编码检验装备是否有可用的绑定量
	 * @param $orgCode
	 * @return 结果数组
	 */
	public function checkEquipment($equips,$orgCode){
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array('result' =>true,'errmsg' =>'','records' => '');
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
                 $datas = array ();
		 for ($i=0;$i<count($equips);$i++) {
		 		$zblb = $equips[$i]['zblb'];
		 		$zbbm = $equips[$i]['zbbm'];
                                $bdsl = $equips[$i]['bdsl'];
				$res = $this->getDistanceCountByParems($zblb,$zbbm,$orgCode,$bdsl);
				if($res['sl']-$bdsl<0){
					$errMsg = $res['lbmc'].'无可用库存';
					$result = array('result' =>false,'errmsg' =>$errMsg,'records' => array('zbbm'=>$zbbm , 'sysl'=>$res['sl']));
					return $result;
				}
                                else
                                {
                                   $data = array (
					'zbbm' => $zbbm,
					'sysl'=>$res['sl']-$bdsl
				);
				array_push($datas, $data);
                                }
		  }
                  $result['records']=$datas;
		return $result;
	}
	
	/**
	 * getPoliceFeatureById
	 * 根据组合ID查询巡逻绑定组合对象
	 * @param $id
	 * @return 绑定对象数据
	 */
	public function getDistanceCountByParems($zblb,$zbbm,$orgCode,$bdsl){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
                if($zblb==NULL||$zbbm==NULL||$orgCode==NULL||$bdsl==NULL)
                {
                   return $data; 
                }
		$sql = "select nvl((select  sum(a.zbsl) from zb a left join dm_ck b on a.ckbm=b.ckbm where ";
		$sql_bds = "),0)-nvl((select  sum(zbsl) from  ZDT_Duty_Equip t where ";
                $sql_zblb = "),0) sysl,lbmc from dm_zblb  where ";
                if($orgCode!=""){
			$sql .= "b.ssbm='$orgCode'";
                        $sql_bds .= "t.orgid='$orgCode'";
		}
		if($zbbm!=""){
			$sql .= " and a.zbbm='$zbbm'";
                        $sql_bds .= "and t.equipid='$zbbm'";
		}
		if($zblb!=""){
			$sql .= " and a.zblb='$zblb'";
                        $sql_bds .= "and t.equiptype='$zblb'";
                        $sql_zblb .= " zblb='$zblb'";
		} 
 		$sql.=$sql_bds.$sql_zblb;
                //echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			oci_close($this->dbconn);
                       
			exit;
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
                         
				$data = array (
					'lbmc' => iconv("GBK", "UTF-8", $row["LBMC"]),
					'sl' =>$row["SYSL"]
				);
			}
		}
                //echo encodeJson($data);
		oci_free_statement($stmt);
		return $data;
	}
	
		
	/**
	 * deleteDutyEquip
	 * 解除装备绑定信息
	 */
	 
	public function deleteDutyEquip($id){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = " delete from ZDT_Duty_Equip where carryId='$id' ";
		//echo  $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)){
			$bRet = false;
			$errMsg="删除失败";
		} 
		oci_free_statement($stmt);
		if ($bRet)
		$arr = array('result' =>$bRet,'errmsg' =>'删除成功');
		else
		$arr = array('result' =>$bRet, 'errmsg' =>$errMsg);
		
		
		return $arr;
	}
	
	/**
	 * deleteConstrolDutyEquip
	 * 解除装备绑定信息
	 */
	 
	public function deleteConstrolDutyEquip($id){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$res = $this->deleteDutyEquip($id);
		
		oci_close($this->dbconn);
		
		return $res;
	}
	
	/**
	 * getEquipment
	 * 根据部门ID查询装备列表
	 * @param $orgCode
	 * @return 结果数组
	 */
	public function getDutyEquipById($id){
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.equiptype as zblb,t.equipid as zbbm,t.orgid as orgcode,t.zbsl zbsl from zdt_duty_equip t where 1=1";
		if($id!=""){
			$sql = $sql . " and  t.carryid = '$id'";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'zblb' => iconv("GBK", "UTF-8", $row["ZBLB"]),
					'zbbm' => iconv("GBK", "UTF-8", $row["ZBBM"]),
                                        'bdsl' => $row["ZBSL"],
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $datas);
		return $result;
	}
	
	/**
	 * getFeatureByOrgCode
	 * 根据部门编码查询巡逻路线
	 * @param $orgCode
	 * @return 巡逻路线组
	 */
	public function getEquipFeatureByOrgCode($featureName,$orgCode,$page,$rows){
		$featureName = iconv("UTF-8", "GBK", $featureName);
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		$arr=array('total'=>0,'rows' => $datas);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(*) ROWCOUNT from zdt_duty_feature t" .
				" left join zdb_user usr on t.producerid=usr.userid" .
				" left join zdb_organization o on o.orgcode=usr.bz where 1=1 " ;
		if($orgCode!=""){
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		if($featureName!=""){
		$sql = $sql . " and featureName like '%$featureName%'";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt,"ROWCOUNT",$row_count);
		if (!@oci_execute($stmt)) {
	  		$bRet = false;
	  		$errMsg="查询失败";
		}else{
			
		 /*处理分页*/
			oci_fetch($stmt);
			$total_rec = $row_count;
			oci_free_statement($stmt);


		$sql = "select usr.username, t.featureid,t.featurename,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.geometry) as geometry,t.producerid,t.type,o.orgcode,o.orgname from zdt_duty_feature t" .
				" left join zdb_user usr on t.producerid=usr.userid" .
				" left join zdb_organization o on o.orgcode=usr.bz where 1=1 " ;
		if($orgCode!=""){
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		if($featureName!=""){
		$sql = $sql . " and featureName like '%$featureName%'";
		}
		$sql = pageResultSet($sql, $page, $rows);
		//echo $sql;	
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			$bRet = false;
			$errMsg="查询失败";
		}else{
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$featuredId = iconv("GBK", "UTF-8", $row["FEATUREID"]);
				$groupRecords = $this->getPoliceFeatureByFeatureId($featuredId);
				$men = array (
					'featureId' => $featuredId,
					'featureName' => iconv("GBK", "UTF-8", $row["FEATURENAME"]),
					'value' => iconv("GBK", "UTF-8", $row["FEATUREID"]),
					'text' => iconv("GBK", "UTF-8", $row["FEATURENAME"]),
					'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"]),
					'producerId' => iconv("GBK", "UTF-8", $row["PRODUCERID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'type' => iconv("GBK", "UTF-8", $row["TYPE"]),
					'groupRecord' => $groupRecords,
					'groupsId' => count($groupRecords)
				);
				array_push($datas, $men);
				}
			oci_free_statement($stmt);
			oci_close($this->dbconn);
			$arr=array('total'=>$total_rec,'rows' => $datas);
			}
		}
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	}
	
	
	/**
	 * 查询通信录根据模糊条件查询
	 */
	public function getSearchAllContact($type, $key, $orgCode) {
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$key = iconv("UTF-8", "GBK", $key);
		if ($type == "2") {
			$sql = "select g.gid,g.userid,g.orgcode,o.orgname,c.hphm from zdt_duty_group g left join zdb_organization o on o.orgcode=g.orgcode left join zdb_equip_car c on c.id=g.carid where g.status!='3'";
				if ($orgCode != "210200000000") {
					$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
				}
			$sql = $sql . "  and (o.orgcode like '%$key%' or o.orgname like '%$key%') order by o.orgcode asc";
		} else if ($type == "3") {
			$sql = "select g.gid,g.userid,g.orgcode,o.orgname,c.hphm from zdt_duty_group g left join zdb_organization o on o.orgcode=g.orgcode left join zdb_equip_car c on c.id=g.carid where g.status!='3'";
			if ($orgCode != "210200000000") {
				$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
			}
			$sql = $sql . "  and (c.hphm like '%$key%') order by o.orgcode asc";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'gid' => iconv("GBK", "UTF-8", $row["GID"]),
					'userId' => iconv("GBK", "UTF-8", $row["USERID"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'groupCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'groupName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$result = array (
			'total' => "",
			'rows' => $datas
		);
		return $result;
	}
	
	/**
	 * deletePoliceFeature
	 * 解除巡逻路线绑定信息
	 */
	 
	public function deleteGroupFeature($featureId){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$delname='';
		$sql = " delete from ZDT_Duty_PoliceFeature where featureId='$featureId'";
		//echo  $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)){
			$bRet = false;
			$errMsg="解绑失败";
		} 
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if ($bRet)
		$arr = array('result' =>$bRet,'errmsg' =>'解绑成功');
		else
		$arr = array('result' =>$bRet, 'errmsg' =>$errMsg);
		
		
		return $arr;
	}
	
	/**
	 * updateOrAddRwd
	 * 添加或修改任务点
	 * @param $id,$roleType,$featureId,$userId
	 * @return true or false
	 */
	public function updateOrAddRwd($kdid,$kdmc,$timePartArray,$geometry,$userId,$orgCode){
		$kdmc = iconv("UTF-8", "GBK", $kdmc);
		$bRet = true;
		$errMsg = '';
		$seq ="";
		
		if($kdid!=""){
			$sql = "update ZDT_Duty_TaskPoint set kdmc='$kdmc',geometry=sdo_geometry('$geometry',4326) where kdid='$kdid' ";
		}else{
			$kdid = $this->getSequenceByTable("ZDT_DUTY_TASKPOINT");
			$sql = "insert into ZDT_Duty_TaskPoint(kdid,kdmc,geometry,cjrid,cjrbm) values('$kdid','$kdmc',sdo_geometry('$geometry',4326),'$userId','$orgCode')";
		}
		//echo $sql;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		//$bRet = $this->deletePatrolDate($kdid);
		//$bRet = $this->insertPatrolDate($timePartArray,$kdid);
                $bRet = $this->insertOrUpdatePatrolDate($timePartArray,$kdid);
		return $bRet;
	}
        function insertOrUpdatePatrolDate($timePartArray,$kdid){
            
		$bRet = true;
		$errMsg = "";
		$gids = "";
		$ydcs = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			$insetSql="";
                        $insetSql.=" update ZDT_Duty_PatrolDate set yxzt='0' where kdid ='$kdid'; ";
			for ($i=0;$i<count($timePartArray);$i++) {
				$qsdksj="";$zzdksj="";
				$kdsj = $timePartArray[$i]['kdsj'];
				if($kdsj!=""){
					$sjArr = explode("至",$kdsj);
					$qsdksj =$sjArr[0];
					$zzdksj = $sjArr[1]=="00:00"?"24:00":$sjArr[1];
				}else{
					$qsdksj = "00:00:00";
					$zzdksj = "24:00:00";
				}
		 		$ydcs = $timePartArray[$i]['ydcs'];
		 		$jgsj = $timePartArray[$i]['jgsj'];
		 		$xlid = array_key_exists('xlid',$timePartArray[$i])?$timePartArray[$i]['xlid']:"";
		 		if($xlid=="")
                                {
                                    $insetSql = $insetSql."  insert into ZDT_Duty_PatrolDate(xlid,qsdksj,zzdksj,jgsj,ydcs,kdid)" .
					" values(ZDT_DUTY_PATROLDATE_SEQ.Nextval,'$qsdksj','$zzdksj','$jgsj','$ydcs','$kdid');";
                                }
                            else {
                                    $insetSql = $insetSql."  update ZDT_Duty_PatrolDate set yxzt='1',qsdksj='$qsdksj',zzdksj='$zzdksj',jgsj='$jgsj',ydcs='$ydcs' where " .
					" xlid='$xlid';";
                                }
	 		
			}
			if($insetSql!=""){
				$sql = "begin ".$insetSql." end;";
				//echo $sql;
				$stmt = oci_parse($this->dbconn, $sql);
				if (!@oci_execute($stmt)){
					$bRet = false;
					$errMsg="操作失败";
				} 
				oci_free_statement($stmt);
			}
			oci_close($this->dbconn);
			return $bRet;
	
        }
	function deletePatrolDate($kdid){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update ZDT_Duty_PatrolDate set yxzt='0'  where kdid ='$kdid'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);	
			
		return $bRet;
	}
	
	function insertPatrolDate($timePartArray,$kdid){
		$bRet = true;
		$errMsg = "";
		$gids = "";
		$ydcs = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			$insetSql="";
			for ($i=0;$i<count($timePartArray);$i++) {
				$qsdksj="";$zzdksj="";
				$kdsj = $timePartArray[$i]['kdsj'];
				if($kdsj!=""){
					$sjArr = explode("至",$kdsj);
					$qsdksj =$sjArr[0];
					$zzdksj = $sjArr[1]=="00:00"?"24:00":$sjArr[1];
				}else{
					$qsdksj = "00:00:00";
					$zzdksj = "24:00:00";
				}
		 		$ydcs = $timePartArray[$i]['ydcs'];
		 		$jgsj = $timePartArray[$i]['jgsj'];
		 		
		 		
   				$insetSql = $insetSql."  insert into ZDT_Duty_PatrolDate(xlid,qsdksj,zzdksj,jgsj,ydcs,kdid)" .
					" values(ZDT_DUTY_PATROLDATE_SEQ.Nextval,'$qsdksj','$zzdksj','$jgsj','$ydcs','$kdid');";
	 		
			}
			if($insetSql!=""){
				$sql = "begin ".$insetSql." end;";
				//echo $sql;
				$stmt = oci_parse($this->dbconn, $sql);
				if (!@oci_execute($stmt)){
					$bRet = false;
					$errMsg="操作失败";
				} 
				oci_free_statement($stmt);
			}
			oci_close($this->dbconn);
			return $bRet;
	}
	
	/**
	 * getEquipment
	 * 根据部门ID查询装备列表
	 * @param $orgCode
	 * @return 结果数组
	 */
	public function getRwd($kdmc,$orgCode){
		$kdmc = iconv("UTF-8", "GBK", $kdmc);
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$arr=array('total'=>0,'rows' => $datas);
		$result = array('result' =>false,'errmsg' =>'','records' => $arr);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.kdid, t.kdmc,p.xlid,p.qsdksj,p.zzdksj,p.jgsj,p.ydcs, MDSYS.Sdo_Util.to_wktgeometry_varchar(t.geometry) as geometry from zdt_duty_taskpoint t ";
		$sql = $sql ." left join zdb_organization o on o.orgcode=t.cjrbm left join zdt_duty_patroldate p on p.kdid=t.kdid where t.rwzt='1' and p.yxzt='1' and rownum<200 " ;
		if($kdmc!=""){
			$sql = $sql . " and  t.kdmc like '%$kdmc%'";
		}
		if($orgCode!=""){
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'kdid' => iconv("GBK", "UTF-8", $row["KDID"]),
					'kdmc' => iconv("GBK", "UTF-8", $row["KDMC"]),
					'xlid' => iconv("GBK", "UTF-8", $row["XLID"]),
					'qsdksj' => iconv("GBK", "UTF-8", $row["QSDKSJ"]),
					'zzdksj' => iconv("GBK", "UTF-8", $row["ZZDKSJ"]),
					'jgsj' => iconv("GBK", "UTF-8", $row["JGSJ"]),
					'ydcs' => iconv("GBK", "UTF-8", $row["YDCS"]),
					'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$arr=array('total'=>count($datas),'rows' => $datas);
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	}
	
	/**
	 * updateOrAddRw
	 * 添加或修改任务
	 * @param $rwid,$rwmc,$dianwei,$xunluozhu
	 * @return true or false
		
	public function updateOrAddRw($rwid,$rwmc,$dianwei,$xunluozhu,$userId,$orgCode){
		$rwmc = iconv("UTF-8", "GBK", $rwmc);
		$bRet = true;
		$errMsg = '';
		//根据任务主表并得到rwid
		$result =  $this->updateOrAddTask($rwid,$rwmc,$userId,$orgCode);
		$rwid = $result['rwid'];
		//删除今天以后的任务点绑定的数据
		$bRet =  $this->deletePointBanding($rwid);
		//删除今天以后的任务绑定数据
		$bRet =  $this->deleteTaskBanding($rwid);
		//添加今天以后的任务绑定数据并返回bdid
		$result =  $this->insertTaskBanding($rwid,$rwmc,$xunluozhu,$dianwei,$userId);
		if($result['result']){
			//添加今天以后的任务点绑定数据
			$bRet =  $this->insertPointBanding($rwid,$dianwei,$result['tbids'],$userId);
		}
		$result=array('rwid'=>$rwid,'bRet' => $bRet);
		return $result;
	}
	*/
	/**
	 * updateOrAddRw
	 * 添加或修改任务
	 * @param $rwid,$rwmc,$dianwei,$xunluozhu
	 * @return true or false
	  */ 
	public function updateOrAddRw($rwid,$rwmc,$dianwei,$xunluozhu,$userId,$orgCode){
		$rwmc = iconv("UTF-8", "GBK", $rwmc);
		$bRet = true;
		$errMsg = '';
		//根据任务主表并得到rwid
		$result =  $this->updateOrAddTask($rwid,$rwmc,$userId,$orgCode);
		$rwid = $result['rwid'];
		//删除今天以后的任务点绑定的数据
		//$bRet =  $this->deletePointBanding($rwid);
		//删除今天以后的任务绑定数据
		//$bRet =  $this->deleteTaskBanding($rwid);
		//添加今天以后的任务绑定数据并返回bdid
		//$result =  $this->insertTaskBanding($rwid,$rwmc,$xunluozhu,$dianwei,$userId);
		//if($result['result']){
			//添加今天以后的任务点绑定数据
		//	$bRet =  $this->insertPointBanding($rwid,$dianwei,$result['tbids'],$userId);
		//}
                //---------------重写上面所有方法以一个事物提交
              if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB(); 
                $del_zdt_duty_pointbanding = "delete from zdt_duty_pointbanding t where t.tbid in (select tb.bdid from zdt_duty_taskbanding tb where rwid ='$rwid' and xlrq>=to_char(sysdate,'yyyy-mm-dd'))";
		$stmt = @oci_parse($this->dbconn, $del_zdt_duty_pointbanding);
		@oci_execute($stmt,OCI_DEFAULT);
                $del_zdt_duty_taskbanding = "delete from zdt_duty_taskbanding  where rwid ='$rwid' and xlrq>=to_char(sysdate,'yyyy-mm-dd')";
                $stmt =@ oci_parse($this->dbconn, $del_zdt_duty_taskbanding);
		@oci_execute($stmt,OCI_DEFAULT);
                $ydcs = "";
		$tbids = array();
			for ($i=0;$i<count($dianwei);$i++) {
	 			$ydcs = $ydcs+$dianwei[$i]['ydcs'];
			}
			$insetSql="";
			for ($i=0;$i<count($xunluozhu);$i++) {
		 		$userId = $xunluozhu[$i]['userId'];
		 		$xlrqs = $xunluozhu[$i]['datept'];
		 		$xlrqArr = explode(",",$xlrqs);
		 		for( $j=0;$j<count($xlrqArr);$j++ ) {
		 			$xlrq = $xlrqArr[$j];
	       			if($xlrq){
	       				$seq = $this->getSeq("ZDT_DUTY_TASKBANDING");
	       				$insetSql = $insetSql."  insert into zdt_duty_taskbanding(bdid,rwid,bdsj,czrid,rwmc,xlrq,userid,ydcs)" .
					" values('$seq','$rwid',to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS'),'$userId','$rwmc','$xlrq','$userId','$ydcs');";
						array_push($tbids,array ("tbid"=>$seq));
	       			}
				}
	 		
			}
			if($insetSql!=""){
				$insetSql = "begin ".$insetSql." end;";
				//echo $sql;
				$stmt = oci_parse($this->dbconn, $insetSql);
				@oci_execute($stmt,OCI_DEFAULT);
				
			}
			
                    $insetSql_PB="";
			for ($i=0;$i<count($dianwei);$i++) {
	 		$kdid = $dianwei[$i]['kdid'];
	 		$xlid = $dianwei[$i]['xlid'];
		 		for ($j=0;$j<count($tbids);$j++) {
		 			$tbid = $tbids[$j]['tbid'];
		 		$insetSql_PB = $insetSql_PB."  insert into zdt_duty_pointbanding(bdid,rwid,kdid,bdsj,czrid,tbid,xlid)" .
						" values(ZDT_DUTY_TASKBANDING_SEQ.Nextval,'$rwid','$kdid',to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS'),'$userId','$tbid','$xlid');";
		 		}
			}
			if($insetSql_PB!=""){
				$insetSql_PB = "begin ".$insetSql_PB." end;";
				//echo $sql;
				$stmt = oci_parse($this->dbconn, $insetSql_PB);
				@oci_execute($stmt,OCI_DEFAULT);
				
			}
                
                
                 $committed = @oci_commit($this->dbconn);

                if (!$committed) {
                    @oci_rollback($this->dbconn);
                        $bRet = false;
			$errMsg = '操作失败';
                 }
                @oci_free_statement($stmt);
		@oci_close($this->dbconn);
		$result=array('rwid'=>$rwid,'bRet' => $bRet);
		return $result;
	}
	
        public function getSeq($tableName) {
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
			//echo getOciError($stmt);
			oci_close($this->dbconn);
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$result = iconv("GBK", "UTF-8", $row["SEQ"]);
			}
		}
		oci_free_statement($stmt);
		//oci_close($this->dbconn);

		return $result;
	}
      
	public function updateOrAddTask($rwid,$rwmc,$userId,$orgCode){
		$bRet = true;
		$errMsg = '';
		if($rwid!=""){
			$sql = "update zdt_duty_task set rwmc='$rwmc' where rwid='$rwid' ";
		}else{
			$rwid = $this->getSequenceByTable("ZDT_DUTY_TASK");
			$sql = "insert into zdt_duty_task(rwid,rwmc,cjsj,cjrid,cjrbm) values('$rwid','$rwmc',to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS'),'$userId','$orgCode')";
		}
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$result=array('rwid'=>$rwid,'bRet' => $bRet);
		return $result;
	}
	
	public function deleteTaskBanding($rwids){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "delete from zdt_duty_taskbanding  where rwid in ($rwids) and xlrq>=to_char(sysdate,'yyyy-mm-dd')";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);	
			
		return $bRet;
		
	}
	
	public function insertTaskBanding($rwid,$rwmc,$xunluozhu,$dianwei,$cjrId){
		$bRet = true;
		$errMsg = "";
		$gids = "";
		$ydcs = "";
		$result = array();
		$datas = array();
			for ($i=0;$i<count($dianwei);$i++) {
	 			$ydcs = $ydcs+$dianwei[$i]['ydcs'];
			}
			$insetSql="";
			for ($i=0;$i<count($xunluozhu);$i++) {
		 		$userId = $xunluozhu[$i]['userId'];
		 		$xlrqs = $xunluozhu[$i]['datept'];
		 		$xlrqArr = explode(",",$xlrqs);
		 		for( $j=0;$j<count($xlrqArr);$j++ ) {
		 			$xlrq = $xlrqArr[$j];
	       			if($xlrq){
	       				$seq = $this->getSequenceByTable("ZDT_DUTY_TASKBANDING");
	       				$insetSql = $insetSql."  insert into zdt_duty_taskbanding(bdid,rwid,bdsj,czrid,rwmc,xlrq,userid,ydcs)" .
					" values('$seq','$rwid',to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS'),'$cjrId','$rwmc','$xlrq','$userId','$ydcs');";
						array_push($datas,array ("tbid"=>$seq));
	       			}
				}
	 		
			}
			if($insetSql!=""){
				$sql = "begin ".$insetSql." end;";
				//echo $sql;
				if ($this->dbconn == null)
				$this->dbconn = $this->LogonDB();
				$stmt = oci_parse($this->dbconn, $sql);
				if (!@oci_execute($stmt)){
					$bRet = false;
					$errMsg="绑定失败";
				} 
				oci_free_statement($stmt);
				oci_close($this->dbconn);
			}
			$result = array("result"=>$bRet,"tbids"=>$datas);
			return $result;
		
	}
	
	public function deletePointBanding($rwids){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "delete from zdt_duty_pointbanding t where t.tbid in (select tb.bdid from zdt_duty_taskbanding tb where rwid in ($rwids) and xlrq>=to_char(sysdate,'yyyy-mm-dd'))";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);	
			
		return $bRet;
		
	}
	
	public function insertPointBanding($rwid,$dianwei,$tbids,$userId){
		$bRet = true;
		$errMsg = "";
		$gids = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			$insetSql="";
			for ($i=0;$i<count($dianwei);$i++) {
	 		$kdid = $dianwei[$i]['kdid'];
	 		$xlid = $dianwei[$i]['xlid'];
		 		for ($j=0;$j<count($tbids);$j++) {
		 			$tbid = $tbids[$j]['tbid'];
		 		$insetSql = $insetSql."  insert into zdt_duty_pointbanding(bdid,rwid,kdid,bdsj,czrid,tbid,xlid)" .
						" values(ZDT_DUTY_TASKBANDING_SEQ.Nextval,'$rwid','$kdid',to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS'),'$userId','$tbid','$xlid');";
		 		}
			}
			if($insetSql!=""){
				$sql = "begin ".$insetSql." end;";
				//echo $sql;
				$stmt = oci_parse($this->dbconn, $sql);
				if (!@oci_execute($stmt)){
					$bRet = false;
					$errMsg="绑定失败";
				} 
				oci_free_statement($stmt);
			}
			oci_close($this->dbconn);
			return $bRet;
		
	}
	
	/**
	 * getRw
	 * 根据部门编码查询巡逻路线
	 * @param $orgCode
	 * @return 巡逻路线组
	 */
	public function getRw($rwmc,$orgCode,$page,$rows){
		$rwmc = iconv("UTF-8", "GBK", $rwmc);
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		$arr=array('total'=>0,'rows' => $datas);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(*) ROWCOUNT from ( " ;
		$sql = $sql . " select t.rwid   from zdt_duty_task t ";
		$sql = $sql . " left join zdb_user usr on t.cjrid = usr.userid ";
		$sql = $sql . " left join zdb_organization o on o.orgcode = usr.bz ";
		$sql = $sql . " left join zdt_duty_taskbanding tb on tb.rwid=t.rwid ";
		$sql = $sql . " left join zdt_duty_pointbanding pb on tb.bdid=pb.tbid ";
		$sql = $sql . " left join zdt_duty_taskpoint tp on tp.kdid=pb.kdid ";
		$sql = $sql . " left join zdt_duty_patroldate pd on pb.xlid=pd.xlid ";
		$sql = $sql . " where t.rwzt = '1' and tp.rwzt='1' and pd.yxzt='1' and tb.xlrq>=to_char(sysdate,'yyyy-mm-dd') and tp.kdid is not null and pd.xlid is not null";
		if($orgCode!=""){
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		if($rwmc!=""){
		$sql = $sql . " and t.rwmc like '%$rwmc%'";
		}
		$sql = $sql . " group by t.rwid) ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt,"ROWCOUNT",$row_count);
		if (!@oci_execute($stmt)) {
	  		$bRet = false;
	  		$errMsg="查询失败";
		}else{
			
		 /*处理分页*/
			oci_fetch($stmt);
			$total_rec = $row_count;
			oci_free_statement($stmt);

		/*
		$sql = "select t.rwid,t.rwmc,t.cjsj,t.cjrid,usr.username,o.orgname from zdt_duty_task t " .
				" left join zdb_user usr on t.cjrid=usr.userid" .
				" left join zdb_organization o on o.orgcode=usr.bz where t.rwzt='1' " ;
		if($orgCode!=""){
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		if($rwmc!=""){
		$sql = $sql . " and rwmc like '%$rwmc%'";
		}
		*/
		$sql = " select t.rwid,t.rwmc,t.cjsj,t.cjrid,usr.username,o.orgname   from zdt_duty_task t ";
		$sql = $sql . " left join zdb_user usr on t.cjrid = usr.userid ";
		$sql = $sql . " left join zdb_organization o on o.orgcode = usr.bz ";
		$sql = $sql . " left join zdt_duty_taskbanding tb on tb.rwid=t.rwid ";
		$sql = $sql . " left join zdt_duty_pointbanding pb on tb.bdid=pb.tbid ";
		$sql = $sql . " left join zdt_duty_taskpoint tp on tp.kdid=pb.kdid ";
		$sql = $sql . " left join zdt_duty_patroldate pd on pb.xlid=pd.xlid ";
		$sql = $sql . " where t.rwzt = '1' and tp.rwzt='1' and pd.yxzt='1' and tb.xlrq>=to_char(sysdate,'yyyy-mm-dd') and tp.kdid is not null and pd.xlid is not null";
		if($orgCode!=""){
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		if($rwmc!=""){
		$sql = $sql . " and t.rwmc like '%$rwmc%'";
		}
		$sql = $sql . " group by t.rwid,t.rwmc,t.cjsj,t.cjrid,usr.username,o.orgname ";


		//echo $sql;
		$sql = pageResultSet($sql, $page, $rows);
		//echo $sql;	
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			$bRet = false;
			$errMsg="查询失败";
		}else{
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$rwid = iconv("GBK", "UTF-8", $row["RWID"]);
				$dianwei = $this->getRwdByRwId($rwid);
				$xunluozhu = $this->getGroupCarByRwId($rwid);
				if(count($dianwei)==0||count($xunluozhu)==0){
					//$prints= $prints.";".$rwid;
					//echo $prints;
					continue;
				}
				$men = array (
					'rwid' => $rwid,
					'rwmc' => iconv("GBK", "UTF-8", $row["RWMC"]),
					'cjsj' => iconv("GBK", "UTF-8", $row["CJSJ"]),
					'cjrid' => iconv("GBK", "UTF-8", $row["CJRID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'dianwei' => $dianwei,
					'xunluozhu' => $xunluozhu
				);
				array_push($datas, $men);
				}
			oci_free_statement($stmt);
			oci_close($this->dbconn);
			$arr=array('total'=>$total_rec,'rows' => $datas);
			}
		}
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	}
	
	function getRwdByRwId($rwid){
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.kdid, t.kdmc,pd.xlid,pd.qsdksj,pd.zzdksj,pd.jgsj,pd.ydcs,t.sdcs, MDSYS.Sdo_Util.to_wktgeometry_varchar(t.geometry) as geometry from zdt_duty_taskbanding tb";
		$sql = $sql . " left join zdt_duty_pointbanding pb on tb.bdid=pb.tbid";
		$sql = $sql . " left join zdt_duty_taskpoint t on t.kdid=pb.kdid";
		$sql = $sql . " left join zdt_duty_patroldate pd on pd.xlid=pb.xlid"; 
		$sql = $sql . " where  t.rwzt='1' and pd.yxzt='1' and  tb.rwid = '$rwid' and tb.xlrq>=to_char(sysdate,'yyyy-mm-dd')";
		$sql = $sql . " group by t.kdid, t.kdmc,pd.xlid,pd.qsdksj,pd.zzdksj,pd.jgsj,pd.ydcs,t.sdcs, MDSYS.Sdo_Util.to_wktgeometry_varchar(t.geometry)";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'kdid' => iconv("GBK", "UTF-8", $row["KDID"]),
					'kdmc' => iconv("GBK", "UTF-8", $row["KDMC"]),
					'qsdksj' => iconv("GBK", "UTF-8", $row["QSDKSJ"]),
					'zzdksj' => iconv("GBK", "UTF-8", $row["ZZDKSJ"]),
					'jgsj' => iconv("GBK", "UTF-8", $row["JGSJ"]),
					'ydcs' => iconv("GBK", "UTF-8", $row["YDCS"]),
					'xlid' => iconv("GBK", "UTF-8", $row["XLID"]),
					'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"]),
					'kdsj' => iconv("GBK", "UTF-8", $row["QSDKSJ"])."至".iconv("GBK", "UTF-8", $row["ZZDKSJ"])
				);
				array_push($datas, $data);
			}
		}
		return $datas;
	}
	
	function getRwdByParam($rwid,$tbid,$userId){
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.kdid, t.kdmc,pd.xlid,pd.qsdksj,pd.zzdksj,pd.jgsj,pd.ydcs,t.sdcs,pb.tbid, MDSYS.Sdo_Util.to_wktgeometry_varchar(t.geometry) as geometry from zdt_duty_taskpoint t  ";
		$sql = $sql . " left join zdt_duty_pointbanding pb on pb.kdid=t.kdid  ";
		$sql = $sql . " left join zdt_duty_patroldate pd on pd.xlid=pb.xlid  ";
		//$sql = $sql . " where t.rwzt='1' and pd.yxzt='1' and pb.rwid = '$rwid' and pb.tbid='$tbid'  ";
		$sql = $sql . " where pb.rwid = '$rwid' and pb.tbid='$tbid'  ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$kdid = iconv("GBK", "UTF-8", $row["KDID"]);
				$xlid = iconv("GBK", "UTF-8", $row["XLID"]);
				$clockParam = $this->getClockParam($rwid,$userId,$xlid,$kdid);
				$data = array (
					'kdid' => iconv("GBK", "UTF-8", $row["KDID"]),
					'kdmc' => iconv("GBK", "UTF-8", $row["KDMC"]),
					'qsdksj' => iconv("GBK", "UTF-8", $row["QSDKSJ"]),
					'zzdksj' => iconv("GBK", "UTF-8", $row["ZZDKSJ"]),
					'jgsj' => iconv("GBK", "UTF-8", $row["JGSJ"]),
					'ydcs' => iconv("GBK", "UTF-8", $row["YDCS"]),
					'sdcs' => $clockParam['ydkcs'],
					'xlid' => iconv("GBK", "UTF-8", $row["XLID"]),
					'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"]),
					'kdsj' => iconv("GBK", "UTF-8", $row["QSDKSJ"])."至".iconv("GBK", "UTF-8", $row["ZZDKSJ"])
				);
				array_push($datas, $data);
			}
		}
		return $datas;
	}
	
	function getGroupCarByRwId($rwid){
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = " select tb.userid,usr.username,o.orgcode,o.orgname,to_char(wm_concat(tb.xlrq)) as xlrq from zdt_duty_taskbanding tb  ";
		//$sql = " select tb.userid,usr.username,o.orgcode,o.orgname,listagg(tb.xlrq,',') within group (order by tb.xlrq) as xlrq from zdt_duty_taskbanding tb  ";
		$sql = $sql . " left join zdb_user usr on usr.userid=tb.userid ";
		$sql = $sql . " left join zdb_organization o on o.orgcode=usr.bz ";
		$sql = $sql . " where 1=1 and tb.rwid = '$rwid' and tb.xlrq>=to_char(sysdate,'yyyy-mm-dd')";
		$sql = $sql . " group by tb.userid,usr.username,o.orgcode,o.orgname";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'userId' => iconv("GBK", "UTF-8", $row["USERID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'datept' => iconv("GBK", "UTF-8", $row["XLRQ"])
				);
				array_push($datas, $data);
			}
		}
		return $datas;
	}
	
	/**
	 * getAndroidTask
	 * 根据身份证编号查询任务信息
	 * 参数若为userId则查询该用户所属组合
	 * @param $orgCode,$userId
	 * @return组合列表数组
	 */
	public function getAndroidTask($userId){
		$bRet = true;
		$errMsg = "";
		$mens = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$result = array('result' =>false,'errmsg' =>'','records' => $mens);
		//$sql = "select t.rwid, t.rwmc,tb.gid,tb.xlrq,tb.bdid from zdt_duty_task t " ;
		//$sql = $sql . " left join zdt_duty_taskbanding tb on tb.rwid = t.rwid ";
		//$sql = $sql . " where t.rwzt='1' and tb.userid='$userId' and tb.xlrq=to_char(sysdate,'yyyy-mm-dd') ";
		$sql="select distinct a.rwid, a.rwmc,a.gid,a.xlrq,a.bdid from zdt_duty_taskbanding a left join zdt_duty_task b on a.rwid=b.rwid left join zdt_duty_pointbanding c on a.bdid=c.tbid left join zdt_duty_patroldate d on c.xlid=d.xlid  where userid ='$userId' and xlrq=to_char(sysdate,'yyyy-mm-dd') and rwzt='1' and d.yxzt='1'";
		//$sql = $sql . " where t.rwzt='1' and tb.userid='$userId' and tb.xlrq='2015-11-21' ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			$i=0;
			while (($row = oci_fetch_assoc($stmt)) != false) {
				//$i++;
				//echo "i=======".$i;
				$rwid = iconv("GBK", "UTF-8", $row["RWID"]);
				$tbid = iconv("GBK", "UTF-8", $row["BDID"]);
				$dianwei = $this->getRwdByParam($rwid,$tbid,$userId);
				$men = array (
					'rwid' => $rwid,
					'rwmc' => iconv("GBK", "UTF-8", $row["RWMC"]),
					'xlrq' => iconv("GBK", "UTF-8", $row["XLRQ"]),
					'points' => $dianwei
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		if ($this->dbconn != null)
		oci_close($this->dbconn);
		$result = array('result' =>$bRet,'errmsg' =>'','records' => $mens);
		return $result;
	}
	
	/**
	 * getAndroidTask
	 * 根据身份证编号查询任务信息
	 * 参数若为userId则查询该用户所属组合
	 * @param $orgCode,$userId
	 * @return组合列表数组
	 */
	public function getGroupByUserId($userId){
		$bRet = true;
		$errMsg = "";
		$men = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select t.gid from zdt_duty_group t where instr(t.userid,'$userId')>0 and t.status!='3'" ;
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'gid' => iconv("GBK", "UTF-8", $row["GID"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $men;
	}
	
	public function insertAutoPoint($rwid,$sfzh,$kdid,$xlid,$geometry,$ydcs,$jgsj){
		$bRet = true;
		$errMsg = "";
		$gid = "";
		$result = array('result' =>$bRet,'errmsg' =>'','records' => array());
		$clockParam = $this->getClockParam($rwid,$sfzh,$xlid,$kdid);
		if($clockParam['ydkcs']>=$ydcs){
			$bRet = false;
			$errMsg="已完成打卡";
			$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $clockParam);
			return $result;
		}
		date_default_timezone_set('Asia/Shanghai'); //设置时区
		$nowDate = strtotime(date("Y-m-d H:i:s"));
		$lastDate = strtotime($clockParam['zhdksj']);
		$diffDate = $nowDate-$lastDate;
		if($diffDate<$jgsj){
			$bRet = false;
			$errMsg="打卡次数过频";
			$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $clockParam);
			return $result;
		}
		
		$taskEntity = $this->getTaskAndTaskPoint($sfzh,$rwid);
		$rwmc = isset($taskEntity['rwmc'])?iconv("UTF-8", "GBK",$taskEntity['rwmc']):"";
		$tbid = isset($taskEntity['tbid'])?iconv("UTF-8", "GBK",$taskEntity['tbid']):"";
		$taskPointEntity = $this->getTaskPointByKdId($kdid);
		$kdmc = isset($taskPointEntity['kdmc'])?iconv("UTF-8", "GBK",$taskPointEntity['kdmc']):"";
		
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			if($geometry!=""){
				$sql = "  insert into zdt_duty_clock(id,kdid,gid,rwid,sdcs,zhdksj,geometry,dklx,shzt,userid,xlid,rwmc,kdmc,tbid)" .
					" values(ZDT_Duty_CLOCK_SEQ.Nextval,'$kdid','$gid','$rwid','1',to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS'),sdo_geometry('$geometry',4326),'1','2','$sfzh','$xlid','$rwmc','$kdmc','$tbid')";
			}else{
				$sql = "  insert into zdt_duty_clock(id,kdid,gid,rwid,sdcs,zhdksj,dklx,shzt,userid,xlid,rwmc,kdmc,tbid)" .
					" values(ZDT_Duty_CLOCK_SEQ.Nextval,'$kdid','$gid','$rwid','1',to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS'),'1','2','$sfzh','$xlid','$rwmc','$kdmc','$tbid')";
			}
	 		
			//echo $sql;
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)){
				$bRet = false;
				$errMsg="打卡失败";
			} 
			oci_free_statement($stmt);
			oci_close($this->dbconn);
			$clockParam = $this->getClockParam($rwid,$sfzh,$xlid,$kdid);
			$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $clockParam);
			return $result;
	}
	
	/**
	 * getPoliceFeatureByJqid
	 * 根据featureId查询警情位置信息表count
	 * @param $featureId
	 * @return true or false
	 */
	public function getClockParam($rwid,$userId,$xlid,$kdid) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		$men = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(*) as ROWCOUNT,max(t.zhdksj) as zhdksj from zdt_duty_clock t  ";
		$sql = $sql . " left join zdt_duty_patroldate pd on pd.xlid=t.xlid ";
		$sql = $sql . " where  t.zhdksj like '%'||to_char(sysdate,'yyyy-mm-dd')||'%' ";
		//$sql = "select count(*) as ROWCOUNT,max(t.zhdksj) as zhdksj from zdt_duty_clock t where t.zhdksj like '%to_char(sysdate,\"yyyy-mm-dd\")%'   ";
		if($xlid!=""){
			  ;
			 $sql = $sql . " and pd.xlid='$xlid' ";
		}
		if($rwid!=""){
			$sql = $sql . " and t.rwid='$rwid' ";
		}
		if($userId!=""){
			$sql = $sql . " and t.userid='$userId' ";
		}
		if($kdid!=""){
			$sql = $sql . " and t.kdid='$kdid' ";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'ydkcs' => iconv("GBK", "UTF-8", $row["ROWCOUNT"]),
					'zhdksj' => iconv("GBK", "UTF-8", $row["ZHDKSJ"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $men;

	}
	
	public function insertHandPoint($rwid,$userid,$kdid,$xlid,$url){
		$bRet = true;
		$errMsg = "";
		$result = array('result' =>$bRet,'errmsg' =>'','records' => array());
		
		$taskEntity = $this->getTaskAndTaskPoint($userid,$rwid);
		//$taskEntity = $this->getTaskByRwId($rwid);
		$tbid = isset($taskEntity['tbid'])?iconv("UTF-8", "GBK",$taskEntity['tbid']):"";
		$rwmc = isset($taskEntity['rwmc'])?iconv("UTF-8", "GBK",$taskEntity['rwmc']):"";
		$taskPointEntity = $this->getTaskPointByKdId($kdid);
		$kdmc = isset($taskPointEntity['kdmc'])?iconv("UTF-8", "GBK",$taskPointEntity['kdmc']):"";
		
		$sql = "  insert into zdt_duty_clock(id,kdid,rwid,sdcs,zhdksj,tpdz,dklx,shzt,userid,xlid,rwmc,kdmc,tbid)" .
			" values(ZDT_Duty_CLOCK_SEQ.Nextval,'$kdid','$rwid','1',to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS'),'$url','2','1','$userid','$xlid','$rwmc','$kdmc','$tbid')";
 		if ($this->dbconn == null)
		$this->dbconn = $this->LogonDB();
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)){
			$bRet = false;
			$errMsg="打卡失败";
		} 
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$clockParam = $this->getClockParam($rwid,$userid,$xlid,$kdid);
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $clockParam);
		return $result;
	}
	
	public function deleteRw($rwEntity){
		$bRet = true;
		$errMsg = "";
		$rwids = "";
		for ($i=0;$i<count($rwEntity);$i++) {
			$rwid = "'"+$rwEntity[$i]['rwid']+"'";
			$p = $i==0 ? "" : ",";
			$rwids .= $p.$rwid;
		}
		$result =  $this->deleteTask($rwids);
		$bRet =  $this->deleteTaskBanding($rwid);
		$bRet =  $this->deletePointBanding($rwid);
			
		return $bRet;
		
	}
	
	public function deleteTask($rwids){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "delete from zdt_duty_task  where rwid in ($rwids)";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);	
			
		return $bRet;
		
	}
	
	/**
	 * getRwdPage
	 * 根据部门编码查询巡逻路线
	 * @param $orgCode
	 * @return 巡逻路线组
	 */
	public function getRwdPage($kdmc,$qsdksj,$zzdksj,$orgCode,$page,$rows){
		$kdmc = iconv("UTF-8", "GBK", $kdmc);
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		$arr=array('total'=>0,'rows' => $datas);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(*) ROWCOUNT from (";
		$sql = $sql . "select t.kdid from zdt_duty_taskpoint t ";
		$sql = $sql . " left join zdb_organization o on o.orgcode=t.cjrbm left join zdt_duty_patroldate p on p.kdid=t.kdid where 1=1 and rwzt='1' " ;
		
		if($kdmc!=""){
			$sql = $sql . " and kdmc like '%$kdmc%'";
		}
		if($qsdksj!=""&&$zzdksj!=""){
		$sql = $sql . " and ('$qsdksj' between p.qsdksj and p.zzdksj  or  '$zzdksj' between  p.qsdksj and p.zzdksj or  p.qsdksj  between '$qsdksj' and '$zzdksj' or p.zzdksj between '$qsdksj' and '$zzdksj')";
		}
		if($orgCode!=""){
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		$sql = $sql . " group by t.kdid,t.kdmc,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.geometry))";
		//echo $sql;	
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt,"ROWCOUNT",$row_count);
		if (!@oci_execute($stmt)) {
	  		$bRet = false;
	  		$errMsg="查询失败";
		}else{
			
		 /*处理分页*/
			oci_fetch($stmt);
			$total_rec = $row_count;
			oci_free_statement($stmt);


		$sql = "select t.kdid,t.kdmc,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.geometry) as geometry from zdt_duty_taskpoint t ";
		$sql = $sql ." left join zdb_organization o on o.orgcode=t.cjrbm left join zdt_duty_patroldate p on p.kdid=t.kdid where 1=1 and rwzt='1' " ;
		
		if($kdmc!=""){
			$sql = $sql . " and kdmc like '%$kdmc%'";
		}
		if($qsdksj!=""&&$zzdksj!=""){
		$sql = $sql . " and ('$qsdksj' between p.qsdksj and p.zzdksj  or  '$zzdksj' between  p.qsdksj and p.zzdksj or  p.qsdksj  between '$qsdksj' and '$zzdksj' or p.zzdksj between '$qsdksj' and '$zzdksj')";
		}
		if($orgCode!=""){
		$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		$sql = $sql . " group by t.kdid,t.kdmc,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.geometry) order by kdid desc ";
		//echo $sql;	
		$sql = pageResultSet($sql, $page, $rows);
		
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			$bRet = false;
			$errMsg="查询失败";
		}else{
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$kdid = iconv("GBK", "UTF-8", $row["KDID"]);
				$timePartArray =  $this->getPatrolDateByKdid($kdid);
				$men = array (
					'kdid' => $kdid,
					'kdmc' => iconv("GBK", "UTF-8", $row["KDMC"]),
					'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"]),
					'timePartArray' => $timePartArray
				);
				array_push($datas, $men);
				}
			oci_free_statement($stmt);
			oci_close($this->dbconn);
			$arr=array('total'=>$total_rec,'rows' => $datas);
			}
		}
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	}
	
	public function getPatrolDateByKdid($kdid){
		$bRet = true;
		$errMsg = "";
		$men = array ();
		$datas = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select t.xlid,t.qsdksj,t.zzdksj,t.jgsj,t.ydcs,t.kdid from ZDT_Duty_PatrolDate t where t.yxzt='1' and  t.kdid ='$kdid'" ;
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'xlid' => iconv("GBK", "UTF-8", $row["XLID"]),
					'qsdksj' => iconv("GBK", "UTF-8", $row["QSDKSJ"]),
					'zzdksj' => iconv("GBK", "UTF-8", $row["ZZDKSJ"]),
					'jgsj' => iconv("GBK", "UTF-8", $row["JGSJ"]),
					'ydcs' => iconv("GBK", "UTF-8", $row["YDCS"]),
					'kdid' => iconv("GBK", "UTF-8", $row["KDID"]),
					'kdsj' => iconv("GBK", "UTF-8", $row["QSDKSJ"])."至".iconv("GBK", "UTF-8", $row["ZZDKSJ"])
				);
				array_push($datas, $men);
			}
		}
		return $datas;
	}
	
	public function deleteRwd($rwdEntity){
		$bRet = true;
		$errMsg = "";
		$kdids = "";
		for ($i=0;$i<count($rwdEntity);$i++) {
			$kdid = "'"+$rwdEntity[$i]['kdid']+"'";
			$p = $i==0 ? "" : ",";
			$kdids .= $p.$kdid;
		}
		$bRet =  $this->deleteTaskPoint($kdids);
		//$bRet =  $this->deletePointBandingByKdids($kdids);
			
		return $bRet;
		
	}
	
	public function deleteTaskPoint($kdids){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update zdt_duty_taskpoint t set t.rwzt='0'  where kdid in ($kdids)";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);	
			
		return $bRet;
		
	}
	
	public function deletePointBandingByKdids($kdids){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "delete from zdt_duty_pointbanding  where kdid in ($kdids)";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);	
			
		return $bRet;
		
	}
	
	function getRwdSearch($rwdEntity,$qsdksj,$zzdksj){
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$sql = "select t.kdid,t.kdmc,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.geometry) as geometry  from zdt_duty_taskpoint t  ";
		$sql = $sql . " where t.rwzt='1'   and t.kdid in (select tp.kdid from zdt_duty_taskbanding tb ";
		$sql = $sql . " left join zdt_duty_pointbanding pb on pb.tbid=tb.bdid ";	
		$sql = $sql . " left join zdt_duty_taskpoint tp on tp.kdid=pb.kdid ";	
		$sql = $sql . " left join zdt_duty_patroldate pd on pd.kdid=tp.kdid ";	
		$sql = $sql . " left join zdb_user usr on usr.userid=tb.userid ";
		$sql = $sql . " left join zdb_organization o on o.orgcode=usr.bz ";		
		$sql = $sql . " left join zdt_duty_task tk on tk.rwid=tb.rwid   ";	
		$sql = $sql . " where tk.rwzt='1' ";		
//		if($qsdksj!=""&&$zzdksj!=""){
//		$sql = $sql . " and (t.qsdksj between '$qsdksj' and '$zzdksj' or  t.zzdksj between '$qsdksj' and '$zzdksj')";
//		}
		if($qsdksj!=""&&$zzdksj!=""){
				$sql = $sql . " and ('$qsdksj' between pd.qsdksj and pd.zzdksj  or  '$zzdksj' between  pd.qsdksj and pd.zzdksj or  pd.qsdksj  between '$qsdksj' and '$zzdksj' or pd.zzdksj between '$qsdksj' and '$zzdksj')";
		}
		$bmSelectSql = "";
		if(count($rwdEntity)>0){
			for ($i=0;$i<count($rwdEntity);$i++) {
				$bmdm = $rwdEntity[$i]['value'];
				$bmSql = " o.parenttreepath like '%$bmdm%' or o.orgCode = '$bmdm'";
				$p = $i==0 ? "and(" : "or";
				$e = $i==count($rwdEntity)-1 ? ")" : "";
				$bmSelectSql .= $p.$bmSql.$e;
			}
		}
		$sql = $sql .$bmSelectSql. " ) ";	
		//	echo $sql;
			if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@ oci_execute($stmt)) {
				$bRet = false;
				echo getOciError($stmt);
				oci_close($this->dbconn);
				$errMsg = '查询失败';
			} else {
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$kdid = iconv("GBK", "UTF-8", $row["KDID"]);
					$timePartArray =  $this->getPatrolDateByKdid($kdid);
					$data = array (
						'kdid' => $kdid,
						'kdmc' => iconv("GBK", "UTF-8", $row["KDMC"]),
						'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"]),
						'timePartArray' => $timePartArray
					);
					array_push($datas, $data);
				}
			}
			oci_free_statement($stmt);
			oci_close($this->dbconn);
			$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $datas);
		return $result;
	}
	
	public function deleteQy($qyEntity){
		$bRet = true;
		$errMsg = "";
		$featureIds = "";
		for ($i=0;$i<count($qyEntity);$i++) {
			$featureId = "'"+$qyEntity[$i]['featureId']+"'";
			$p = $i==0 ? "" : ",";
			$featureIds .= $p.$featureId;
		}
		$bRet =  $this->deleteFeatures($featureIds);
		$bRet =  $this->deletePoliceFeatureByFeatureId($featureIds);
			
		return $bRet;
		
	}
	
	/**
	 * deleteFeature
	 * 删除巡逻路线信息
	 */
	 
	public function deleteFeatures($featureIds){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = " delete from ZDT_Duty_Feature where featureId in ($featureIds) ";
		//echo  $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)){
			$bRet = false;
			$errMsg="删除信息失败";
		} 
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		
		return $bRet;
	}
	
	/**
	 * deletePoliceFeature
	 * 解除巡逻路线绑定信息
	 */
	 
	public function deletePoliceFeatureByFeatureId($featureId){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = " delete from ZDT_Duty_PoliceFeature where featureId in ($featureId)";
		//echo  $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)){
			$bRet = false;
			$errMsg="解绑失败";
		} 
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		
		return $bRet;
	}
	
	/**
	 * 访问外部接口
	 */
	public function loadFeautures($url,$params,$userId)
	{
		$post_data['userName'] =	isset($userId)	?	urlencode($userId) : ""	;
		$data['data']=	array($post_data);	
		$param = json_encode($data, JSON_UNESCAPED_UNICODE);
		$params = $params.$param;
		//echo $url.$params;
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
    
		$result = Json_decode($result,true);
		
        return $result;
	}
	
	/**
	 * getTaskByRwId
	 * 根据任务ID查询任务对象
	 * @param $featureId
	 * @return true or false
	 */
	public function getTaskByRwId($rwid) {
		$bRet = true;
		$errMsg = '';
		$men = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select rwmc,rwid from zdt_duty_task t where rwid='$rwid'  ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'rwmc' => iconv("GBK", "UTF-8", $row["RWMC"]),
					'rwid' => iconv("GBK", "UTF-8", $row["RWID"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $men;

	}
	
	/**
	 * getTaskPointByKdId
	 * 根据卡点ID查询卡点对象
	 * @param $featureId
	 * @return true or false
	 */
	public function getTaskPointByKdId($kdid) {
		$bRet = true;
		$errMsg = '';
		$men = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select kdmc,kdid from zdt_duty_taskpoint t where kdid='$kdid'  ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'kdmc' => iconv("GBK", "UTF-8", $row["KDMC"]),
					'kdid' => iconv("GBK", "UTF-8", $row["KDID"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $men;

	}
	
	/**
	 * getAndroidTask
	 * 根据身份证编号查询任务信息
	 * 参数若为userId则查询该用户所属组合
	 * @param $orgCode,$userId
	 * @return组合列表数组
	 */

	public function getTaskAndTaskPoint($userId,$rwid){
		$bRet = true;
		$errMsg = '';
		$men = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select t.rwid, t.rwmc,tb.xlrq,tb.bdid from zdt_duty_task t " ;
		$sql = $sql . " left join zdt_duty_taskbanding tb on tb.rwid = t.rwid ";
		$sql = $sql . " where t.rwzt='1' and tb.userid='$userId' and tb.xlrq=to_char(sysdate,'yyyy-mm-dd') and t.rwid='$rwid' ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'rwid' => iconv("GBK", "UTF-8", $row["RWID"]),
					'rwmc' => iconv("GBK", "UTF-8", $row["RWMC"]),
					'tbid' => iconv("GBK", "UTF-8", $row["BDID"]),
					'xlrq' => iconv("GBK", "UTF-8", $row["XLRQ"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $men;
	}
/*	
	public function updateRwGeometry($rwid,$zcd,$geometry){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update zdt_duty_task set zcd='$zcd',geometry=sdo_geometry('$geometry',4326)  where rwid ='$rwid'";
		$stmt = oci_parse($this->dbconn, $sql);
		//echo $sql;
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);	
			
		return $bRet;
		
	}
*/
	public function updateRwGeometry($rwid,$zcd,$geometry){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$up_sql = "update zdt_duty_task set zcd='$zcd',line = EMPTY_CLOB() WHERE rwid = '$rwid' RETURNING line INTO :mylob";
		$stmt = oci_parse($this->dbconn, $up_sql);
                 $mylob = oci_new_descriptor($this->dbconn,OCI_D_LOB);
                 oci_bind_by_name($stmt,':mylob',$mylob, -1, OCI_B_CLOB);
    // Execute the statement using OCI_DEFAULT (begin a transaction)
                 oci_execute($stmt, OCI_NO_AUTO_COMMIT) or die ("Unable to execute query\n");
                 if ( !$mylob->save($geometry) ) {
                // Rollback the procedure
                echo $geometry;
                oci_rollback($this->dbconn);
                        $bRet = false;
			$errMsg = '操作失败';
                }
				//oci_commit($this->dbconn);
                $sql="update zdt_duty_task set geometry=sdo_geometry(line,4326) where rwid='$rwid'";
                $stmt = oci_parse($this->dbconn, $sql);
                // Everything OK so commit
				if (!@ oci_execute($stmt)) {
				$bRet = false;
				$errMsg = '操作失败';
				}
                oci_commit($this->dbconn);
		oci_close($this->dbconn);	
			
		return $bRet;
		
	}
}
?>
