<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getIntelligence
 *
 * @author Administrator
 */
class GetIntelligence extends TpmsDB {
    //put your code here
    //
    public function getIntelligenceList($lastTime)
    {
      
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		$arr=array('total'=>0,'rows' => $datas);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			
		 /*组成sql*/
		$sql = "select a.guid,a.content,to_char(a.createtime,'yyyy-mm-dd hh24:mi:ss') createtime ,a.zjhm,a.zp,a.yy,b.username,c.orgname,nvl(dbms_lob.getlength(e.fj),0) yycd ,a.sp from GIS_JQSC a left join zdb_user b on a.zjhm=b.userid left join zdb_organization c on a.bmdm=c.orgcode left join fj_sound e on a.yy=e.md5  where rownum <400";
		
                if ($lastTime != null&&$lastTime != "") {
			$sql .=  " and a.createtime > to_date('$lastTime','yyyy-mm-dd hh24:mi:ss')";
                        //echo $sql;
		}
                
		$sql = $sql . " order by a.createtime desc";
		
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
				$bRet = false;
				$errMsg="查询失败";
			}else{
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array(
						'guid' => iconv("GBK", "UTF-8", $row["GUID"]),
                                                'username' => iconv("GBK", "UTF-8", $row["USERNAME"]),
                                                'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"]),                             
                                                'zp' =>  isset($row["ZP"])?"../../zhdd/php/ajfx/AjJpg.php?imageid=".str_replace(",",",../../zhdd/php/ajfx/AjJpg.php?imageid=",(iconv("GBK", "UTF-8", $row["ZP"]))):"",
                                                'yy' => $row["YYCD"]>2?"../../zhdd/php/ajfx/Ajmp3.php?imageid=".iconv("GBK", "UTF-8", $row["YY"]):"",
                                                'sp' =>  isset($row["SP"])?"../../php/ajfx/AjMp4.php?imageid=".str_replace(",",",../../php/ajfx/AjMp4.php?imageid=",(iconv("GBK", "UTF-8", $row["SP"]))):"",
                                                'content' => iconv("GBK", "UTF-8", $row["CONTENT"]),
						'createtime' => iconv("GBK", "UTF-8", $row["CREATETIME"])
       				);
		   			array_push($datas, $data);
	  		}
	  			oci_free_statement($stmt);
	  			oci_close($this->dbconn);
				$arr=array('total'=>count($datas),'rows' => $datas);
			}
		
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	  
    }
    public function getIntelligenceDetailed($guid){
        
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select a.zjhm,a.zp,a.yy,b.username,a.content,c.orgname ,a.sp from GIS_JQSC a left join zdb_user b on a.zjhm=b.userid left join zdb_organization c on a.bmdm=c.orgcode where a.guid='$guid'";
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
					'zjhm' => iconv("GBK", "UTF-8", $row["ZJHM"]),
					'zp' => isset($row["ZP"])?iconv("GBK", "UTF-8", $row["ZP"]):"",
					'yy' => isset($row["YY"])?iconv("GBK", "UTF-8", $row["YY"]):"",
                                        'sp' => isset($row["SP"])?iconv("GBK", "UTF-8", $row["SP"]):"",
					'username' => iconv("GBK", "UTF-8", $row["USERNAME"]),
                                        'content' => iconv("GBK", "UTF-8", $row["CONTENT"]),
					'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"])
					
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		else
			$arr = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $men
			);

		return $arr;
	
    }
}
?>
