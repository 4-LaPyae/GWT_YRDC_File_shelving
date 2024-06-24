<?php
	$movepath = '';
	require_once($movepath .'library/reference.php');
	require_once('autoload.php');
	
	$type="";
	$name="";
	$code="";
	$token="";
	$sno="";
	$dwn="";
	if( isset($_GET["dwn"])){
		$dwn="1";
	}
	if( isset($_GET["type"]) && isset($_GET["name"]) && isset($_GET["code"]) && isset($_GET["token"]) && isset($_GET["sno"]) )
	{
		if(strpos($_GET["type"], "..")!==false)
		{
			die("Invalid download link , error code 1");
		}
		if(strpos($_GET["name"], "..")!==false)
		{
			die("Invalid download link , error code 2");
		}
		if(strpos($_GET["code"], "..")!==false)
		{
			die("Invalid download link , error code 3");
		}
		if(strpos($_GET["sno"], "..")!==false)
		{
			die("Invalid download link , error code sno");
		}
		
		$type=clean($_GET["type"]);
		$name=clean($_GET["name"]);
		$code=clean($_GET["code"]); 
		$token=clean($_GET["token"]); 
		$sno=clean($_GET["sno"]); 
		$enc_data = "name=$name&type=$type&code=$code&sno=$sno";
		if (GWTFixedSaultHashPassword::verify($enc_data , $token)) {
			//echo 'Correct Password!\n';
		} else {
			die("Invalid download link , error code 0");
		}
		if($code=="x"){
			$code="";
		} 
		else{
			$code = "".$code."/";
		}
		
		global $g_upload_path_type; 
		$base_g_upload_path = "";
		if (array_key_exists($type, $g_upload_path_type)) {
			// file is okay continue
			$base_g_upload_path = $g_upload_path_type[$type];
		} else {
			 die("Invalid type $type , error code 0");
		} 
				
		global $g_upload_path;		
		global $g_allow_filetype;
		$filename = $g_upload_path.$base_g_upload_path.$code. $name ; //echo $filename;
		if(file_exists($filename))
		{
			 $mime_upload_file_type = get_mime_type($filename); //echo $mime_upload_file_type;
			if (count($g_allow_filetype) > 0)
			{
				if (in_array($mime_upload_file_type, $g_allow_filetype)) {
					// file is okay continue
				} else {
					die("Invalid download link , error code 4");
				} 
			}
			
			$fextension = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); 
			$mime_extension = getFileExtensionfromMediaType( $mime_upload_file_type);
			if(($fextension=="jpg" || $fextension=="jpeg") && ($mime_extension=="jpg" || $mime_extension=="jpeg")){ 
				//skip for check jpeg download
			}else if($fextension != $mime_extension){
				die("Invalid download link , error code 5");
			}
				
			// headers require to download a file
			if (headers_sent()) throw new Exception('Headers sent.');
			while (ob_get_level() && ob_end_clean());
			if (ob_get_level()) throw new Exception('Buffering is still active.');

			if($dwn=="1"){
				header("Content-length: ". filesize($filename));
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($filename));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			}else{
				 header('Content-Type: '.$mime_upload_file_type);
			}
			//header("Content-type: image/jpg");
			readfile($filename);
		}
	}
?>