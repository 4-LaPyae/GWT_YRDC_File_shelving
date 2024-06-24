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
	$cri_str = ' WHERE 1=1';
	
	// permission by usertype_department 
	if ( $usertypeid != 0 && $department_enables !='')
		$cri_str .= ' AND s.department_id IN ('.$department_enables.')';
	
	// permission by user_type_security_type 
	if ( $usertypeid != 0 && $security_type_enables !='')
		$cri_str .= ' AND f.security_type_id IN ('.$security_type_enables.')';
	
	$param = array();
	if( isset($_GET['sSearch']) )
	{
		$criobj = json_safedecode($_GET['sSearch']);
		
		if(isset($criobj->cri_txt_fromdate) && $criobj->cri_txt_fromdate != '')
		{
			$cri_str .= " AND DATE(created_date) >= :from_date  ";
			$param['from_date'] =   to_ymd($criobj->cri_txt_fromdate);				
		}
			
		if(isset($criobj->cri_txt_todate) && $criobj->cri_txt_todate != '')
		{
			$cri_str .= " AND DATE(created_date) <= :to_date  ";
			$param['to_date'] =   to_ymd($criobj->cri_txt_todate);				
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
	$_SESSION['SESS_SUMMARY_FOLDER_SORTINGCOLS_MLR'] = $SortingCols;	//to get sorting in export file 

	$rResult = $report_bol->select_folder_summary_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);	
	$iTotal = intval($rResult->getFoundRows());	
	
	$c=$DisplayStart;
	$response = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotal,'iTotalDisplayRecords'=>$iTotal,'aaData'=>array());	
	
	$sum_folder_count = $sum_borrow_count = $sum_destroy_count = 0;
	if( $rResult->rowCount() > 0 )
	{
		while($aRow = $rResult->getNext())
		{
			$department_name = htmlspecialchars($aRow['department_name']);
			$folder_count = htmlspecialchars($aRow['folder_count']);
			$borrow_count = htmlspecialchars($aRow['borrow_count']);
			$destroy_count = htmlspecialchars($aRow['destroy_count']);
			
			$shelf_have_count = 0;	
			if	($folder_count > 0)
				$shelf_have_count = $folder_count - $borrow_count; 
		
			$c++;
			$tmpentry = array();
			$tmpentry[] = $c;
			$tmpentry[] = $department_name;
			$tmpentry[] = $folder_count;
			$tmpentry[] = $borrow_count;
			$tmpentry[] = $shelf_have_count;
			$tmpentry[] = $destroy_count;
			$response['aaData'][] = $tmpentry;
		}
		// စုစုပေါင်း		
		$response['aaData'][] = total_folder_summary_report();
	}
	
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
	header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
	header("Cache-Control: no-cache, no-store, must-revalidate" );
	header("Pragma: no-cache" );
	header("Content-type: text/x-json");
	echo json_encode($response);
	
	function fnColumnToField( $i )
	{
		if($i=1)
			return "s.department_id";
		else
			return TRUE;
	}
	
	// စုစုပေါင်း
	function total_folder_summary_report()
	{
		global $report_bol, $cri_arr;
		$total_arr = $report_bol->get_total_folder_summary_report($cri_arr);	
		$sum_folder_count = $total_arr['sum_folder_count'];
		$sum_borrow_count = $total_arr['sum_borrow_count'];
		$sum_destroy_count = $total_arr['sum_destroy_count'];
		
		$sum_shelf_have_count = 0;	
		if	($sum_folder_count > 0)
			$sum_shelf_have_count = $sum_folder_count - $sum_borrow_count; 		
			
		$tmpentryout = array();
		$tmpentryout[]  = '';
		$tmpentryout[]  = 'စုစုပေါင်း';
		$tmpentryout[]  = $sum_folder_count;
		$tmpentryout[]  = $sum_borrow_count;
		$tmpentryout[]  = $sum_shelf_have_count;
		$tmpentryout[]  = $sum_destroy_count;
		return $tmpentryout;
	}
?>