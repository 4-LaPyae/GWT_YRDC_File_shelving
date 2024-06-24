<?php
	$movepath = '';
	require_once($movepath .'library/reference.php');
	require_once('autoload.php');
	require_once('adminauth.php');
	cleanGETforJQryDataTable();
	
	$folder_bol=new folder_bol();
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
	$cri_str = ' WHERE 1=1 AND status IN (1,2) ';
	
	// permission by usertype_department 
	if ( $usertypeid != 0 && $department_enables !='')
		$cri_str .= ' AND sf.department_id IN ('.$department_enables.')';
	
	// permission by user_type_security_type 
	if ( $usertypeid != 0 && $security_type_enables !='')
		$cri_str .= ' AND f.security_type_id IN ('.$security_type_enables.')';
	
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
		
		if( isset($criobj->cri_shelf_id) && $criobj->cri_shelf_id != '' )	
		{
			$cri_str .= " AND f.shelf_id = :shelf_id";
			$param[':shelf_id'] = $criobj->cri_shelf_id;
		}
		
		if( isset($criobj->cri_file_type_id) && $criobj->cri_file_type_id != '' )
		{
			$cri_str .= " AND f.file_type_id = :file_type_id";
			$param[':file_type_id'] = $criobj->cri_file_type_id;
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
	
	// Check with Encrypt Value
	$is_change = 0;
	if( $result = $folder_bol->select_folder_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) )
	{
		$currenturl = clean($_SERVER['SCRIPT_FILENAME']);
		$securitybol = new securitybol();
		
		$folder_ids = '';
		while( $row = $result->getNext() )
		{
			$folder_ids .= $row['folder_id'] . ',';
		}
		
		$folder_cri_arr = '';
		if( $folder_ids != '' )
		{
			$folder_ids = substr_replace( $folder_ids, "", -1 );
			
			$in_query_string_arr = get_in_query_string($folder_ids);
			$cri = ' AND folder_id IN ('.$in_query_string_arr[0].')';
			$folder_cri_arr = array($cri, $in_query_string_arr[1]);
			//print_r($folder_cri_arr);exit;
			$change_folder = $securitybol->check_and_change_invalid_record('folder', $folder_cri_arr, $currenturl);
			$change_folder_transaction = $securitybol->check_and_change_invalid_record('folder_transaction', $folder_cri_arr, $currenturl);
			$change_file = $securitybol->check_and_change_invalid_record('file', $folder_cri_arr, $currenturl);
			
			$folder_transaction_cri = ' AND folder_transaction_id IN ('.$in_query_string_arr[0].')';
			$folder_transaction_cri_arr = array($folder_transaction_cri, $in_query_string_arr[1]);
			//print_r($folder_transaction_cri_arr);exit;
			$change_file_transaction = $securitybol->check_and_change_invalid_record('file_transaction', $folder_transaction_cri_arr, $currenturl);
			if( $change_folder || $change_folder_transaction || $change_file  || $change_file_transaction )
				$is_change = 1;
		}
	}
	
	$rResult = $folder_bol->select_folder_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);	
	$iTotal = intval($rResult->getFoundRows());	
	
	$c=$DisplayStart;
	$response = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotal,'iTotalDisplayRecords'=>$iTotal,'aaData'=>array());	
	
	while($aRow = $rResult->getNext())
	{
		$folder_id = htmlspecialchars($aRow['folder_id']);
		$rfid_no = htmlspecialchars($aRow['rfid_no']);
		$folder_no = htmlspecialchars($aRow['folder_no']);
		$file_type_name = htmlspecialchars($aRow['file_type_name']);
		$folder_security_type_name = htmlspecialchars($aRow['security_type_name']);
		$shelf_id = htmlspecialchars($aRow['shelf_id']);
		$shelf_name = htmlspecialchars($aRow['shelf_name']);
		$shelf_row = htmlspecialchars($aRow['shelf_row']);
		$shelf_column = htmlspecialchars($aRow['shelf_column']);
		$description = htmlspecialchars($aRow['description']);
		$file_count = htmlspecialchars($aRow['letter_count_no']);
		
		$c++;
		$action = 'Lock Folder';	
		if($aRow['is_lock'] == 0)
		{
			if ( isset($pageenablearr["Location"]) || $usertypeid == 0  )
				$action = "<a href='folder_location.php?folder_id=$folder_id&shelf_id=$shelf_id&rfid_no=$rfid_no' title='Folder Location' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-file-cabinet' /></svg></a>";
			
			if ( isset($pageenablearr["Detail"]) || $usertypeid == 0  )
				$action .= "<a href='folder_detail.php?folder_id=$folder_id' title='Detail' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-detail-view' /></svg></a>";
			
			if ( isset($pageenablearr["Edit"]) || $usertypeid == 0  )
				$action .= "<a href='folder_edit.php?folder_id=$folder_id' title='Edit' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-edit' /></svg></a>";
			
			if ( isset($pageenablearr["Delete"])   || $usertypeid == 0  )
				$action .= "<a href id='icodelete$folder_id' title='Delete' data-toggle='modal' data-target='#modal-delete' onclick='delete_folder($folder_id, \"". rawurlencode($file_type_name) ."\")' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-trash' /></svg></a>";
			
			if ( isset($pageenablearr["စာဖိုင်တွဲ အဝင်အထွက်စာရင်း"]) || $usertypeid == 0  )
				$action .= "<a href='transaction_list.php?folder_id=$folder_id' title='Transaction' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-transaction' /></svg></a>";

			if ( isset($pageenablearr["ဖိုင်စာရင်း"]) || $usertypeid == 0  )
				$action .= "<a href='file_list.php?folder_id=$folder_id' title='File List' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-file-list-box' /></svg></i></a>";
			
			if ( isset($pageenablearr["စာဖိုင်တွဲဖျက်သိမ်းခြင်း"]) || $usertypeid == 0  )
				$action .= "<a href='folder_delete.php?folder_id=$folder_id' title='Folder Delete' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-folder-delete' /></svg></a>";
		}
		
		$tmpentry = array();
		$tmpentry[] = $is_change;
		$tmpentry[] = $action;
		$tmpentry[] = $c;
		$tmpentry[] = $rfid_no;
		$tmpentry[] = $folder_no;
		$tmpentry[] = $description;
		$tmpentry[] = $file_type_name;
		$tmpentry[] = $shelf_name;
		$tmpentry[] = $shelf_row;
		$tmpentry[] = $shelf_column;
		$tmpentry[] = $file_count;
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
			return 'rfid_no';
	}
?>