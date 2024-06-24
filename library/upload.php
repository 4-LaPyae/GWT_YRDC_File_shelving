<?php
	require_once('config.php');
	require_once('globalfunction.php');
/**
 * upload.php
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

#!! IMPORTANT: 
#!! this file is just an example, it doesn't incorporate any security checks and 
#!! is not recommended to be used in production environment as it is. Be sure to 
#!! revise it and customize to your needs.


// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 5 minutes execution time
@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

if( isset($_GET['folder_name']) )
	$folder_name = $_GET['folder_name'];

$target_dir = "upload_photo/temp/".$folder_name;
if(strpos($target_dir, "..")!==false)
{
	die("Invalid upload path");
}
if (!empty($_FILES)) {
	$fileName = $_FILES["file"]["name"];
	upload_file( $_FILES['file'], $target_dir, $fileName );
}