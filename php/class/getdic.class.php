<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getdicClass
 *
 * @author Administrator
 */
class getdic extends TpmsDB {
    //put your code here
    	public function getdicByPcode($p_code) {
		$bRet = true;
		$errMsg = "";
		

		//判断用户名是否存在
		$sql = "select code,des from code_jqfl where ";
                if($p_code!="")
                {
                   $sql.="p_code='$p_code'" ;
                }
                else {
                   $sql.="p_code is null" ;
                }
                //echo $sql;
                if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			$bRet = false;
		} else {
			$mens = array ();
			$data_qxz=array(
					'value' => iconv("GBK","UTF-8",""),
					'text' => '请选择'
				);
			array_push($mens, $data_qxz);
			while(($row=oci_fetch_assoc($stmt))!= false) {
				$data = array(
					'value' => iconv("GBK","UTF-8",$row["CODE"]),
					'text' => iconv("GBK","UTF-8",$row["DES"])
					
				);
				array_push($mens, $data);
			} 
		}
		OCIFreeStatement($stmt);
		
		if (!$bRet)
			return false;

		return $mens;
	}
     
}
?>
