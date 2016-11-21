<?php

class getOracleBlob extends TpmsDB {

    public function getBlob($sql) {
        if ($this->dbconn == null)
            $this->dbconn = $this->LogonDB();
        $stmt = oci_parse($this->dbconn, $sql);
        if (!@oci_execute($stmt)) {
            oci_close($this->dbconn);
            exit;
        } else {

            if ( $row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_LOBS) ) {
               
                print $row['FJ'];
            }
            
        }
        OCIFreeStatement($stmt);
        OCILogoff($this->dbconn);
    }

}
?>
