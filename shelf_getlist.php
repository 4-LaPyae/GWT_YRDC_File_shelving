<?php
	$movepath = '';
	require_once($movepath .'library/reference.php');
	require_once('autoload.php');
	include "adminauth.php";
	cleanGETforJQryDataTable();
	
	$shelf_bol=new shelf_bol();	
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
	
	//permission by usertype_department
	if ( $usertypeid != 0 && $department_enables !='')
		$cri_str .= ' AND s.department_id IN ('.$department_enables.')';
	
	if( isset($_GET['sSearch']) )
	{
		$criobj = json_safedecode($_GET['sSearch']);
		if( isset($criobj->cri_shelf_name) && $criobj->cri_shelf_name != '' )	
		{
			$cri_str .= " AND shelf_name LIKE :cri_shelf_name";	
			$param[':cri_shelf_name'] = '%'. clean($criobj->cri_shelf_name) .'%';
		}
		if( isset($criobj->cri_location_id) && $criobj->cri_location_id != '' )
		{
			$cri_str .= " AND s.location_id = :location_id";	
			$param[':location_id'] = $criobj->cri_location_id;
		}
		if( isset($criobj->cri_department_id) && $criobj->cri_department_id != '' )
		{
			$cri_str .= " AND s.department_id = :department_id";
			$param[':department_id'] = $criobj->cri_department_id;
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
	
	$rResult = $shelf_bol->select_shelf_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);	
	$iTotal = intval($rResult->getFoundRows());	
	
	$c=$DisplayStart;
	$response = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotal,'iTotalDisplayRecords'=>$iTotal,'aaData'=>array());	
	
	while($aRow = $rResult->getNext())
	{
		$c++;
		$action = '';
		if ( isset($pageenablearr["Edit"])   || $usertypeid == 0  )
			$action .= "<a href onclick='create_edit_shelf_popup($aRow[shelf_id])' title='Edit' data-toggle='modal' data-target='#modal-edit' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-edit' /></svg></a>";
		if ( isset($pageenablearr["Delete"])   || $usertypeid == 0  )
			$action .= "<a href id='icodelete$aRow[shelf_id]' title='Delete' data-toggle='modal' data-target='#modal-delete' onclick='delete_shelf($aRow[shelf_id], \"". rawurlencode($aRow['shelf_name']) ."\")' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-trash' /></svg></a>";
		
		
		$tmpentry = array();
		$tmpentry[] = $c;
		$tmpentry[] = htmlspecialchars($aRow['shelf_code']);
		$tmpentry[] = htmlspecialchars($aRow['shelf_name']);
		$tmpentry[] = htmlspecialchars($aRow['location_name']);
		$tmpentry[] = htmlspecialchars($aRow['department_name']);
		$tmpentry[] = htmlspecialchars($aRow['no_of_row']);
		$tmpentry[] = htmlspecialchars($aRow['no_of_column']);
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
			return 'shelf_code';
		else if($i=2)
			return 'shelf_name';
		else	
			return 'shelf_id';
	}
?>