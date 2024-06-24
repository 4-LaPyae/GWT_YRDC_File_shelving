<?php	
	// $dbhost = '10.10.210.54';
	// $dbuser = 'fileshelvinguser';
	// $dbpass = 'YRDCgL0b@lF!l3$h3lv!nG';
	// $dbname = 'yrdc_file_shelving';
	$dbhost = 'localhost';
	$dbuser = 'root';
	$dbpass = 'phyunu';
	$dbname = 'yrdc_file_shelving';
	
	$rootpath = "https://officeadmin.yrdc.gov.mm/file_shelving";
	
	/** global upload **/
	$g_upload_path = "/var/www/officeadmin.yrdc.gov.mm/public_data/file_shelving/";
	$g_img_extension = "*.jpg;*.jpeg;*.gif;*.png;*.pdf;";
	$g_max_filesize = 100*1024*1024;
	$g_file_overwrite = FALSE;
	$g_allow_filetype = array(
							'jpg'		=>'image/jpg', 
							'JPG'		=>'image/jpg', 
							'jpeg'	=>'image/jpeg', 
							'JPEG'	=>'image/jpeg', 
							'gif'		=>'image/gif', 
							'png'		=>'image/png',
							'pdf'		=>'application/pdf'
						);
	$g_upload_path_type = array('file'		=>'upload_document/file/');
	/** end of global upload **/
?>
