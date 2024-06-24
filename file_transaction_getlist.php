<?php
	$movepath = '';
	require_once($movepath .'library/reference.php');
	require_once('autoload.php');
	include "adminauth.php";
	cleanGETforJQryDataTable();
	
	$file_transaction_bol=new file_transaction_bol();	
	
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
	if(isset($_GET['file_id']))
	{
		$file_id = clean($_GET['file_id']);
		$cri_str = "WHERE 1=1 AND file_id = :file_id ";
		$param = array(":file_id"=>$file_id);
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
	
	$rResult = $file_transaction_bol->select_file_transaction_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);	
	$iTotal = intval($rResult->getFoundRows());	
	
	$c=$DisplayStart;
	$response = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotal,'iTotalDisplayRecords'=>$iTotal,'aaData'=>array());	
	
	while($aRow = $rResult->getNext())
	{
		$file_transaction_id = htmlspecialchars($aRow['file_transaction_id']);
		$file_id = htmlspecialchars($aRow['file_id']);
		$taken_date = htmlspecialchars($aRow['now_taken_date']);
		$given_date = htmlspecialchars($aRow['now_given_date']);
		$taken_employeeid = htmlspecialchars($aRow['taken_employeeid']);
		$taken_employee_name = htmlspecialchars($aRow['taken_employee_name']);
		$taken_designation = htmlspecialchars($aRow['taken_designation']);
		$taken_department = htmlspecialchars($aRow['taken_department']);
		$remark = htmlspecialchars($aRow['remark']);
		
		$c++;
		$action = '';
		if ( isset($pageenablearr["Edit"]) || $usertypeid == 0  )
			$action .= "<a href='file_transaction_update.php?file_transaction_id=$file_transaction_id' title='Edit' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-edit' /></svg></a>";
		
		if ( isset($pageenablearr["Delete"]) || $usertypeid == 0  )
			$action .= "<a href id='icodelete$file_transaction_id' title='Delete' data-toggle='modal' data-target='#modal-delete' onclick='delete_file_transaction($file_transaction_id, \"". rawurlencode($taken_employee_name) ."\")' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-trash' /></svg></a>";
		
		if($given_date == '')
		{
			if ( isset($pageenablearr["Backup"]) || $usertypeid == 0  )
				$action .= "<a href title='Backup' data-toggle='modal' data-target='#modal-backup' onclick='create_add_given_date_popup($file_transaction_id, $file_id)' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-back-file' /></svg></a>";
		}
		
		$tmpentry = array();
		$tmpentry[] = $action;
		$tmpentry[] = $c;
		$tmpentry[] = $taken_employeeid;
		$tmpentry[] = $taken_employee_name;
		$tmpentry[] = $taken_designation;
		$tmpentry[] = $taken_department;
		$tmpentry[] = $taken_date;
		$tmpentry[] = $given_date;
		$tmpentry[] = $remark;
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
			return "DATE_FORMAT(taken_date, '%Y %m %d %H:%i:%s')";
		else
			return TRUE;
	}
?>