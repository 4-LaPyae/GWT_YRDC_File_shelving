<?php
	function clean($str) 
	{
		$str = @trim ( $str );
		if (get_magic_quotes_gpc ()) 
		{
			$str = stripslashes ( $str );
		}
		return $str;
	}

	function unsetcookie($cookiename)
	{
		setcookie ($cookiename, "", time() - 3600); 
		unset($_COOKIE[$cookiename]); 
	}

	function getImageFileNameWithExtension($path, $file)
	{
		$extarr = array('.jpg', '.jpeg', '.gif', '.png', '.pdf');
		foreach( $extarr as $ext )
		{
			if( file_exists($path.$file.$ext) )				
				return $path.$file.$ext;
		}		
		return '';
	}

	function getImageFileNameWithExtension_arr($path, $file)
	{	
		$result = array();
		$extarr = array('.jpg', '.jpeg', '.gif', '.png');
		foreach($extarr as $ext)
		{		
			if(file_exists($path.$file.$ext))
			{
				$result[] = $path.$file.$ext;
			}
		}
		return $result;
	}

	/*
	check input string $value is valid date or not depends on input date format $informat
	if it is valie, reference paramer retdate will fill with date value in yyyy-MM-dd format
	all non-digit characters in $value string will be discard
	support format dd, mm, yy, yyyy with separator (\-., /) or without separator. e.g. dd/mm/yyyy, ddmmyyyy, mm/dd/yyyy, mmddyy, etc.
	format text are case insensitive 
	not support single day or month.  e.g. d/m/yyyy or m/d/yyyy, etc.

	return 0, if no error
	return 1, if different strlen, not match with input date format
	return 2, if invalid date value
	*/
	function checkmydate($value, $informat)
	{
		$value = trim($value);
		$informat = trim(strtoupper($informat));

		$s_date = str_replace(array('\'', '-', '.', ',', ' ','/'), '/', $value);
		$s_format = str_replace(array('\'', '-', '.', ',', ' ','/'), '/', $informat);
		if(strlen($s_format) != strlen($s_date))
			return 1;
		
		$a_date = explode('/', $s_date);
		$a_format = explode('/', $s_format);
		if(sizeof($a_format) != sizeof($a_date))
			return 1;
		
		$d = "";
		$m = "";
		$y = "";
		if(sizeof($a_format) > 1)	//with separator
		{
			for($i=0;$i<sizeof($a_format);$i++)
			{
				
				if(strlen($a_format[$i]) != strlen(preg_replace('/[^\d]/', '', $a_date[$i])))  //replace all non digit char with blank in $a_date[$i]
					return 1.1;
					
				switch ($a_format[$i])
				{
					case 'YYYY':
						$y .= $a_date[$i];
						break;
					case 'YY':
						$y .= $a_date[$i];
						break;
					case 'MM':
						$m .= $a_date[$i];
						break;
					case 'DD':
						$d .= $a_date[$i];
						break;
					default:
						return 1;
				}
			}
		}
		else //for without separator
		{
			$a_date = str_split($s_date);
			$a_format = str_split($s_format);

			for($i=0;$i<sizeof($a_format);$i++)
			{
				if(is_numeric($a_date[$i]) == false)
					return 1.2;
					
				switch ($a_format[$i])
				{
					case 'Y':
						$y .= $a_date[$i];
						break;
					case 'M':
						$m .= $a_date[$i];
						break;
					case 'D':
						$d .= $a_date[$i];
						break;
					default:
						return 1;
				}
			}
		}
		
		if($y == "00")  //check two zero digits year
		{
			$y = "2000";
		}

		if(checkdate($m, $d, $y) || ($m==2 && $d==29 && $y==1900))   //checkdate function incorrectly reply for 2/29/1900
		{
			$retdate = date_create($y.'-'.$m.'-'.$d);
			$retdate = date_format($retdate, 'Y-m-d');
			return $retdate;		
		}
		else 
			return 2;
	}
	
	function encrypt($text) 
	{		
		return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, MPTA, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))); 
	} 

	function decrypt($text) 
	{	
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, MPTA, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))); 
	}
	
	//Encryption function
	function api_mc_encrypt($encrypt)
	{
		global $api_encrypt_key;	//
		global $api_encrypt_vi;
		$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC , '');
		mcrypt_generic_init($td, $api_encrypt_key, $api_encrypt_vi);
		$encrypted = mcrypt_generic($td, $encrypt);
		$encode = base64_encode($encrypted);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return $encode;
	}

	//Decryption function
	function api_mc_decrypt($decrypt)
	{
		global $api_encrypt_key;	//
		global $api_encrypt_vi;
		$decoded = base64_decode($decrypt);
		$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC , '');
		mcrypt_generic_init($td, $api_encrypt_key, $api_encrypt_vi);
		try{
			$decrypted = mdecrypt_generic($td, $decoded);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			return trim($decrypted);
		} catch (Exception $e) {
			return null;
		}		
	}
	
	function isemail($email) 
	{
		return preg_match('|^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$|i', $email);
	}
	
	function get_user_menu($movepath,$usertypeid)
	{
		$parentid = 0;
		$usertypebol = new usertypebol();
		$result = $usertypebol->get_header_menu($parentid,$usertypeid);

		while($row = $result->getNext())
		{
			echo '<li';
			if($row['url'] != '#')
				echo ' class="nav-item"><a href="'.$movepath.$row['url'].'" class="nav-link">';
			else
				echo ' class="nav-item dropdown"><a class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">';

			echo $row['menu_name'];
			echo '</a>';
			
			$result2 = $usertypebol->get_header_menu($row['menu_id'],$usertypeid);
			
			if($result2->rowCount() > 0)
			{	
				echo '<ul class="dropdown-menu mt-0 rounded-0 bg-gray dk border-0 p-md-3">';
				while($row2 = $result2->getNext())
				{
					echo '<li><a href="'.$movepath.$row2['url'].'" class="dropdown-item">'.$row2['menu_name'].'</a></li>';
				}
				echo '</ul>';
			}
			echo '</li>';
		}
	}
	
	function to_ymd($date)
	{
		try
		{
			if ($date != "")
			{
				$tmp_arr = explode('-',$date);
				
				if (count($tmp_arr)==3)
				{
					$date = new DateTime($date);
					$date = $date -> format("Y-m-d");
				}
			}
			return $date;
		}
		catch(Exception $e)
		{
			return "";
		}
	}
	
	function to_dmy($date)
	{
		try
		{
			if ($date != "")
			{
				$date = new DateTime($date);
				$date = $date -> format("d-m-Y");
			}
			return $date;
		}
		catch(Exception $e)
		{
			return "";
		}
	}
	
	function validate_required($name)
	{
		if(trim($name) =='')
			return false;
		else
			return true;
	}
	
	function json_safedecode($jsonstring)
	{
		if(get_magic_quotes_gpc())
			$retstr = json_decode(stripslashes($jsonstring));	
		else
			$retstr = json_decode($jsonstring);
		return $retstr;
	}
	
	function PHPExcelMergeCenter($sheet,$cell)
	{
		$sheet->mergeCells($cell);
		$sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle($cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$sheet->getStyle($cell)->getAlignment()->setWrapText(true);
	}
	
	function Next_Excel_Col($curcolval) 
	{
		$rcol = "";
		$next = false;
		$curcol = $curcolval;
		$increses = false;
		while ( $curcol != "" ) {
			$col = substr ( $curcol, - 1, 1 );
			$curcol = substr ( $curcol, 0, strlen ( $curcol ) - 1 );
			
			if ($col == "Z") {
				$next = true;
				$rcol = "A" . $rcol;
			
			} else {
				$next = false;
				if ($increses == false) {
					$rcol = chr ( ord ( $col ) + 1 ) . $rcol;
					$increses = true;
				} else {
					$rcol = $col . $rcol;
				}
			}
			if (($curcol == "") && ($next == true)) {
				$rcol = "A" . $rcol;
			}
		}
		return trim ( $rcol );
	}
	
	function tempfile_unique($dir, $prefix, $postfix)
	{
		/* Creates a new non-existant file with the specified post and pre fixes */
	   
		if ($dir[strlen($dir) - 1] == '/') {
			$trailing_slash = "";
		} else {
			$trailing_slash = "/";
		}
		/*The PHP function is_dir returns true on files that have no extension.
		The filetype function will tell you correctly what the file is */
		if (!is_dir(realpath($dir)) || filetype(realpath($dir)) != "dir") {
			// The specified dir is not actualy a dir
			return false;
		}
		if (!is_writable($dir)){
			// The directory will not let us create a file there
			return false;
		}
	   
		do{    
			$seed = substr(md5(microtime()), 0, 8);
			$filename = $dir . $trailing_slash . $prefix . $seed . $postfix;
		} while (file_exists($filename));
		$fp = fopen($filename, "w");
		fclose($fp);
		return $filename;
	}
	
	function is_positiveNumeric($input_str, $islength = '') //  check for valid numeric strings	
	{
		$compstr  = "0123456789";		
		$input_str_length = strlen($input_str);
		if($islength != '')
		{
			if($input_str_length != $islength)
				return FALSE;
		}
		$blnResult = TRUE;		
		for($i=0; $i < $input_str_length && $blnResult == TRUE ; $i++)
		{
			if(strpos($compstr, $input_str[$i]) === FALSE)
				$blnResult = FALSE;
		}
		return $blnResult;		
	}
	
	function encrypt_img_path($text) 
	{		
		return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'JAZZ_DONUT', $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))); 
	}
	
	function decrypt_img_path($text) 
	{	
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, 'JAZZ_DONUT', base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))); 
	}
	
	function get_real_file_size($size=0) 
	{
		if (!$size) 
		{
			return 0;
		}
	   /* $scan['GB'] = 1073741824;
		$scan['Gb'] = 1073741824;
		$scan['G'] = 1073741824;
		$scan['g'] = 1073741824; */
		$scan['MB'] = 1048576;
		$scan['Mb'] = 1048576;
		$scan['M'] = 1048576;
		$scan['m'] = 1048576;
		$scan['KB'] = 1024;
		$scan['Kb'] = 1024;
		$scan['K'] = 1024;
		$scan['k'] = 1024;

		while (list($key) = each($scan)) 
		{
			if ((strlen($size)>strlen($key))&&(substr($size, strlen($size) - strlen($key))==$key)) 
			{
				$size = substr($size, 0, strlen($size) - strlen($key)) * $scan[$key];
				break;
			}
		}
		return $size;
	}
	
	//check for decimal number only//
	function is_decimal($input_str)
	{		
		$regex = '/^[0-9]*(\.)?[0-9]+$/';
		$result=preg_match ($regex,$input_str);
		return $result;	
	}
	
	function clean_jscode($script_str)
	{
		/**
		* SMT Added
		* remove <script> tab in the input string for prevention XSS
		**/
		$script_str = htmlspecialchars_decode($script_str);
		$search_arr = array('<script', '</script>');
		$script_str = str_ireplace($search_arr, $search_arr, $script_str);
		
		$split_arr = explode('<script', $script_str);
		$remove_jscode_arr  = array();
		
		foreach($split_arr as $key=>$val)
		{
			$newarr = explode('</script>', $split_arr[$key]);
			$remove_jscode_arr[] = ($key == 0)? $newarr[0]:$newarr[1];
		}
		
		return implode('', $remove_jscode_arr);
	}
	
	function delete_allcookies()
	{
		foreach($_COOKIE as $key=>$val)
		{
			setcookie($key, '', time()-1000);
			setcookie($key, '', time()-1000, '/');
			
			if( is_array($val) )
			{
				foreach($val as $k=>$v)
				{
					$index = $key.'['. $k .']';			
					setcookie($index, '', time()-1000);
					setcookie($index, '', time()-1000, '/');
				}
			}
		}
	}
	
	function get_localization_value($localize_name, $language_id)
	{
		$localizationbol = new localizationbol();
		$localize_value = $localizationbol->get_localize($localize_name, $language_id);
		return $localize_value;
	}
	
	// need to validate file size, file type
	function chmod_R($pathname, $filemode) 
	{ 
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathname));
		foreach($iterator as $item) {
			chmod($item, $filemode);
		}
	}
	
	function upload_file( $fileobj, $directory, $custom_file_name='' , $custom_allow_filetype=array() ) 
	{
		global $g_upload_path;		
		$err_msg = array();
		if ( count($custom_allow_filetype) == 0 )
			global $g_allow_filetype;
		else
			$g_allow_filetype = $custom_allow_filetype;
			
		global $g_max_filesize;
		global $g_file_overwrite;
	
		$upload_path = "";
		if($directory.trim("/") <> "")
			$upload_path = $g_upload_path . $directory . '/';
		else
			$upload_path = $g_upload_path;
			
		if(strpos($upload_path, "..")!==false)
		{
			die("Invalid upload path");
		}
		if(!file_exists($upload_path))
		{
			$oldumask = umask(0);  //to work recursive create directory with correct permission
			if (!mkdir($upload_path, 0777, true))
				$err_msg[] = "Invalid file path";
			
			chmod_R($upload_path, 0774);
			umask($oldumask);
		}
		
		if ( $custom_file_name == '' )
		{
			$seed = substr(md5(microtime()), 0,16);
			//$upload_file = $upload_path . 'tmp_' . $seed . basename($fileobj["name"]);		
			$tname = str_replace( " " , "" , basename($fileobj["name"]) ); //replace space name from filename
			$upload_file = $upload_path . 'tmp_' . $seed .'__rnd__'.$tname ;		
		}
		else
			$upload_file = $upload_path.$custom_file_name;
		
		if(strpos($upload_file, "..")!==false)
		{
			die("Invalid upload file");
		}
		$upload_file_type = $fileobj["type"];
		
		/////////////////required criteria/////////////
		if ($fileobj['error'] != UPLOAD_ERR_OK)
		{
			$err_msg[] = "File error code: ".$fileobj['error'];
		}
		
		/////////////////optional criteria/////////////
		if ($g_max_filesize > 0 && $fileobj["size"] > $g_max_filesize) {	
			$err_msg[] = "File is too large.";
		}
		
		if ($g_file_overwrite == false && file_exists($upload_file)) {
			$err_msg[] = "File already exists.";
		}

		if (count($g_allow_filetype) > 0)
		{
			if (in_array($upload_file_type, $g_allow_filetype)) {
				// file is okay continue
			} else {
				$err_msg[] =  $upload_file_type . " file type not allow.";
			} 
		}
		
		if(count($err_msg) == 0)
		{
			if (move_uploaded_file($fileobj["tmp_name"], $upload_file)) {				
				chmod($upload_file, 0774);
			} else {
				$err_msg[] = "There was an error uploading your file.";
			}
		}
		if ( count($err_msg) == 0  )
			return $upload_file;
		else
			return false;
	}
	
	function get_mime_type($file) {
		 $mtype = false;
		 if (function_exists('finfo_open')) {
			 $finfo = finfo_open(FILEINFO_MIME_TYPE);
			 $mtype = finfo_file($finfo, $file);
			 finfo_close($finfo);
		 } elseif (function_exists('mime_content_type')) {
			$mtype = mime_content_type($file);
		 } 
		 return $mtype;
	}
		
	function upload_file_new( $fileobj, $directory, $custom_file_name='' , $custom_allow_filetype=array() ) 
	{
		$pos = strpos($directory,"..");
		if ($pos !== false) {
			die("UnAuthorized Access");
		}
		global $g_upload_path;		
		$err_msg = array();
		if ( count($custom_allow_filetype) == 0 )
			global $g_allow_filetype;
		else
			$g_allow_filetype = $custom_allow_filetype;
			
		global $g_max_filesize;
		global $g_file_overwrite;
	
		$upload_path = "";
		if($directory.trim("/") <> "")
			$upload_path = $g_upload_path . $directory . '/';
		else
			$upload_path = $g_upload_path;
			
		if(strpos($upload_path, "..")!==false)
		{
			die("Invalid upload path");
		}
		if(!file_exists($upload_path))
		{
			$oldumask = umask(0);  //to work recursive create directory with correct permission
			if (!mkdir($upload_path, 0774, true))
				$err_msg[] = "Invalid file path";
			
			//chmod_R($upload_path, 0755);
			umask($oldumask);
		}
		
		if ( $custom_file_name == '' )
		{
			$seed = substr(md5(microtime()), 0,16);
			//$upload_file = $upload_path . 'tmp_' . $seed . basename($fileobj["name"]);		
			$tname = basename($fileobj["name"]) ;//str_replace( " " , "" , basename($fileobj["name"]) ); //replace space name from filename
			$upload_file = $upload_path . 'tmp_' . $seed .'__rnd__'.$tname ;		
		}
		else
			$upload_file = $upload_path.$custom_file_name;
		
		if(strpos($upload_file, "..")!==false)
		{
			upload_die_msg(basename($fileobj["name"]),"Invalid upload file.");
		}
		
		$upload_file_type = $fileobj["type"];
		$filename = $fileobj["tmp_name"];
		$mime_upload_file_type = get_mime_type($filename); //echo $mime_upload_file_type;
		if($upload_file_type != $mime_upload_file_type){
			upload_die_msg(basename($fileobj["name"]),"Invalid MIME file type.");
		}
		
		$fextension = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION)); 
		$mime_extension = getFileExtensionfromMediaType($mime_upload_file_type); 
		if(($fextension=="jpg" || $fextension=="jpeg") && ($mime_extension=="jpg" || $mime_extension=="jpeg")){
			//echo "skip for JPG and JPEG strtolower($fextension) != strtolower($mime_extension)";
		}else if($fextension != $mime_extension){ 
			upload_die_msg(basename($fileobj["name"]),"Invalid file extension.");
		}
		/////////////////required criteria/////////////
		if ($fileobj['error'] != UPLOAD_ERR_OK)
		{
			$err_msg[] = "File error code: ".$fileobj['error'];
		}
		
		/////////////////optional criteria/////////////
		if ($g_max_filesize > 0 && $fileobj["size"] > $g_max_filesize) {	
			$err_msg[] = "File is too large.";
		}
		
		if ($g_file_overwrite == false && file_exists($upload_file)) {
			$err_msg[] = "File already exists.";
		}

		if (count($g_allow_filetype) > 0)
		{
			if (in_array($upload_file_type, $g_allow_filetype)) {
				// file is okay continue
			} else {
				$err_msg[] =  $upload_file_type . " file type not allow.";
			} 
		}
		
		if(count($err_msg) == 0)
		{
			if (move_uploaded_file($fileobj["tmp_name"], $upload_file)) {				
				chmod($upload_file, 0664);
			} else {
				$err_msg[] = "There was an error uploading your file.";
			}
		}
		if ( count($err_msg) == 0  )
			return $upload_file;
		else{
			upload_die_msg(basename($fileobj["name"]),implode(", ",$err_msg));
			return false;
		}
	}
	
	function upload_multiple_files($fileindex , $fileobj, $directory, $custom_file_name='' , $custom_allow_filetype=array() ) 
	{
		$pos = strpos($directory,"..");
		if ($pos !== false) {
			die("UnAuthorized Access");
		}
		
		global $g_upload_path;		
		$err_msg = array();
		if ( count($custom_allow_filetype) == 0 )
			global $g_allow_filetype;
		else
			$g_allow_filetype = $custom_allow_filetype;
			
		global $g_max_filesize;
		global $g_file_overwrite;
	
		$upload_path = "";
		if($directory.trim("/") <> "")
			$upload_path = $g_upload_path . $directory . '/';
		else
			$upload_path = $g_upload_path;

		if(!file_exists($upload_path))
		{
			$oldumask = umask(0);  //to work recursive create directory with correct permission
			if (!mkdir($upload_path, 0774, true))
				$err_msg[] = "Invalid file path";
			
			//chmod_R($upload_path, 0755);
			umask($oldumask);
		}
		
		if ( $custom_file_name == '' )
		{
			$seed = substr(md5(microtime()), 0,16);
			$tname = basename($fileobj["name"]["$fileindex"]);//str_replace( " " , " " , basename($fileobj["name"]["$fileindex"]) ); //replace space name from filename
			$upload_file = $upload_path . 'tmp_' . $seed .'__rnd__'.$tname ;		
		}
		else
			$upload_file = $upload_path.$custom_file_name;
		
		$upload_file_type = $fileobj["type"]["$fileindex"];
		$filename = $fileobj["tmp_name"]["$fileindex"];
		$mime_upload_file_type = get_mime_type($filename); //echo $mime_upload_file_type;
		if($upload_file_type != $mime_upload_file_type){
			upload_die_msg(basename($fileobj["name"]["$fileindex"]),"Invalid MIME file type.");
		}
		
		$fextension = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION)); 
		$mime_extension = getFileExtensionfromMediaType($mime_upload_file_type); 
		if(($fextension=="jpg" || $fextension=="jpeg") && ($mime_extension=="jpg" || $mime_extension=="jpeg")){
			//echo "skip for JPG and JPEG strtolower($fextension) != strtolower($mime_extension)";
		}else if($fextension != $mime_extension){ 
			upload_die_msg(basename($fileobj["name"]["$fileindex"]),"Invalid file extension.");
		}
		/////////////////required criteria/////////////
		if ($fileobj['error']["$fileindex"] != UPLOAD_ERR_OK)
		{
			$err_msg[] = "File error code: ".$fileobj['error']["$fileindex"];
		}
		
		/////////////////optional criteria/////////////
		if ($g_max_filesize > 0 && $fileobj["size"]["$fileindex"] > $g_max_filesize) {	
			$err_msg[] = "File is too large.";
		}
		
		if ($g_file_overwrite == false && file_exists($upload_file)) {
			$err_msg[] = "File already exists.";
		}

		if (count($g_allow_filetype) > 0)
		{
			if (in_array($upload_file_type, $g_allow_filetype)) {
				// file is okay continue
			} else {
				$err_msg[] =  $upload_file_type . " file type not allow.";
			} 
		}
		
		if(count($err_msg) == 0)
		{
			if (move_uploaded_file($fileobj["tmp_name"]["$fileindex"], $upload_file)) {				
				chmod($upload_file, 0664);
			} else {
				$err_msg[] = "There was an error uploading your file.";
			}
		}
		//print_r($err_msg);
		if ( count($err_msg) == 0  )
			return $upload_file;
		else{
			upload_die_msg(basename($fileobj["name"]["$fileindex"]),implode(", ",$err_msg));
			return false;
		}
	}
	
	/*
     * To find file name
     *
     * @param   string  $filename	file name
     * @param   string  $needle		file ext to find file
     * @return  string  $show_img1			show_data_content.php list as file list string
     */
	function getfilename_fromtmp($filename, $needle)
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$filenamewithoutext = pathinfo($filename , PATHINFO_FILENAME);
		$rev_pos = strpos (($filenamewithoutext), ($needle)); 
		if ($rev_pos===false) return false;
		$str = substr($filenamewithoutext ,$rev_pos); 
		$str = str_replace( $needle , "" , $str);
		return $str.".".strtolower($ext);
	}
	
	/*
     * To get file list as array
	 * It will also check allowed_filetype
     *
     * @param   string  $fullfilepath			path to find files
     * @param   string  $custom_allow_filetype	custom_allow_filetype is not set, it will use from config
     * @return  array  $show_img1				file list string array
     */
	function getfilenamelist($fullfilepath, $custom_allow_filetype=array() ){
		if(substr($fullfilepath, -1) !="/") $fullfilepath .= "/";
		if ( count($custom_allow_filetype) == 0 )
			global $g_allow_filetype;
		else
			$g_allow_filetype = $custom_allow_filetype;
		
		$return_ary = array();
		// Open a known directory, and proceed to read its contents
		if (is_dir($fullfilepath)) {
			if ($dh = opendir($fullfilepath)) {
				while (($filename = readdir($dh)) !== false) {
					//echo  $filename;
					if($filename == "." || $filename == "..") continue;
					$ext = pathinfo($fullfilepath . $filename, PATHINFO_EXTENSION);
					$filenamewithoutext = pathinfo($fullfilepath . $filename, PATHINFO_FILENAME);
					$filedate = date ("Y-M-d H:i:s", filemtime($fullfilepath . $filename));
					if (array_key_exists( $ext , $g_allow_filetype)) {// }(trim($filenamefilterexpression)!=""){
						 //if (preg_match($filenamefilterexpression, $filenamewithoutext)){
							 $filepath = $fullfilepath ."".$filenamewithoutext;  //  echo "\n bef $fullfilepath $allowed_extension";
							if(trim($filepath)!=""){
								$return_ary[]=array("FileName"=>$filename
									, "FileExtension"=> $ext 
									, "FileModifiedDate"=> $filedate );
							}
					}
				}
				closedir($dh);
			}
		}
		return $return_ary;
	}
	
	//**** unicode char code array functions ***//
	function convert_to_englishdigit($str)
	{
		$retstr = ''; //$str.'---';
		//$results = array();
		//preg_match_all('/./u', $str, $results);		
		foreach(split_unicode_str($str) as $charcode)
		{
			if( $charcode == 46 || ($charcode>=48 && $charcode<=57) || ($charcode>=4160 && $charcode<=4169))
			{
				if($charcode>=4160 && $charcode<=4169)   //digit ascii code range for english and myanmar (mm3 and zawgyi use same code range)
					$charcode = $charcode - 4112;  //convert unicode digit to ascii digit 
				
				if( substr_count($retstr, '.') > 0 && $charcode == 46)// Check . is onc time exist
					return false;
			}
			else
				return false;  //not digit string
			$retstr .= chr($charcode);			
		}
		return $retstr;
	}
	
	function split_unicode_str( $string )
	{
		$string	= (string) $string;
		$length	= strlen($string);
		$sequence = array();

		for ( $i=0; $i<$length; ) 
		{
			$bytes = _characterBytes($string, $i);
			$ord = unicode_ord($string, $bytes, $i);

			if ( $ord !== false )
				$sequence[]	= $ord;

			if ( $bytes === false )
				$i++;
			else
				$i += $bytes;
		}

		return $sequence;

	}

	function unicode_ord( &$string, $bytes = null, $pos=0 )
	{
		if ( is_null($bytes) )
			$bytes = _characterBytes($string);
		
		if ( strlen($string) >= $bytes ) 
		{
			switch ( $bytes )
			{
				case 1:
					return ord($string[$pos]);
					break;

				case 2:
					return  ( (ord($string[$pos]) 	& 0x1f)	<< 6 ) +
							( (ord($string[$pos+1]) & 0x3f) );
					break;

				case 3:
					return 	( (ord($string[$pos]) 	& 0xf)	<< 12 ) + 
							( (ord($string[$pos+1]) & 0x3f) << 6 ) +
							( (ord($string[$pos+2]) & 0x3f) );
					break;

				case 4:
					return 	( (ord($string[$pos]) 	& 0x7) 	<< 18 ) + 
							( (ord($string[$pos+1]) & 0x3f)	<< 12 ) + 
							( (ord($string[$pos+1]) & 0x3f)	<< 6 ) +
							( (ord($string[$pos+2]) & 0x3f) );
					break;

				case 0:
				default:
					return false;
			}
		}

		return false;
	}
	
	function _characterBytes( &$string, $position = 0 ) 
	{
		$char = $string[$position];
		$charVal = ord($char);
		
		if ( ($charVal & 0x80) === 0 )
			return 1;

		elseif ( ($charVal & 0xe0) === 0xc0 )
			return 2;

		elseif ( ($charVal & 0xf0) === 0xe0 )
			return 3;
		
		elseif ( ($charVal & 0xf8) === 0xf0)
			return 4;
		/*
		elseif ( ($charVal & 0xfe) === 0xf8 )
			return 5;
		*/

		return false;
	}
	//**** unicode char code array functions ***//
	
	function convert_to_myanmardigit($str)
	{
		$retstr = '';
		foreach(split_unicode_str($str) as $charcode)
		{
			if( $charcode == 46 || ($charcode>=48 && $charcode<=57) || ($charcode>=4160 && $charcode<=4169))
			{
				if($charcode>=48 && $charcode<=57)   //digit ascii code range for english
					$charcode = $charcode + 4112; //convert ascii digit to unicode myanmar digit e.g. &#4160;
				
				if( substr_count($retstr, '&#46;') > 0 && $charcode == 46)// Check . is onc time exist
					return false;
			}
			else
				return false;  //not digit string
			$retstr .= '&#' . $charcode  . ';';
		}
		return mb_convert_encoding($retstr, 'UTF-8', 'HTML-ENTITIES');
	}
	
	function set_zero_on_blank($val, $mode = 0)
	{
		$mode_arr = array(0, '0000-00-00');
		if( $mode == -1 )
		{
			if( array_search($val, $mode_arr) === false )
				return $val;	
			else
					return '';
		}
		else
		{
			if( $val == '' )
				return $mode_arr[$mode];
			else
				return $val;
		}
	}
	
	function multiexplode ($delimiters,$string)
	{  
		$launch = explode($delimiters[0], $string);
		return  $launch;
	}
	
	/* convert myanmar number only with comma to english digit */
	function set_convert_mm_digit_format($input)
	{
		$unicode = array('၀', '၁', '၂', '၃', '၄', '၅', '၆', '၇', '၈', '၉');
		$english = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

		$string = str_replace($unicode, $english , $input);

		return join(',', $string);
	}
	
	function cleanGETforJQryDataTable()
	{
		//All possible integer fileds array
		$int_field_array = array('sEcho', 'iColumns', 'iDisplayStart', 'iDisplayLength', 'iSortingCols', 'iSortCol_', 'iTotalRecords', 'iTotalDisplayRecords');
		
		//All possible asc or desc fileds array
		$asc_field_array = array('sSortDir_', 'SortDir_');
		
		foreach($_GET as $key => $value)
		{
			if(in_array($key, $int_field_array)) 
			{ 
				if( is_numeric($value) )
					$_GET[$key] = abs($value);
				else
				{
					if( $key == 'iDisplayStart' )
						$_GET[$key] = 1;
					if( $key == 'iDisplayLength' )
						$_GET[$key] = 10;
					if( $key == 'iSortingCols' && abs($value) <= 0 )
						$_GET[$key] = 1;
				}
			}
			  
			if(in_array(substr($key, 0, -1), $int_field_array))
			{
				$_GET[$key] = abs($value);
			}
			 
			if(in_array(substr($key, 0, -1), $asc_field_array))
			{
				$_GET[$key] = ($value == "asc") ? $value : "desc";
			}
		}
	}
	
	function validate_password_rule($candidate)
	{
		//mini length 6, at least one char, one digit, one special char
		$r1= 8;// mini length 6
		$r2='/[a-zA-Z]/';  //One Char
		$r3='/[0-9]/';  //One Digit
		$r4='/[!@$%^*]/';  //One special=> char whatever you mean by 'special char'

		if(strlen($candidate)<$r1) return false;
		if(preg_match_all($r2,$candidate, $o)<1) return false;
		if(preg_match_all($r3,$candidate, $o)<1) return false;
		if(preg_match_all($r4,$candidate, $o)<1) return false;
		return true;
	}

	function encryptNET3DES($key,$iv,$text)
	{
		$td = mcrypt_module_open (MCRYPT_3DES, '', MCRYPT_MODE_CBC,'');
		
		// Complete the key
		$key_add = 24-strlen($key);
		$key .= substr($key,0,$key_add);

		// Padding the text 
		$block = mcrypt_get_block_size("tripledes", "cbc");
		$len = strlen($text);           
		$padding = $block - ($len % $block);
		$text .= str_repeat(chr($padding),$padding);

		mcrypt_generic_init ($td, $key, $iv);
		$encrypt_text = mcrypt_generic ($td, $text);
		$encrypt_text = base64_encode($encrypt_text);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return $encrypt_text;    
	}

	function decryptNET3DES($key,$iv,$text)
	{
		$td = mcrypt_module_open (MCRYPT_3DES, '' , MCRYPT_MODE_CBC, '');

		// Complete the key
		$text = base64_decode($text);
		$key_add = 24-strlen($key);
		$key .= substr($key,0,$key_add);

		mcrypt_generic_init ($td, $key, $iv);

		$decrypt_text = mdecrypt_generic ($td, $text);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		//remove the padding text
		$block = mcrypt_get_block_size("tripledes", "cbc");
		$packing = ord($decrypt_text{strlen($decrypt_text)-1}); 
		if($packing and ($packing < $block))
		{      
			for(  ($P=strlen($decrypt_text)-1);($P>=(strlen($decrypt_text)-$packing));$P--)
			{
			   if(ord($decrypt_text{$P}) != $packing)
			   {
				   $packing = 0;
			   }
			}
		}
		$decrypt_text = substr($decrypt_text,0,strlen($decrypt_text)-$packing);
		return $decrypt_text;
	}
	
	function upload_die_msg($filename,$errmsg){
		$tmpentry = array();	
		$tmpentry['name'] = $filename;
		$tmpentry["error"]= $errmsg;
		echo json_encode($tmpentry); exit();
	}
	
	function getFileTileAndDescription($filepath){
		$rtn = array();
		if(file_exists($filepath)){
			$exif = @exif_read_data($filepath, 'IFD0');
			if($exif){  
				$exif_ary = @exif_read_data($filepath, 0, true);
				if($exif_ary){
					$artist = (isset($exif_ary['IFD0']['Artist']))? $exif_ary['IFD0']['Artist'] : ""; //Authors
					$description = (isset($exif_ary['IFD0']['ImageDescription']))? $exif_ary['IFD0']['ImageDescription'] : ""; //Title
					//echo "artist=$artist , description=$description";
					if(trim($artist)=="" || trim($description)==""){
						return array();
					}
					$rtn['artist'] = $artist;
					$rtn['description'] = $description;
				}
			}
		}
		return $rtn;
	}
?>