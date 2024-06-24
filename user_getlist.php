<?php
	$movepath = '';
	require_once($movepath .'library/reference.php');
	require_once('autoload.php');
	require_once ("adminauth.php");
	cleanGETforJQryDataTable();	
	$userbol=new userbol();	
	
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
	
	/* 1. Root Admin, 2. Department, 3. User */
	$root_admin = 0;		
	if(isset($_SESSION ['YRDCFSH_ROOT_ADMIN']))
		$root_admin = $_SESSION ['YRDCFSH_ROOT_ADMIN'];
	// echo $root_admin;exit;
	
	// Searching
	$cri_str = ' WHERE 1=1 ';
	// permission by usertype_department
	if ( $usertypeid != 0 )
	{
		if($root_admin == 1 || $root_admin == 2 || $root_admin == 3)
			$cri_str .="AND td.department_id IN ($department_enables) AND td.is_root_admin >= $root_admin ";
	}
	$param = array();
	if( isset($_GET['sSearch']) )
	{
		$cri_obj = json_safedecode($_GET['sSearch']);
		
		if(isset($cri_obj->cri_username) && $cri_obj->cri_username != '')
		{
			$cri_str .= " AND user_name LIKE :user_name";
			$param['user_name'] = "%". $cri_obj->cri_username."%" ;				
		}	
		
		if(isset($cri_obj->cri_user_email) && $cri_obj->cri_user_email !='')
		{
			$cri_str .= " AND user_email LIKE :user_email";
			$param['user_email'] = '%' . $cri_obj->cri_user_email.'%';				
		}
		
		if(isset($cri_obj->cri_usertype) && $cri_obj->cri_usertype !='')
		{
			$cri_str .= " AND u.user_type_id = :user_type_id";
			$param['user_type_id'] =   $cri_obj->cri_usertype ;				
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
	if( $result = $userbol->selectuser( $DisplayStart, $DisplayLength, $SortingCols, $cri_arr) )
	{
		$currenturl = $_SERVER['SCRIPT_FILENAME'];
		$securitybol = new securitybol();
		
		$user_ids = '';
		while( $row = $result->getNext() )
		{
			$user_ids .= $row['user_id'] . ',';
		}
		
		$user_cri_arr = '';
		if( $user_ids != '' )
		{
			$user_ids = substr_replace( $user_ids, "", -1 );
			
			$in_query_string_arr = get_in_query_string($user_ids);
			$cri = ' AND user_id IN ('.$in_query_string_arr[0].')';
			$user_cri_arr = array($cri, $in_query_string_arr[1]);
			//print_r($user_cri_arr);exit;
			$change_user = $securitybol->check_and_change_invalid_record('user', $user_cri_arr, $currenturl);
			
			if( $change_user )
				$is_change = 1;
		}
	}
	
	$rResult=$userbol->selectuser( $DisplayStart, $DisplayLength, $SortingCols, $cri_arr);	
	$iTotal = $rResult->getFoundRows();
	
	$c=$DisplayStart;
	$response = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotal,'iTotalDisplayRecords'=>$iTotal,'aaData'=>array());	
	
	while($aRow = $rResult->getNext())
	{
		$c++;
		$user_type_id = $aRow['user_type_id'];
		
		if( $aRow['is_active'] == 0 )
		{
			$title='Inactive';
			$attr="style='opacity:0.4;'";
		}
		else
		{
			$title='Active';
			$attr='';
		}
		
		$action = '';		
		if ( $usertypeid != $user_type_id)
		{
			if ( isset($pageenablearr["Edit"])   || $usertypeid == 0  )
				$action .= "<a href onclick='create_edit_user_popup($aRow[user_id])' title='Edit' data-toggle='modal' data-target='#modal-edit' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-edit' /></svg></a>";

			if ( isset($pageenablearr["ChangePassword"])   || $usertypeid == 0  )
				$action .= "<span id='divprogress$aRow[user_id]'></span>
							<a href title='Change Password' data-toggle='modal' data-target='#modal-changepassword' onclick='create_change_password_popup($aRow[user_id],\"". rawurlencode($aRow['user_name']) ."\")' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-key' /></svg></a>";
			
			if(isset($pageenablearr["ChangeStatus"]) || $usertypeid==0)
				$action .= "<a href title='$title' data-toggle='modal' data-target='#modal-active' onclick='change_user_status($aRow[user_id],\"". rawurlencode($aRow['user_name']) ."\", \"$aRow[is_active]\")' class='d-inline-flex' $attr><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-activate-user' /></svg></a>";

			if ( isset($pageenablearr["Delete"])   || $usertypeid == 0  )
				$action .= "<a href id='icodelete$aRow[user_id]' title='Delete' data-toggle='modal' data-target='#modal-delete' onclick='delete_user($aRow[user_id], \"". rawurlencode($aRow['user_name']) ."\")' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-trash' /></svg></a>";
		}
		
		if($aRow['is_active'] == 2)	
			$action = " Lock User ";
		if ( $aRow['user_type_id'] == 0 )
			$action = '';
			
		$tmpentry = array();		
		$tmpentry[] = $c;		
		$tmpentry[] = htmlspecialchars($aRow['user_name']);		
		$tmpentry[] = htmlspecialchars($aRow['user_email']);				
		$tmpentry[] = htmlspecialchars($aRow['user_type_name']);	
		$tmpentry[] = $action;
		$tmpentry[] = $is_change;
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
			return true;
		else if ( $i == 1 )
			return 'u.user_name';	
		else if ( $i == 2 )
			return 'u.user_email';
		else if ( $i == 3 )
			return 'ut.user_type_name';		
		else
			return true;
	}
?>