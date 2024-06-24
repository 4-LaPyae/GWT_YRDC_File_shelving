<?php	
	$movepath = ''; 
	require_once($movepath . 'library/reference.php');
	require_once("autoload.php");
	require_once ("adminauth.php");
	cleanGETforJQryDataTable();
	$usertypebol = new usertypebol();	
	
	$usertypeid = 0;
	if( isset($_SESSION ['YRDCFSH_LOGIN_TYPE_ID']) )		
		$usertypeid = $_SESSION['YRDCFSH_LOGIN_TYPE_ID'];
	
	$sEcho = 0;
	if(isset($_GET['sEcho']))
		$sEcho = intval($_GET['sEcho']);
	
	// Paging
	$DisplayStart = 0;
	if( isset( $_GET['iDisplayStart'] ) )
		$DisplayStart = $_GET['iDisplayStart'];

	$DisplayLength = 10;
	if( isset( $_GET['iDisplayLength'] ) )
		$DisplayLength = $_GET['iDisplayLength'];
	
	/* 1. Root Admin, 2. Department, 3. User */
	$root_admin = 0;
	if( isset( $_GET['root_admin'] ) )
		$root_admin = $_GET['root_admin'];
	// echo $root_admin;exit;
	
	$cri_str =" ";		
	// permission by usertype_department
	if ( $usertypeid != 0 )
	{
		if($root_admin == 1 || $root_admin == 2 || $root_admin == 3)
			$cri_str =" WHERE 1=1 AND department_id IN ($department_enables) AND is_root_admin >= $root_admin ";
	}
	// echo $cri_str;exit;
	
	// Ordering
	$SortingCols = '';
	if( isset( $_GET['iSortCol_0'] ) )
	{
		$SortingCols = "ORDER BY  ";
		for ( $i=0 ; $i < $_GET['iSortingCols']; $i++ )
		{
			$SortingCols .= fnColumnToField($_GET['iSortCol_'.$i])."	".$_GET['sSortDir_'.$i].", ";
		}
		$SortingCols = substr_replace($SortingCols, "", -2 );
	}
	
	$result = $usertypebol->get_all_usetype_list($DisplayStart, $DisplayLength, $SortingCols, $cri_str);
	$iTotal = $result->getFoundRows();
	$response = array('sEcho'=>$sEcho, 'iTotalRecords'=>intval($iTotal), 'iTotalDisplayRecords'=>intval($iTotal), 'aaData'=>array());	
	
	if($result->rowCount() > 0)
	{
		$c = $DisplayStart;
		while($row = $result->getNext())
		{
			$c++;
			$user_type_name = htmlspecialchars($row['user_type_name']);
			$user_type_id = htmlspecialchars($row['user_type_id']);
			
			// echo 'tt='.$usertypeid.'=='.$user_type_id.'<br/>';
			
			$action = '';
			if ( $usertypeid != $user_type_id)
			{
				$action = "<a href='usertype_update.php?usertype=$user_type_id' title='Edit' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-edit' /></svg></a>
				<a href id='icodelete$user_type_id' title='Delete' data-toggle='modal' data-target='#modal-delete' onclick='delete_user_type($user_type_id, \"". rawurlencode($user_type_name) ."\")' class='d-inline-flex'><svg class='icon i-xs p-1 text-black'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='js/symbol-defs.svg#icon-trash' /></svg></a>";
			}
			$tmpentry = array();
			$tmpentry[] = $c;
			$tmpentry[] = $user_type_name;
			$tmpentry[] = $action;
			$response['aaData'][] = $tmpentry;
		}
		//exit;
	}
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
	header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
	header("Cache-Control: no-cache, must-revalidate" );
	header("Pragma: no-cache" );
	header("Content-type: text/x-json");
	echo json_encode($response);
	
	function fnColumnToField( $i )
	{
		if ( $i == 0 )
			return true;
		else if ( $i == 1 )
			return "user_type_name";
		else
			return true;
	}
?>