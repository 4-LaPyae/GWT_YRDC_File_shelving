<?php
	$classdirectory = array('bol', 'commoninfo', 'dal');
	
	$department_type_arr = array('0' => 'ကော်မတီတွင်း', '1' => 'ပြင်ပ');
	$division_arr = array('၁','၂','၃','၄','၅','၆','၇','၈','၉','၁၀','၁၁','၁၂','၁၃','၁၄');
	
	date_default_timezone_set('Asia/Rangoon');	
	
	define('YRDCFSH', 'yrdc_file_shelf');
	define("YRDCFSH_ENCRYPTION_KEY", "f!L@sHe1V!Ng@YRDCDc");
	define("YRDCFSH_ENCRYPTION_IV", "F!1e@YRDC"); //Please use 8 chars for IVs
	$numberformat = '####.####';
	$encrypt_key = 'globalfileshelving2018';
?>