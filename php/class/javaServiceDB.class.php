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
class javaServiceDB extends TpmsDB {
	
	public function getOrganization($orgCode){
		$bRet = true;
		$errMsg = "";
		$org1 = array ();
		$result = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = " select t.orgcode,t.id,t.orgname,t.parentid,t.orglevel,t.parenttreepath,t.orgqc,t.xzqh from zdb_organization t  where (t.orglevel='20' or t.orglevel='30' or t.orglevel='31' or t.orglevel='41' )  ";
		if ($orgCode != GlobalConfig :: getInstance()->dsdm."00000000") {
			$sql = $sql . " and orgcode in(select orgcode from zdb_organization o start with o.orgcode='$orgCode' connect by prior o.id=o.parentid) order by orgcode";
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
				$org_1 = array (
						'id' => iconv("GBK", "UTF-8", $row["ID"]),
						'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
						'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
						'parentId' => iconv("GBK", "UTF-8", $row["PARENTID"]),
						'parenttreepath' => iconv("GBK", "UTF-8", $row["PARENTTREEPATH"]),
						'orglevel' => iconv("GBK", "UTF-8", $row["ORGLEVEL"]),
						'orgqc' => iconv("GBK", "UTF-8", $row["ORGQC"]),
						'xzqh' => iconv("GBK", "UTF-8", $row["XZQH"])
					);
					array_push($result, $org_1);
			}
		}
		
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		
		return $result;
	}
	
	
}
?>
