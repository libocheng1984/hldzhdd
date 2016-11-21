<?php


/**
 * class event
 * version: 1.0
 * 查询所在公共类
 * author: jiangan
 * 2014/6/17
 * 
 * 此类定义交通信息中心全部方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class Unit extends TpmsDB {

	/**
	 * 根据部门编号查询所有警车
	 * @param orgCode
	 * @return
	 */
	public function GetCarByBmdm($orgCode) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select car.hphm,car.pp,org.orgcode,org.orgname,car.id as carId from zdb_equip_car car, zdb_organization org where car.dwdm=org.orgCode and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode')";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {
			$cars = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$car = array (
					'id' => iconv("GBK", "UTF-8", $row["CARID"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'pp' => iconv("GBK", "UTF-8", $row["PP"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'type' => '1'
				);
				array_push($cars, $car);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $cars;
	}

	/**
	* 根据部门编号查询所有警员
	* @param orgCode
	* @return
	*/
	public function GetMenByBmdm($orgCode) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select usr.id,usr.userid,usr.username,usr.alarm,org.orgcode,org.orgname from zdb_user usr, zdb_organization org where usr.bz=org.orgCode and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode')";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {
			$mens = array ();

			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'id' => iconv("GBK", "UTF-8", $row["USERID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'userCode' => iconv("GBK", "UTF-8", $row["ALARM"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'type' => '2'
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $mens;
	}

	/**
	 * createDutyGroups
	 * 创建巡逻组
	 */
	public function createDutyGroups($gid, $carId, $userId, $leaderId, $orgCode, $status, $m350Id) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from ZDT_DUTY_GROUP where status!='3' and carid='$carId'";
		
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		if ($count > 0) {
			$arr = array (
				'result' => 'false',
				'errmsg' => '车辆已绑定'
			);
			return $arr;
		}
		$sql = "select count(1) as ROWCOUNT from ZDT_DUTY_GROUP where status!='3' and m350Id='$m350Id'";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		if ($count > 0) {
			$arr = array (
				'result' => 'false',
				'errmsg' => '350兆已绑定'
			);
			return $arr;
		}
		$sql = "select count(1) as ROWCOUNT from ZDT_DUTY_GROUP where status!='3' ";
		$arr1 = explode(",", $userId);
		$contionSql = "";
		$countNum = 0;
		for ($i = 0; $i < count($arr1); $i++) {
			if ($arr1[$i] != "") {
				if ($countNum == 0) {
					$contionSql = $contionSql . " instr(userid,'$arr1[$i]')>0";
				} else {
					$contionSql = $contionSql . " or instr(userid,'$arr1[$i]')>0";
				}
				$countNum++;
			}
		}
		if ($contionSql != "") {
			$sql = $sql . " and (" . $contionSql . ")";
		}
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		if ($count > 0) {
			$arr = array (
				'result' => 'false',
				'errmsg' => '人员已绑定'
			);
			return $arr;
		}

		$gid = iconv("UTF-8", "GBK", $gid);
		$carId = iconv("UTF-8", "GBK", $carId);
		$userId = iconv("UTF-8", "GBK", $userId);
		$leaderId = iconv("UTF-8", "GBK", $leaderId);
		$status = iconv("UTF-8", "GBK", $status);
		$sql = "insert  into  ZDT_DUTY_GROUP(Gid,CarId,UserId,LeaderId,OrgCode,Status,CreateTime,m350Id)  values(ZDT_DUTY_GROUP_SEQ.Nextval,'$carId','$userId','$leaderId','$orgCode','$status',sysdate,'$m350Id')";
		
		$stmt = oci_parse($this->dbconn, $sql);
		$r = @ oci_execute($stmt);
		if (!$r) {
			$bRet = false;
			$errMsg = '';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$org = $this->getOrganization($orgCode);
		if ($org)
			$this->clearAllMemcache($org['orgCode'], $org['parentTreePath']);
		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);

		else
			$arr = array (
				'result' => 'true',
				'errmsg' => '创建成功'
			);

		return $arr;
	}

	/**
	* updateGroups
	* 修改巡逻组
	*/
	public function updateDutyGroups($id, $status) {
		if ($status == "3") {
			$res = $this->getCountEventBygid($id);
			if ($res) {
				$arr = array (
					'result' => 'false',
					'errmsg' => "该组还有未完成的警情，不能解散"
				);
				return $arr;
			}
		}

		$org = $this->getOrganizationByGid($id);
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		if ($status == "3") {
			$sql = "delete from ZDT_DUTY_GROUP   where gid='$id'";
		} else {
			$sql = "update  ZDT_DUTY_GROUP set status='$status'  where gid='$id'";
		}
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}

		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if ($org)
			$this->clearAllMemcache($org['orgCode'], $org['parentTreePath']);
		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		else
			$arr = array (
				'result' => 'true',
				'errmsg' => '操作成功'
			);

		return $arr;
	}

	public function getCountEventBygid($gid) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		//$sql = "select count(1) as ROWCOUNT from Zdt_Duty_Group g,zdt_peprocess_command c where g.leaderid=c.rybh and c.jqztdm!='5' and gid='$gid'";
		$sql = "select count(1) as ROWCOUNT from Zdt_Duty_Group g left join zdt_peprocess_command c on g.leaderid=c.rybh left join zdt_peprocess p on p.cjdbh=c.cjdbh" .
				" left join zdt_policeevent e on e.jqid=p.jqid where e.jqclzt !='5' and e.jqclzt != '1' and e.jqzt!='2' and p.cjdzt='1' and g.gid='$gid'";
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

	public function getOrganization($orgCode) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$org = array ();
		$sql = "select t.orgcode,t.orgname,t.parenttreepath from zdb_organization t where t.orgcode='$orgCode'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {

			if (($row = oci_fetch_assoc($stmt)) != false) {
				$org = array (
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'parentTreePath' => iconv("GBK", "UTF-8", $row["PARENTTREEPATH"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $org;
	}

	public function getOrganizationByGid($gid) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$org = array ();
		$sql = "select t.orgcode,t.orgname,t.parenttreepath from zdb_organization t,zdt_duty_group g where t.orgcode=g.orgcode and g.gid='$gid'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {

			if (($row = oci_fetch_assoc($stmt)) != false) {
				$org = array (
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'parentTreePath' => iconv("GBK", "UTF-8", $row["PARENTTREEPATH"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $org;
	}

}
?>
