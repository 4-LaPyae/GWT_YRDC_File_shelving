<?php	
	require_once('../library/reference.php');
	require_once("autoload.php");
	require_once('../adminauth.php');
	$tmpfoldername = "";
	$hidfoldername = "";
	$folder_token = "";
	$upload_foldertype = "";
	$check_fileproperties = false;
	
	//rnd : temp folder name , not allow sub folder
	if(isset($_POST['rnd']))
	{
		$hidfoldername = clean(trim($_POST['rnd']));
		if($hidfoldername!=""){
			$tmpfoldername = $hidfoldername;
			if(strpos($hidfoldername,"..") !== false || strpos($hidfoldername,"/") !== false) {
				die("UnAuthorized Access");
			}
			$hidfoldername = "/".$hidfoldername;
		}
	}
	
	//folder_token : salt token to verify folder name
	if(isset($_POST['folder_token']))
	{
		$folder_token = clean(trim($_POST['folder_token']));
	}
	
	//upload_foldertype : salt token to verify folder name
	if(isset($_POST['upload_foldertype']))
	{
		$upload_foldertype = clean(trim($_POST['upload_foldertype']));
		$ary_checkfileproperties = explode("@", $upload_foldertype);
		if(count($ary_checkfileproperties)>=2){
			if($ary_checkfileproperties[1] =="properties")
			$check_fileproperties = true;
		}
	}

	//verify folder name
	if($tmpfoldername!=""){
		$enc_data = "data=$tmpfoldername&type=$upload_foldertype";
		if (GWTFixedSaultHashPassword::verify($enc_data , $folder_token)) {
			//echo 'Correct Password!\n';
		} else {
			die("UnAuthorized Access , error code 0");
		}
	}
	
	$fdivname = "";
	//fdiv : filecontrol name
	if(isset($_POST['fdiv']))
	{
		$fdivname = clean(trim($_POST['fdiv']));
		if($fdivname==""){
			die("UnAuthorized Access");
		}
	}

	$fileobj = null;
	if(!empty($_FILES))
	{
		if(isset($_FILES[$fdivname]) && $_FILES[$fdivname] !='')
		{
			$fileobj = $_FILES[$fdivname];
		}
		$image_path_ary=array();
		$isarray=is_array($fileobj['name']);// ? 'Array' : 'not an Array';
		$total = count($fileobj['name']); 
		if($isarray && $total > 0)
		{ 
			//check if any file uploaded
			for($j=0; $j < count($fileobj['name']); $j++)
			{ //loop the uploaded file array
				
				if($check_fileproperties == true){
					$tmpfilepath = $fileobj["tmp_name"]["$j"];
					$fileproperties = @getFileTileAndDescription($tmpfilepath);
					
					if(count($fileproperties)<=0){
						$tmpentry = array();	
						$tmpentry['name'] = $fileobj['name'][$j];
						$tmpentry["error"]= "Title or Authors Properties not found (".$fileobj['name'][$j].")";
						echo json_encode($tmpentry); exit();
					}
				}
				
				$image_path = upload_multiple_files($j, $fileobj , "tmp".$hidfoldername);
				 if ( $image_path != false )
				{
					$image_name =  substr($image_path, strrpos($image_path, '/')+1);			
					//$image_name = rawurlencode($image_name);
					$tmpentry = array();	
					$tmpentry['name'] = $fileobj['name'][$j];
					$tmpentry['path'] = $image_name;//$img_path;
					$image_path_ary[]= $tmpentry;
				}
			}			
			echo json_encode($image_path_ary); exit();
		}
		else{
			
			if($check_fileproperties == true){
				$tmpfilepath = $fileobj["tmp_name"];
				$fileproperties = @getFileTileAndDescription($tmpfilepath);
				
				if(count($fileproperties)<=0){
					$tmpentry = array();	
					$tmpentry['name'] = $fileobj['name'];
					$tmpentry["error"]= "Title or Authors Properties not found (". $fileobj['name'].")";
					echo json_encode($tmpentry); exit();
				}
			}
			
			$image_path = upload_file_new($fileobj, "tmp".$hidfoldername);
			if ( $image_path != false )
			{
				$image_name =  substr($image_path, strrpos($image_path, '/')+1);			
				//$image_name = rawurlencode($image_name);
				$tmpentry = array();	
				$tmpentry['name'] = $fileobj['name'];
				$tmpentry['path'] = $image_name;//$img_path;
				$image_path_ary[]= $tmpentry;
				echo json_encode($image_path_ary);
				exit();
			}
		}
	}
	
	echo json_encode("no file to upload");
?>