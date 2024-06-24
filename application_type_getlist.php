<?php
	$movepath = '';
	require_once($movepath .'library/reference.php');
	require_once('autoload.php');
	include "adminauth.php";
	cleanGETforJQryDataTable();
	
	$application_type_bol=new application_type_bol();	
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
	$cri_str = ' WHERE 1=1 ';
	$param = array();
	if( isset($_GET['sSearch']) )
	{
		$criobj = json_safedecode($_GET['sSearch']);
		if( isset($criobj->cri_application_type_name) && $criobj->cri_application_type_name != '' )	
		{
			$cri_str .= " AND application_type_name LIKE :cri_application_type_name";	
			$param[':cri_application_type_name'] = '%'. $criobj->cri_application_type_name .'%';
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
	
	$rResult = $application_type_bol->select_application_type_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);	
	$iTotal = intval($rResult->getFoundRows());	
	
	$c=$DisplayStart;
	$response = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotal,'iTotalDisplayRecords'=>$iTotal,'aaData'=>array());	
	
	while($aRow = $rResult->getNext())
	{
		$c++;
		$action = '';
		if ( isset($pageenablearr["Edit"])   || $usertypeid == 0  )
			$action .= "<a href onclick='create_edit_application_type_popup($aRow[application_type_id])' title='Edit' data-toggle='modal' data-target='#modal-edit' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-edit' /></svg></a>";
		if ( isset($pageenablearr["Delete"])   || $usertypeid == 0  )
			$action .= "<a href id='icodelete$aRow[application_type_id]' title='Delete' data-toggle='modal' data-target='#modal-delete' onclick='delete_application_type($aRow[application_type_id], \"". rawurlencode($aRow['application_type_name']) ."\")' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-trash' /></svg></a>";
				
		$tmpentry = array();
		$tmpentry[] = $c;
		$tmpentry[] = htmlspecialchars($aRow['application_type_code']);
		$tmpentry[] = htmlspecialchars($aRow['application_type_name']);
		$tmpentry[] = $action;			
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
		if($i=1)
			return 'application_type_code';
		else if($i=2)
			return 'application_type_name';
		else	
			return 'application_type_id';
	}
?>