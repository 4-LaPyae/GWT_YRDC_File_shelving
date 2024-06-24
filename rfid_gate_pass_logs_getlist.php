<?php
	$movepath = '';
	require_once($movepath .'library/reference.php');
	require_once('autoload.php');
	include "adminauth.php";
	cleanGETforJQryDataTable();	
	$gate_bol=new gate_bol();	
	$sEcho = intval($_GET['sEcho']);
		
	// Paging
	$DisplayStart = 0;
	if ( isset($_GET['iDisplayStart']) )
		$DisplayStart = $_GET['iDisplayStart'];
	
	$DisplayLength = 10;
	if ( isset($_GET['iDisplayLength']) )
		$DisplayLength = $_GET['iDisplayLength'];
	
	// Searching
	$cri_str = ' WHERE 1=1 ';
	$param = array();
	if( isset($_GET['sSearch']) )
	{
		$criobj = json_safedecode($_GET['sSearch']);
		if( isset($criobj->cri_rfid_no) && $criobj->cri_rfid_no != '' )	
		{
			$cri_str .= " AND gl.rfid_no LIKE :cri_rfid_no";	
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
		
		if( isset($criobj->cri_gate_name) && $criobj->cri_gate_name != '' )	
		{
			$cri_str .= " AND gate_name LIKE :cri_gate_name";	
			$param[':cri_gate_name'] = '%'. clean($criobj->cri_gate_name) .'%';
		}
		
		if(isset($criobj->cri_txt_fromdate) && $criobj->cri_txt_fromdate != '')
		{
			$cri_str .= " AND DATE(log_time) >= :from_date  ";
			$param['from_date'] =   to_ymd($criobj->cri_txt_fromdate);				
		}
			
		if(isset($criobj->cri_txt_todate) && $criobj->cri_txt_todate != '')
		{
			$cri_str .= " AND DATE(log_time) <= :to_date  ";
			$param['to_date'] =   to_ymd($criobj->cri_txt_todate);				
		}
	}
	$cri_arr = array($cri_str, $param);
	//echo $cri_str; exit();
	
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
	$_SESSION['SESS_RFID_GATE_PASS_LOGS_SORTINGCOLS_MLR'] = $SortingCols;	//to get sorting in export file 
	
	$rResult = $gate_bol->select_rfid_gate_pass_log_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);	
	$iTotal = intval($rResult->getFoundRows());	
	
	$c=$DisplayStart;
	$response = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotal,'iTotalDisplayRecords'=>$iTotal,'aaData'=>array());	
	
	while($aRow = $rResult->getNext())
	{
		$c++;
		$tmpentry = array();
		$tmpentry[] = $c;
		$tmpentry[] = htmlspecialchars($aRow['rfid_card_no']);
		$tmpentry[] = htmlspecialchars($aRow['folder_no']);
		$tmpentry[] = htmlspecialchars($aRow['description']);
		$tmpentry[] = htmlspecialchars($aRow['now_date']);
		$tmpentry[] = htmlspecialchars($aRow['gate_name']);
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
			return "DATE_FORMAT(log_time, '%Y %m %d %H:%i:%s')";
		else
			return TRUE;
	}
?>