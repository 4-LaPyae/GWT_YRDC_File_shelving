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
	$cri_str = ' WHERE 1=1 AND status = 3 ';
	
	// permission by usertype_department 
	if ( $usertypeid != 0 && $department_enables !='')
		$cri_str .= ' AND td.department_id IN ('.$department_enables.')';
	
	// permission by user_type_security_type 
	if ( $usertypeid != 0 && $security_type_enables !='')
		$cri_str .= ' AND f.security_type_id IN ('.$security_type_enables.')';
	
	// permission by user_type_application_type 
	if ( $usertypeid != 0 && $application_type_enables !='')
		$cri_str .= ' AND f.application_type_id IN ('.$application_type_enables.')';
	
	$param = array();
	if( isset($_GET['sSearch']) )
	{
		$criobj = json_safedecode($_GET['sSearch']);
		
		if( isset($criobj->cri_letter_no) && $criobj->cri_letter_no != '' )	
		{
			$cri_str .= " AND letter_no LIKE :cri_letter_no";	
			$param[':cri_letter_no'] = '%'. clean($criobj->cri_letter_no) .'%';
		}
		
		if(isset($criobj->cri_txt_fromdate) && $criobj->cri_txt_fromdate != '')
		{
			$cri_str .= " AND DATE(letter_date) >= :from_date  ";
			$param['from_date'] =   to_ymd($criobj->cri_txt_fromdate);				
		}
			
		if(isset($criobj->cri_txt_todate) && $criobj->cri_txt_todate != '')
		{
			$cri_str .= " AND DATE(letter_date) <= :to_date  ";
			$param['to_date'] =   to_ymd($criobj->cri_txt_todate);				
		}
		
		if( isset($criobj->cri_file_description) && $criobj->cri_file_description != '' )	
		{
			$cri_str .= " AND description LIKE :cri_file_description";	
			$param[':cri_file_description'] = '%'. clean($criobj->cri_file_description) .'%';
		}
		
		if( isset($criobj->cri_application_type_id) && $criobj->cri_application_type_id != '' )
		{
			$cri_str .= " AND f.application_type_id = :application_type_id";
			$param[':application_type_id'] = $criobj->cri_application_type_id;
		}
		
		if( isset($criobj->cri_security_type_id) && $criobj->cri_security_type_id != '' )
		{
			$cri_str .= " AND f.security_type_id = :security_type_id";
			$param[':security_type_id'] = $criobj->cri_security_type_id;
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
	$_SESSION['SESS_BURN_FILE_SORTINGCOLS_MLR'] = $SortingCols;	//to get sorting in export file 
	
	$rResult = $report_bol->select_file_burn_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);	
	$iTotal = intval($rResult->getFoundRows());	
	
	$c=$DisplayStart;
	$response = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotal,'iTotalDisplayRecords'=>$iTotal,'aaData'=>array());	
	
	while($aRow = $rResult->getNext())
	{
		$destroy_order_no = htmlspecialchars($aRow['destroy_order_no']);
		$destroy_order_employeeid = htmlspecialchars($aRow['destroy_order_employeeid']);
		$destroy_order_employee_name = htmlspecialchars($aRow['destroy_order_employee_name']);
		$destroy_order_department = htmlspecialchars($aRow['destroy_order_department']);
		$destroy_date = '<div class="text-nowrap">'.htmlspecialchars($aRow['now_destroy_date']);
		$destroy_duty_employeeid = htmlspecialchars($aRow['destroy_duty_employeeid']);
		$destroy_duty_employee_name = htmlspecialchars($aRow['destroy_duty_employee_name']);
		$destroy_duty_department = htmlspecialchars($aRow['destroy_duty_department']);
		$letter_no = htmlspecialchars($aRow['letter_no']);
		$letter_date = '<div class="text-nowrap">'.htmlspecialchars($aRow['now_letter_date']);
		$description = '<div style="min-width: 200px">'.htmlspecialchars($aRow['description']).'</div>';
		$security_type_name = htmlspecialchars($aRow['security_type_name']);
		$application_type_name = htmlspecialchars($aRow['application_type_name']);

		$c++;
		$tmpentry = array();
		$tmpentry[] = $c;
		$tmpentry[] = $destroy_order_no;
		$tmpentry[] = $destroy_date;
		$tmpentry[] = $destroy_order_employeeid;
		$tmpentry[] = $destroy_order_employee_name;
		$tmpentry[] = $destroy_order_department;
		$tmpentry[] = $destroy_duty_employeeid;
		$tmpentry[] = $destroy_duty_employee_name;
		$tmpentry[] = $destroy_duty_department;
		$tmpentry[] = $letter_no;
		$tmpentry[] = $letter_date;
		$tmpentry[] = $description;
		$tmpentry[] = $security_type_name;
		$tmpentry[] = $application_type_name;
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
		if($i=2)
			return "DATE_FORMAT(destroy_date, '%Y %m %d %H:%i:%s')";
		else
			return TRUE;
	}
?>