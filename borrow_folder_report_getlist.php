<?php
	$movepath = '';
	require_once($movepath .'library/reference.php');
	require_once('autoload.php');
	require_once('adminauth.php');
	cleanGETforJQryDataTable();	
	$report_bol=new report_bol();	
	
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
	$cri_str = ' WHERE 1=1 AND status = 2 ';
	
	// permission by usertype_department 
	if ( $usertypeid != 0 && $department_enables !='')
		$cri_str .= ' AND sf.department_id IN ('.$department_enables.')';
	
	// permission by user_type_security_type 
	if ( $usertypeid != 0 && $security_type_enables !='')
		$cri_str .= ' AND fd.security_type_id IN ('.$security_type_enables.')';
	
	$param = array();
	if( isset($_GET['sSearch']) )
	{
		$criobj = json_safedecode($_GET['sSearch']);
		
		if( isset($criobj->cri_rfid_no) && $criobj->cri_rfid_no != '' )	
		{
			$cri_str .= " AND rfid_no LIKE :cri_rfid_no";	
			$param[':cri_rfid_no'] = '%'. clean($criobj->cri_rfid_no) .'%';
		}
		
		if( isset($criobj->cri_folder_no) && $criobj->cri_folder_no != '' )	
		{
			$cri_str .= " AND folder_no LIKE :cri_folder_no";	
			$param[':cri_folder_no'] = '%'. clean($criobj->cri_folder_no) .'%';
		}
		
		if( isset($criobj->cri_folder_description) && $criobj->cri_folder_description != '' )	
		{
			$cri_str .= " AND description LIKE :cri_folder_description";	
			$param[':cri_folder_description'] = '%'. clean($criobj->cri_folder_description) .'%';
		}
		
		if( isset($criobj->cri_file_type_id) && $criobj->cri_file_type_id != '' )
		{
			$cri_str .= " AND fd.file_type_id = :file_type_id";
			$param[':file_type_id'] = $criobj->cri_file_type_id;
		}
	}
	$cri_arr = array($cri_str, $param);
	// print_r($cri_arr);exit;
	
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
	$_SESSION['SESS_BORROW_FOLDER_SORTINGCOLS_MLR'] = $SortingCols;	//to get sorting in export file 

	$rResult = $report_bol->select_folder_borrow_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);	
	$iTotal = intval($rResult->getFoundRows());	
	
	$c=$DisplayStart;
	$response = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotal,'iTotalDisplayRecords'=>$iTotal,'aaData'=>array());	
	
	while($aRow = $rResult->getNext())
	{
		$taken_employeeid = htmlspecialchars($aRow['taken_employeeid']);
		$taken_employee_name = htmlspecialchars($aRow['taken_employee_name']);
		$taken_department = htmlspecialchars($aRow['taken_department']);
		$taken_date = '<div class="text-nowrap">'.htmlspecialchars($aRow['now_taken_date']);
		$rfid_no = htmlspecialchars($aRow['rfid_no']);
		$folder_no = htmlspecialchars($aRow['folder_no']);
		$description = htmlspecialchars($aRow['description']);
		$file_type_name = htmlspecialchars($aRow['file_type_name']);
				
		$c++;
		$tmpentry = array();
		$tmpentry[] = $c;
		$tmpentry[] = $taken_employeeid;
		$tmpentry[] = $taken_employee_name;
		$tmpentry[] = $taken_department;
		$tmpentry[] = $taken_date;
		$tmpentry[] = $rfid_no;
		$tmpentry[] = $folder_no;
		$tmpentry[] = $description;
		$tmpentry[] = $file_type_name;
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
		if($i=4)
			return "DATE_FORMAT(taken_date, '%Y %m %d %H:%i:%s')";
		else
			return TRUE;
	}
?>