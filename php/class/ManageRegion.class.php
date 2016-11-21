<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManageRegion
 *
 * @author lenovo
 */
class ManageRegion extends TpmsDB{
  
        public function selectManageRegion ()
        {
                $bRet = 1;
		$errMsg = "";
		$datas = array ();
		$sql = "select id,name,type,deptid,deptname,code,polygon wkt from tb_manage_region";
                if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = 0;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
                                  $geometry = isset($row['WKT'])?$row['WKT']->load():"";
				$data = array (
          'id' => iconv("GBK", "UTF-8", $row["ID"]),                            
					'name' => iconv("GBK", "UTF-8", $row["NAME"]),
					'type' => iconv("GBK", "UTF-8", $row["TYPE"]),
					'deptid' => iconv("GBK", "UTF-8", $row["DEPTID"]),
					'deptname' => iconv("GBK", "UTF-8", $row["DEPTNAME"]),
					'code' => iconv("GBK", "UTF-8", $row["CODE"]),
                                        'wkt' => $geometry
					
				);
				array_push($datas, $data);
			}
		}
	
		$res = array (
				'head' => array (
							'code' => $bRet,
							'message' => $errMsg
							),
				'value' => $datas,
				'extend' => ''
			);
		return $res;
        }
        
        public  function insertManageRegion($name,$code,$type,$deptid,$deptname,$wkt){
            $bRet = true;
            $row_count=0;
            $res;
            $sql = "select count(1) as ROWCOUNT from tb_manage_region where code='$code'";
            if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			//echo getOciError($stmt);
			oci_close($this->dbconn);
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$row_count = iconv("GBK", "UTF-8", $row["ROWCOUNT"]);
			}
		
		    if ($row_count > 0) {
			     $res = array (
					'head' => array (
								'code' => 0,
								'message' => '辖区编码已经存在'
								),
					'value' => '',
					'extend' => ''
				);
                        oci_close($this->dbconn);
			return $res;
		    }else{
		   		//$query = "insert into tb_manage_region (name,type,deptid,geo) values ('人民广场责任区','1','20123123123123',ST_GeomFromText('POLYGON((121.2 31.2,121.3 31.3,121.4 31.4,121.2 31.2))',4326));";
				
				if ($this->dbconn == null)
                                    $this->dbconn = $this->LogonDB();
                                    $id = $this->getSeq('seq_tb_manage_region');
                                    $nameGBK=iconv("UTF-8","GBK",$name);
                                    $deptnameGBK=iconv("UTF-8","GBK",$deptname);
                                    $up_sql = "insert into tb_manage_region (id,name,code,type,deptid,deptname,polygon) values ($id,'$nameGBK','$code','$type','$deptid','$deptnameGBK',empty_clob())  RETURNING polygon INTO :mylob";
                                    //echo $up_sql;
                                    $stmt = @oci_parse($this->dbconn, $up_sql);
                                    $mylob = @oci_new_descriptor($this->dbconn,OCI_D_LOB);
                                     @oci_bind_by_name($stmt,':mylob',$mylob, -1, OCI_B_CLOB);
                        // Execute the statement using OCI_DEFAULT (begin a transaction)
                                     @oci_execute($stmt, OCI_NO_AUTO_COMMIT) or die ("Unable to execute query\n");
                                     if ( !$mylob->save($wkt) ) {
                                    // Rollback the procedure
                                    echo $wkt;
                                    oci_rollback($this->dbconn);
                                            $bRet = false;
                                            $errMsg = '操作失败';
                                    }
                                                    //oci_commit($this->dbconn);
                                    $sql="update tb_manage_region set geo=sdo_geometry(polygon,4326) where id=$id";
                                    $stmt = oci_parse($this->dbconn, $sql);
                                    // Everything OK so commit
                                                    if (!@ oci_execute($stmt)) {
                                                    $bRet = false;
                                                    $errMsg = '操作失败';
                                                    }
                                    oci_commit($this->dbconn);
                                    oci_close($this->dbconn);	
          
				if ($bRet) {
					//echo 'bret==='.$bRet; 
					$res = array (
						'head' => array (
									'code' => 1,
									'message' => ''
									),
						'value' => array ('id'=>$id,'code'=>$code,'name'=>$name,'deptid'=>$deptid,'deptname'=>$deptname,'wkt'=>$wkt),
						'extend' => ''
					);
				} else { 
					$res = array (
						'head' => array (
									'code' => 0,
									'message' => '操作失败'
									),
						'value' => '',
						'extend' => ''
					);
				}
				
				return $res;
				
		   }
		}
        }
        public function updateManageRegion($name,$code,$type,$deptid,$deptname,$wkt,$id){
            $bRet=true;
            $sql = "select count(1) as ROWCOUNT from tb_manage_region where code='$code'and id<>".$id;
	      if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			//echo getOciError($stmt);
			oci_close($this->dbconn);
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$row_count = iconv("GBK", "UTF-8", $row["ROWCOUNT"]);
			}
		
		    if ($row_count > 0) {
			     $res = array (
					'head' => array (
								'code' => 0,
								'message' => '辖区编码已经存在'
								),
					'value' => '',
					'extend' => ''
				);
                        oci_close($this->dbconn);
			return $res;
		    }else{
		    $nameGBK=iconv("UTF-8","GBK",$name);
   			$deptnameGBK=iconv("UTF-8","GBK",$deptname);
				$up_sql = "update tb_manage_region set name='$nameGBK',code='$code',deptid='$deptid',deptname='$deptnameGBK',polygon=empty_clob() where id='$id'   RETURNING polygon INTO :mylob";
				 $stmt = oci_parse($this->dbconn, $up_sql);
                                    $mylob = oci_new_descriptor($this->dbconn,OCI_D_LOB);
                                     oci_bind_by_name($stmt,':mylob',$mylob, -1, OCI_B_CLOB);
                        // Execute the statement using OCI_DEFAULT (begin a transaction)
                                     oci_execute($stmt, OCI_NO_AUTO_COMMIT) or die ("Unable to execute query\n");
                                     if ( !$mylob->save($wkt) ) {
                                    // Rollback the procedure
                                    echo $wkt;
                                    oci_rollback($this->dbconn);
                                            $bRet = false;
                                            $errMsg = '操作失败';
                                    }
                                                    //oci_commit($this->dbconn);
                                    $sql="update tb_manage_region set geo=sdo_geometry(polygon,4326) where id=$id";
                                    $stmt = oci_parse($this->dbconn, $sql);
                                    // Everything OK so commit
                                                    if (!@ oci_execute($stmt)) {
                                                    $bRet = false;
                                                    $errMsg = '操作失败';
                                                    }
                                    oci_commit($this->dbconn);
                                    oci_close($this->dbconn);	
				if ($bRet) { 
					$arr = array('result' =>'true' , 'errmsg' =>'执行成功!!');
				} else { 
					$arr = array('result' =>'false' , 'errmsg' =>'执行失败!!');
				}
				$res = array (
					'head' => array (
								'code' => 1,
								'message' => ''
								),
					'value' => $arr,
					'extend' => ''
				);
			return $res;
		   }
		}
        }
        public function deleteManageRegion($id){
            
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$delname='';
		
		$sql = " delete from tb_manage_region where id=".$id;
			//echo  $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)){
			$bRet = false;
			$errMsg="执行失败";
		} 
		oci_free_statement($stmt);
		
		if ($bRet) { 
			$arr = array('result' =>'true' , 'errmsg' =>'执行成功!!');
		} else { 
			$arr = array('result' =>'false' , 'errmsg' =>'执行失败!!');
		}
		oci_close($this->dbconn);
		
		$res = array (
			'head' => array (
						'code' => 1,
						'message' => ''
						),
			'value' => $arr,
			'extend' => ''
		);
		
		return $res;
	
        }

        public function getSeq($tableName) {
		$bRet = true;
		$result = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		//select SENDMESSAGELOG_SEQ.Nextval as seq from dual
		$sql = "select $tableName".".Nextval as SEQ from dual";
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
}
?>

