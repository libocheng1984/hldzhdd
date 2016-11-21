<?php
/**
 * class：SystemDB
 * version: 1.0
 * 系统管理类
 * author: carl
 * 2014/6/17
 * 
 * 此类定义系统管理和字典下载所需方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class SystemDB extends TpmsDB {
	/**
	 * LoginSystem
	 * 登陆系统
	 */
	public function LoginSystem($userId, $password) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//判断用户名是否存在
		$sql="select usr.id,usr.userId,usr.userName,usr.alarm, org.id,org.orgCode,org.orgname  from zdb_user usr, zdb_organization org where usr.userid='$userId' and usr.password='$password' and usr.bz=org.orgcode";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			$bRet = false;
		} else {
			if (($row=oci_fetch_assoc($stmt))!= false) {
				$data = array(
					'id' => iconv("GBK","UTF-8",$row["ID"]),
					'userId' => iconv("GBK","UTF-8",$row["USERID"]),
					'userName' => iconv("GBK","UTF-8",$row["USERNAME"]),
					'userCode' => iconv("GBK","UTF-8",$row["ALARM"]),
					'orgCode' => iconv("GBK","UTF-8",$row["ORGCODE"]),
					'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"])
				);
				// 新增写入日志表
                $sql_insert_longin_log="insert into login_log (userid) values('$userId')";
                $stmt = oci_parse($this->dbconn, $sql_insert_longin_log);
                oci_execute($stmt);
			} else
				$bRet = false;
		}
		OCIFreeStatement($stmt);
		
		if (!$bRet)
			return false;

		return $data;
	}
	
	/**
	 * LoginSystem
	 * 登陆系统
	 */
	public function getUserRole($userId) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//判断用户名是否存在
		$sql="select usr.id,usr.userId,usr.userName,usr.alarm, org.id,org.orgCode,org.orgname  from zdb_user usr, zdb_organization org where usr.userid='$userId' and usr.bz=org.orgcode";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			$bRet = false;
		} else {
			if (($row=oci_fetch_assoc($stmt))!= false) {
				$data = array(
					'id' => iconv("GBK","UTF-8",$row["ID"]),
					'userId' => iconv("GBK","UTF-8",$row["USERID"]),
					'userName' => iconv("GBK","UTF-8",$row["USERNAME"]),
					'userCode' => iconv("GBK","UTF-8",$row["ALARM"]),
					'orgCode' => iconv("GBK","UTF-8",$row["ORGCODE"]),
					'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"])
				);
			} else
				$bRet = false;
		}
		OCIFreeStatement($stmt);
		
		if (!$bRet)
			return false;

		return $data;
	}
	
	/**
	 * getNormalGroup
	 * 根据部门编号查询派出所车辆列表
	 */
	public function getNormalGroup($orgCode,$jqid) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//判断用户名是否存在
		$sql = "select gp.gid,gp.status,gp.leaderid,org.orgcode,org.orgname,gp.carid,car.hphm,usr.username,op.orgname as parentName" .
				"  from zdt_duty_group gp, zdb_organization org, zdb_equip_car car,zdb_user usr,zdb_organization op where org.orgcode = gp.orgcode" .
				"   and car.id = gp.carid   and usr.userid=gp.leaderid and org.parentid = op.id  and gp.status!='3'  and org.orgcode = '$orgCode'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			$bRet = false;
		} else {
			$mens = array ();
			while(($row=oci_fetch_assoc($stmt))!= false) {
				$data = array(
					'gid' => iconv("GBK","UTF-8",$row["GID"]),
					'orgCode' => iconv("GBK","UTF-8",$row["ORGCODE"]),
					'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"]),
					'parentName' => iconv("GBK","UTF-8",$row["PARENTNAME"]),
					'carId' => iconv("GBK","UTF-8",$row["CARID"]),
					'hphm' => iconv("GBK","UTF-8",$row["HPHM"]),
					'userName' => iconv("GBK","UTF-8",$row["USERNAME"]),
					'status' => iconv("GBK","UTF-8",$row["STATUS"])
				);
				array_push($mens, $data);
			} 
		}
		OCIFreeStatement($stmt);
		
		if (!$bRet)
			return false;
		if(count($mens)<1)
		{
			// 新增写入日志表
            $sql_insert_pjsb_log ="insert into pjsb_log (jqid,bmbm) values('$jqid','$orgCode')";
            $stmt = oci_parse($this->dbconn, $sql_insert_pjsb_log);
            oci_execute($stmt);
		}
		return $mens;
	}
	
	public function getOrgTree($orgCode){
		$bRet = true;
		$errMsg = "";
		$org1 = array ();
		$org2 = array ();
		$org3 = array ();
		$result = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = " select t.orgcode,t.id,t.orgname,t.parentid,t.orglevel from zdb_organization t where t.id !='196880' and t.id!='198542' and (t.orglevel ='10' or t.orglevel ='21' or t.orglevel ='32') ";
		if ($orgCode != GlobalConfig :: getInstance()->dsdm."00000000") {
			$sql = $sql . " and (t.parenttreepath like '%$orgCode%' or t.orgCode = '$orgCode')";
		}
		else
		{
			$sql = $sql . " and (t.parenttreepath like '%".GlobalConfig :: getInstance()->dsdm."%' or t.orgCode = '$orgCode')";
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
				$orgLevel = iconv("GBK", "UTF-8", $row["ORGLEVEL"]);
				$id = iconv("GBK", "UTF-8", $row["ID"]);
				if($orgLevel=="10"){
					$org_1 = array (
						'id' => iconv("GBK", "UTF-8", $row["ID"]),
						'value' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
						'text' => iconv("GBK", "UTF-8", $row["ORGNAME"])
					);
					array_push($org1, $org_1);
				}
				if($orgLevel=="21"){
					$org_2 = array (
						'id' => iconv("GBK", "UTF-8", $row["ID"]),
						'parentId' => iconv("GBK", "UTF-8", $row["PARENTID"]),
						'value' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
						'text' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
						'state' => "closed"
					);
					array_push($org2, $org_2);
				}
				if($orgLevel=="32"){
					$org_3 = array (
						'parentId' => iconv("GBK", "UTF-8", $row["PARENTID"]),
						'value' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
						'text' => iconv("GBK", "UTF-8", $row["ORGNAME"])
					);
					array_push($org3, $org_3);
				}
			}
		}
		for ($i = 0, $count1 = sizeof($org2); $i < $count1; $i++) {
			$parentId = $org2[$i]['id'];
			$chird = array();
			for ($j = 0, $count2 = sizeof($org3); $j < $count2; $j++) {
				if($org3[$j]['parentId']==$parentId){
					array_push($chird, $org3[$j]);
				}
				
			}
			if( count($chird)>0){
				$org2[$i]['children'] = $chird;
			}
			
		}
		
		for ($i = 0, $count1 = sizeof($org1); $i < $count1; $i++) {
			$parentId = $org1[$i]['id'];
			$chird = array();
			for ($j = 0, $count2 = sizeof($org2); $j < $count2; $j++) {
				if($org2[$j]['parentId']==$parentId){
					array_push($chird, $org2[$j]);
				}
				
			}
			if( count($chird)>0){
				$org1[$i]['children'] = $chird;
			}
			
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if(count($org1)>0){
			$result = $org1;
		}else if(count($org2)>0){
			$result = $org2;
		}else if(count($org3)>0){
			$result = $org3;
		}
		return $result;
	}
	
	public function getOrganization(){
		$bRet = true;
		$errMsg = "";
		$org = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = " select t.orgcode,t.id,t.orgname,t.parentid,t.orglevel,o.orgcode as parentCode,o.orgname as parentName from zdb_organization t" .
				" left join zdb_organization o on t.parentid=o.id where t.orglevel ='10' or t.orglevel ='21' or t.orglevel ='32'";
		//		$sql = " select t.orgcode,t.id,t.orgname,t.parentid,t.orglevel,o.orgcode as parentCode,o.orgname as parentName from zdb_organization t left join zdb_organization o on t.parentid=o.id";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
					$datas = array (
						'id' => iconv("GBK", "UTF-8", $row["ID"]),
						'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
						'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
						'parentCode' => iconv("GBK", "UTF-8", $row["PARENTCODE"]),
						'parentName' => iconv("GBK", "UTF-8", $row["PARENTNAME"])
					);
				array_push($org, $datas);
			}
			
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $org;
	}
        public function getOrganizationAll(){
		$bRet = true;
		$errMsg = "";
		$org = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = " select t.orgcode,t.id,t.orgname,t.parentid,t.orglevel,o.orgcode as parentCode,o.orgname as parentName from zdb_organization t" .
				" left join zdb_organization o on t.parentid=o.id";
		//		$sql = " select t.orgcode,t.id,t.orgname,t.parentid,t.orglevel,o.orgcode as parentCode,o.orgname as parentName from zdb_organization t left join zdb_organization o on t.parentid=o.id";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
					$datas = array (
						'id' => iconv("GBK", "UTF-8", $row["ID"]),
						'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
						'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
						'parentCode' => iconv("GBK", "UTF-8", $row["PARENTCODE"]),
						'parentName' => iconv("GBK", "UTF-8", $row["PARENTNAME"])
					);
				array_push($org, $datas);
			}
			
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $org;
	}
	public function getOrgTreeForMR($orgCode){
		$bRet = true;
		$errMsg = "";
		$org1 = array ();
		$org2 = array ();
		$org3 = array ();
		$result = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = " select t.orgcode,t.id,t.orgname,t.parentid,t.orglevel from zdb_organization t where t.id !='196880' and t.id!='198542' and (t.orglevel ='10' or t.orglevel ='21' or t.orglevel ='32') ";
		if ($orgCode != GlobalConfig :: getInstance()->dsdm."00000000") {
			$sql = $sql . " and (t.parenttreepath like '%$orgCode%' or t.orgCode = '$orgCode')";
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
				$orgLevel = iconv("GBK", "UTF-8", $row["ORGLEVEL"]);
				$id = iconv("GBK", "UTF-8", $row["ID"]);
				if($orgLevel=="10"){
					$org_1 = array (
						'id' => iconv("GBK", "UTF-8", $row["ID"]),
						'value' => iconv("GBK", "UTF-8", $row["ID"]),
						'text' => iconv("GBK", "UTF-8", $row["ORGNAME"])
					);
					array_push($org1, $org_1);
				}
				if($orgLevel=="21"){
					$org_2 = array (
						'id' => iconv("GBK", "UTF-8", $row["ID"]),
						'parentId' => iconv("GBK", "UTF-8", $row["PARENTID"]),
						'value' => iconv("GBK", "UTF-8", $row["ID"]),
						'text' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
						'state' => "closed"
					);
					array_push($org2, $org_2);
				}
				if($orgLevel=="32"){
					$org_3 = array (
						'parentId' => iconv("GBK", "UTF-8", $row["PARENTID"]),
						'value' => iconv("GBK", "UTF-8", $row["ID"]),
						'text' => iconv("GBK", "UTF-8", $row["ORGNAME"])
					);
					array_push($org3, $org_3);
				}
			}
		}
		for ($i = 0, $count1 = sizeof($org2); $i < $count1; $i++) {
			$parentId = $org2[$i]['id'];
			$chird = array();
			for ($j = 0, $count2 = sizeof($org3); $j < $count2; $j++) {
				if($org3[$j]['parentId']==$parentId){
					array_push($chird, $org3[$j]);
				}
				
			}
			if( count($chird)>0){
				$org2[$i]['children'] = $chird;
			}
			
		}
		
		for ($i = 0, $count1 = sizeof($org1); $i < $count1; $i++) {
			$parentId = $org1[$i]['id'];
			$chird = array();
			for ($j = 0, $count2 = sizeof($org2); $j < $count2; $j++) {
				if($org2[$j]['parentId']==$parentId){
					array_push($chird, $org2[$j]);
				}
				
			}
			if( count($chird)>0){
				$org1[$i]['children'] = $chird;
			}
			
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if(count($org1)>0){
			$result = $org1;
		}else if(count($org2)>0){
			$result = $org2;
		}else if(count($org3)>0){
			$result = $org3;
		}
		return $result;
	}
	
	
}
?>
