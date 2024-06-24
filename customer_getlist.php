<?php
	$movepath = '';
	require_once($movepath .'library/reference.php');
	require_once('autoload.php');
	require_once('adminauth.php');
	cleanGETforJQryDataTable();
	
	$customer_bol=new customer_bol();	
	$sEcho = 0;
	if(isset($_GET['sEcho']))
		$sEcho = intval($_GET['sEcho']);
		
	// Paging
	$DisplayStart = 0;
	if ( isset($_GET['iDisplayStart']) )
		$DisplayStart = $_GET['iDisplayStart'];
	
	$DisplayLength = 10;
	if ( isset($_GET['iDisplayLength']) )
		$DisplayLength = $_GET['iDisplayLength'];
	
	// Searching
	$cri_str = ' WHERE 1=1  ';
	$param = array();
	if( isset($_GET['sSearch']) )
	{
		$criobj = json_safedecode($_GET['sSearch']);
		if( isset($criobj->cri_customer_name) && $criobj->cri_customer_name != '' )	
		{
			$cri_str .= " AND customer_name LIKE :cri_customer_name";	
			$param['cri_customer_name'] = '%'. clean($criobj->cri_customer_name) .'%';
		}
		
		if( isset($criobj->cri_father_name) && $criobj->cri_father_name != '' )	
		{
			$cri_str .= " AND father_name LIKE :cri_father_name";	
			$param['cri_father_name'] = '%'. clean($criobj->cri_father_name) .'%';
		}
		
		if( isset($criobj->cri_txt_birthdate) && $criobj->cri_txt_birthdate != '' )	
		{
			$cri_str .= " AND date_of_birth = :cri_txt_birthdate";	
			$param['cri_txt_birthdate'] =   to_ymd($criobj->cri_txt_birthdate);
		}
		
		if( isset($criobj->selnrcdivision_cri_nrcno) && $criobj->selnrctownship_cri_nrcno && $criobj->selnrctype_cri_nrcno && $criobj->txtnrcno_cri_nrcno && $criobj->txtnrcno_cri_nrcno != '' )
		{
			$cri_str .= " AND nrc_division_code = :nrc_division_code AND nrc_township_code = :nrc_township_code AND nrc_citizen_type = :nrc_citizen_type AND nrc_number = :nrc_number ";
			
			$param['nrc_division_code'] = $criobj->selnrcdivision_cri_nrcno;
			$param['nrc_township_code'] = $criobj->selnrctownship_cri_nrcno;
			$param['nrc_citizen_type'] = $criobj->selnrctype_cri_nrcno;
			$param['nrc_number'] = $criobj->txtnrcno_cri_nrcno;
		}
			
		if( isset($criobj->txt_cri_nationalcardno) && $criobj->txt_cri_nationalcardno != "" )
		{
			$cri_str .= " AND nrc_text = :nrc_text";
			$param['nrc_text'] = $criobj->txt_cri_nationalcardno;
		}
		if( isset($criobj->txt_cri_passportno) && $criobj->txt_cri_passportno != "" )
		{
			$cri_str .= " AND passport = :passport";
			$param['passport'] = $criobj->txt_cri_passportno;
		}
	}
	$cri_arr = array($cri_str, $param);
	// print_r($cri_arr); exit();
	
	// Ordering 
	$SortingCols = '';
	if ( isset( $_GET['iSortCol_0'] ) )
	{		
		$SortingCols = " ORDER BY ";
		for ( $i=0 ; $i < $_GET['iSortingCols']; $i++ )
		{
			$SortingCols .= fnColumnToField($_GET['iSortCol_'.$i])." ".$_GET['sSortDir_'.$i].", ";
		}
		$SortingCols = substr_replace($SortingCols, "", -2 );	
	}
	
	// Check with Encrypt Value
	$is_change = 0;
	if( $result = $customer_bol->select_customer_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) )
	{
		$currenturl = clean($_SERVER['SCRIPT_FILENAME']);
		$securitybol = new securitybol();
		
		$customer_ids = '';
		while( $row = $result->getNext() )
		{
			$customer_ids .= $row['customer_id'] . ',';
		}
		
		$customer_cri_arr = '';
		if( $customer_ids != '' )
		{
			$customer_ids = substr_replace( $customer_ids, "", -1 );
			
			$in_query_string_arr = get_in_query_string($customer_ids);
			$cri = ' AND customer_id IN ('.$in_query_string_arr[0].')';
			$customer_cri_arr = array($cri, $in_query_string_arr[1]);
			//print_r($customer_cri_arr);exit;
			$change_customer = $securitybol->check_and_change_invalid_record('customer', $customer_cri_arr, $currenturl);
			if( $change_customer )
				$is_change = 1;
		}
	}
	
	$rResult = $customer_bol->select_customer_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);	
	$iTotal = intval($rResult->getFoundRows());	
	
	$c=$DisplayStart;
	$response = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotal,'iTotalDisplayRecords'=>$iTotal,'aaData'=>array());	
	
	while($aRow = $rResult->getNext())
	{
		$customer_id = htmlspecialchars($aRow['customer_id']);
		$customer_name = htmlspecialchars($aRow['customer_name']);
		$nrc_no = htmlspecialchars($aRow['nrc']);
		$father_name = htmlspecialchars($aRow['father_name']);
		$date_of_birth = htmlspecialchars($aRow['birth_date']);
		$street = htmlspecialchars($aRow['street']);
		$house_no = htmlspecialchars($aRow['house_no']);
		$township_name = htmlspecialchars($aRow['township_name']);
		$ward_name = htmlspecialchars($aRow['ward_name']);
		
		$address = '';
		if($township_name != '')
			$address = $township_name.'၊'.$ward_name.'၊'.$street.'၊'.$house_no;
		
		$c++;
		$action = 'Lock Customer';	
		if($aRow['is_lock'] == 0)
		{
			if ( isset($pageenablearr["Edit"]) || $usertypeid == 0 )
				$action = "<a href='customer_edit.php?customer_id=$customer_id' title='Edit' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-edit' /></svg></a>";
			
			if ( isset($pageenablearr["Delete"])   || $usertypeid == 0 )
				$action .= "<a href id='icodelete$customer_id' title='Delete' data-toggle='modal' data-target='#modal-delete' onclick='delete_customer($customer_id, \"". rawurlencode($customer_name) ."\")' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-trash' /></svg></a>";
		}
		
		$tmpentry = array();
		$tmpentry[] = $is_change;
		$tmpentry[] = $action;
		$tmpentry[] = $c;
		$tmpentry[] = $customer_name;
		$tmpentry[] = $nrc_no;
		$tmpentry[] = $father_name;
		$tmpentry[] = $date_of_birth;
		$tmpentry[] = $address;
		$response['aaData'][] = $tmpentry;
	}
	
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
	header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
	header("Cache-Control: no-cache, no-store, must-revalidate" );
	header("Pragma: no-cache" );
	header("Content-type: text/x-json");
	echo json_encode($response);
	
	function fnColumnToField( $i )
	{
		if($i=6)
			return "DATE_FORMAT(date_of_birth, '%Y %m %d')";
	}
?>