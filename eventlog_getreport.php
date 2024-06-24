<?php
	$movepath = '';
	require_once("autoload.php");
	require_once($movepath .'library/reference.php');
	$sEcho = intval($_GET['sEcho']);

	$eventlogbol=new eventlogbol();

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
		
		if( $criobj->cri_action_type && $criobj->cri_action_type != '0' )
		{
			$cri_str .= " AND action_type = :action_type";
			$param[':action_type'] =  $criobj->cri_action_type;
		}
		
		if( $criobj->cri_txtusername && $criobj->cri_txtusername != '' )
		{
			$cri_str .= " AND user_name LIKE :user_name";
			$param[':user_name'] =  '%' . $criobj->cri_txtusername.'%';
		}
		
		if( $criobj->cri_txt_fromdate && $criobj->cri_txt_fromdate != '' )
		{
			$cri_str .= " AND DATE(action_date) >= :from_date";
			$param[':from_date'] = to_ymd($criobj->cri_txt_fromdate);
		}
		
		if( $criobj->cri_txt_todate && $criobj->cri_txt_todate != '' )
		{
			$cri_str .= " AND DATE(action_date) <= :to_date";
			$param[':to_date'] = to_ymd($criobj->cri_txt_todate);
		}
	}
	$cri_arr = array($cri_str, $param);
	//print_r($cri_arr); exit();

	// Ordering
	$SortingCols = '';
	if ( isset($_GET['iSortCol_0']) )
	{
		$SortingCols = " ORDER BY ";
		for ( $i=0 ; $i < $_GET['iSortingCols']; $i++ )
		{
			$SortingCols .= fnColumnToField($_GET['iSortCol_'.$i])." ".$_GET['sSortDir_'.$i].", ";
		}
		$SortingCols = substr_replace($SortingCols, "", -2 );
	}

	$rResult = $eventlogbol->select_all_event_log($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);
	$iTotal = $rResult->getFoundRows();

	$c = $DisplayStart;
	$response = array('sEcho'=>$sEcho, 'iTotalRecords'=>$iTotal, 'iTotalDisplayRecords'=>$iTotal, 'aaData'=>array());

	while($aRow = $rResult->getNext())
	{
		$c++;
		$date = htmlspecialchars($aRow['action_date']);
		$description = htmlspecialchars($aRow['description']);
		$action_type = $aRow['action_type'];
		if( $action_type == 'Insert' )
			$action_type = 'အသစ်ထည့်ခြင်း';
		else if(  $action_type == 'Update' )
			$action_type = 'ပြင်ဆင်ခြင်း';
		else if(  $action_type == 'Delete' )
			$action_type = 'ဖျက်ခြင်း';
			
		$tmpentry = array();
		$tmpentry[] = $c;
		$tmpentry[] = htmlspecialchars($aRow['user_name']);
		$tmpentry[] = str_replace(" ", "<br>", $date);
		$tmpentry[] = $action_type;
		$tmpentry[] = htmlspecialchars($aRow['table_name']);

		if( strlen($description) > 85 )
			$tmpentry[] = mb_substr($description, 0, 85, 'UTF-8')." <a data-toggle='modal' data-target='#modal-description' onclick='detail_popup($aRow[id])' style='cursor:pointer;color:blue;'>Detail</a>";

		else
			$tmpentry[] = '<div style="word-wrap: break-word;">'. $description .'</div>';
		
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
		if ( $i == 0 )
			return TRUE;
		else if ( $i == 1 )
			return 'user_name';
		else if ( $i == 2 )
			return 'action_date';
		else if ( $i == 3 )
			return 'action_type';
		else if ( $i == 4 )
			return 'table_name';
		else
			return TRUE;
	}
?>