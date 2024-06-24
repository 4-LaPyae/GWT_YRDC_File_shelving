<?php
	$movepath = '';
	require_once($movepath .'library/reference.php');
	require_once('autoload.php');
	include "adminauth.php";
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
	$param = array();
	
	// Searching
	if(isset($_GET['folder_id']))
	{
		$folder_id = clean($_GET['folder_id']);
		$cri_str = "WHERE 1=1 AND folder_id = :folder_id AND status IN(1,2) ";
		$param[':folder_id'] = $folder_id;
	}
	
	// permission by user_type_security_type 
	if ( $usertypeid != 0 && ($security_type_enables !='' && $application_type_enables !='' ))
		$cri_str .= ' AND f.security_type_id IN ('.$security_type_enables.') AND f.application_type_id IN ('.$application_type_enables.')';
	
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
	
	$rResult = $file_bol->select_file_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);	
	$iTotal = intval($rResult->getFoundRows());	
	
	$c=$DisplayStart;
	$response = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotal,'iTotalDisplayRecords'=>$iTotal,'aaData'=>array());	
	
	while($aRow = $rResult->getNext())
	{
		$file_id = htmlspecialchars($aRow['file_id']);
		$letter_no = htmlspecialchars($aRow['letter_no']);
		$letter_count = htmlspecialchars($aRow['letter_count']);
		$letter_date = '<div class="text-nowrap">'.htmlspecialchars($aRow['now_date']);
		$description = '<div style="min-width: 200px">'.htmlspecialchars($aRow['description']).'</div>';
		$to_do = htmlspecialchars($aRow['to_do']);
		$remark = htmlspecialchars($aRow['remark']);
		$from_department_name = '<div style="min-width: 200px">'.htmlspecialchars($aRow['from_department_name']).'</div>';
		$security_type_name = htmlspecialchars($aRow['security_type_name']);
		$application_type_name = htmlspecialchars($aRow['application_type_name']);
		$application_description = htmlspecialchars($aRow['application_description']);
		$application_references = htmlspecialchars($aRow['application_references']);
		$receiver_customer_name = htmlspecialchars($aRow['receiver_customer_name']);
		$sender_customer_name = htmlspecialchars($aRow['sender_customer_name']);
		
		$c++;
		$action = '';
		if ( isset($pageenablearr["Edit"])   || $usertypeid == 0  )
			$action .= "<a href='file_update.php?file_id=$file_id' title='Edit' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-edit' /></svg></a>";
		
		if ( isset($pageenablearr["Delete"])   || $usertypeid == 0  )
			$action .= "<a id='icodelete$file_id' title='Delete' data-toggle='modal' data-target='#modal-delete' onclick='delete_file($file_id, \"". rawurlencode($letter_no) ."\")' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-trash' /></svg></a>";
		
		if ( isset($pageenablearr["စာများ အဝင်အထွက်စာရင်း"]) || $usertypeid == 0  )
			$action .= "<a href='file_transaction_list.php?file_id=$file_id' title='Transaction' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-transaction' /></svg></a>";
		
		if ( isset($pageenablearr["စာဖိုင်ဖျက်သိမ်းခြင်း"]) || $usertypeid == 0  )
			$action .= "<a href='file_delete.php?file_id=$file_id' title='File Delete' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-folder-delete' /></svg></a>";
		
		$tmpentry = array();
		$tmpentry[] = $action;
		$tmpentry[] = $c;
		$tmpentry[] = $letter_no;
		$tmpentry[] = $letter_date;
		$tmpentry[] = $from_department_name;
		$tmpentry[] = $sender_customer_name;
		$tmpentry[] = $receiver_customer_name;
		$tmpentry[] = $description;
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
		if($i=1)
			return "DATE_FORMAT(letter_date, '%Y %m %d %H:%i:%s')";
		else
			return TRUE;
	}
?>