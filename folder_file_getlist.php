<?php
	$movepath = '';
	require_once($movepath .'library/reference.php');
	require_once('autoload.php');
	require_once('adminauth.php');
	cleanGETforJQryDataTable();
	
	$file_bol=new file_bol();	
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
		$cri_str .= ' AND td.department_id IN ('.$department_enables.')';
	
	// permission by user_type_security_type 
	if ( $usertypeid != 0 && $security_type_enables !='')
		$cri_str .= ' AND f.security_type_id IN ('.$security_type_enables.')';
	
	// permission by user_type_application_type 
	if ( $usertypeid != 0 && $application_type_enables !='')
		$cri_str .= ' AND f.application_type_id IN ('.$application_type_enables.')';
	
	// permission by user_type_security_type 
	/* if ( $usertypeid != 0 && ($security_type_enables !='' && $application_type_enables !='' ))
		$cri_str .= ' AND f.security_type_id IN ('.$security_type_enables.') AND f.application_type_id IN ('.$application_type_enables.')'; */
		
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
			$cri_str .= " AND td.folder_description LIKE :cri_folder_description";	
			$param[':cri_folder_description'] = '%'. clean($criobj->cri_folder_description) .'%';
		}
		
		if( isset($criobj->cri_shelf_id) && $criobj->cri_shelf_id != '' )	
		{
			$cri_str .= " AND td.shelf_id = :shelf_id";
			$param[':shelf_id'] = $criobj->cri_shelf_id;
		}
		
		if( isset($criobj->cri_file_type_id) && $criobj->cri_file_type_id != '' )
		{
			$cri_str .= " AND td.file_type_id = :file_type_id";
			$param[':file_type_id'] = $criobj->cri_file_type_id;
		}
		
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
			$cri_str .= " AND file_description LIKE :cri_file_description";	
			$param[':cri_file_description'] = '%'. clean($criobj->cri_file_description) .'%';
		}
		
		if( isset($criobj->cri_department_id) && $criobj->cri_department_id != '0' )
		{
			$cri_str .= " AND f.from_department_id = :from_department_id";
			$param[':from_department_id'] = $criobj->cri_department_id;
		}
		
		if( isset($criobj->cri_sender_customer_id) && $criobj->cri_sender_customer_id != '' )
		{
			$cri_str .= " AND f.sender_customer_id = :sender_customer_id";
			$param[':sender_customer_id'] = $criobj->cri_sender_customer_id;
		}
		
		if( isset($criobj->cri_receiver_customer_id) && $criobj->cri_receiver_customer_id != '' )
		{
			$cri_str .= " AND f.receiver_customer_id = :receiver_customer_id";
			$param[':receiver_customer_id'] = $criobj->cri_receiver_customer_id;
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
	
	$rResult = $file_bol->select_folder_file_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);	
	$iTotal = intval($rResult->getFoundRows());	
	
	$c=$DisplayStart;
	$response = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotal,'iTotalDisplayRecords'=>$iTotal,'aaData'=>array());	
	
	while($aRow = $rResult->getNext())
	{
		$rfid_no = htmlspecialchars($aRow['rfid_no']);
		$folder_no = htmlspecialchars($aRow['folder_no']);
		$file_type_name = htmlspecialchars($aRow['file_type_name']);
		$folder_security_type_name = htmlspecialchars($aRow['folder_security_type_name']);
		$shelf_name = htmlspecialchars($aRow['shelf_name']);
		$shelf_row = htmlspecialchars($aRow['shelf_row']);
		$shelf_column = htmlspecialchars($aRow['shelf_column']);
		$folder_description = htmlspecialchars($aRow['folder_description']);
		$letter_no = htmlspecialchars($aRow['letter_no']);
		$letter_count = htmlspecialchars($aRow['letter_count']);
		$letter_date = '<div class="text-nowrap">'.htmlspecialchars($aRow['now_date']);
		$file_description = '<div style="min-width: 200px">'.htmlspecialchars($aRow['file_description']).'</div>';
		$to_do = htmlspecialchars($aRow['to_do']);
		$remark = htmlspecialchars($aRow['remark']);
		$from_department_name = '<div style="min-width: 200px">'.htmlspecialchars($aRow['department_name']).'</div>';
		$security_type_name = htmlspecialchars($aRow['file_security_type_name']);
		$application_type_name = htmlspecialchars($aRow['application_type_name']);
		$receiver_customer_name = htmlspecialchars($aRow['receiver_customer_name']);
		$sender_customer_name = htmlspecialchars($aRow['sender_customer_name']);
		
		$c++;
		$tmpentry = array();
		$tmpentry[] = $c;
		$tmpentry[] = $rfid_no;
		$tmpentry[] = $folder_no;
		$tmpentry[] = $folder_description;
		$tmpentry[] = $file_type_name;
		$tmpentry[] = $shelf_name;
		$tmpentry[] = $shelf_row;
		$tmpentry[] = $shelf_column;
		$tmpentry[] = $letter_no;
		$tmpentry[] = $letter_date;
		$tmpentry[] = $from_department_name;
		$tmpentry[] = $sender_customer_name;
		$tmpentry[] = $receiver_customer_name;
		$tmpentry[] = $file_description;
		$tmpentry[] = $security_type_name;
		$tmpentry[] = $application_type_name;
		$tmpentry[] = $letter_count;
		$tmpentry[] = $to_do;
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
		if($i=9)
			return "DATE_FORMAT(letter_date, '%Y %m %d %H:%i:%s')";
		else
			return TRUE;
	}
?>