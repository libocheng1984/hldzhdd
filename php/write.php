<?php
//删除行同修改行一样，只不过是将原来行替换为同等长度的空格串
	/*$f = fopen('d:/data.txt', 'r+');
   for($i=0;$i<3&&!feof($f);$i++){
        $row = fgets($f);
        $len = strlen($row);
        echo ftell($f)-$len."";
        fseek($f, ftell($f)-$len);
        fwrite($f, str_pad('', $len));
        //fwrite($f,"");
    }
    fclose($f);*/
     $fn = "d:/data.txt";
     $f= fopen($fn, "r");
    for($i=0;$i<3&&!feof($f);$i++){
		fgets($f);
    }
    ob_start();
	fpassthru($f);
    fclose($f);
    file_put_contents($fn, ob_get_clean() );
 
//echo $line;
?>