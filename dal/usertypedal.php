<?php
class usertypedal
{
	function getmenu($parentid)
	{
		 $qry ="SELECT menu_id, menu_name,serial_no, Deriv1.Count 
		 FROM fss_tbl_menu a 
		LEFT OUTER JOIN (
			SELECT parent_id, COUNT(*) AS Count
			FROM fss_tbl_menu 
			GROUP BY parent_id
		) Deriv1 ON a.menu_id = Deriv1.parent_id WHERE a.parent_id= :parentid ";		
		$result= execute_query($qry, array('parentid'=>$parentid)) or die("getmenu query fail.");		
		return new readonlyresultset($result);
	}
	
	function getmenutree($nodeid, $menu_type)
	{
		$param=array('nodeid'=>$nodeid, 'nodeid2'=>$nodeid);
		
		$query = "SELECT menu_id,menu_name,parent_id,serial_no,menu_type,1 AS submenu 
		FROM fss_tbl_menu 
		WHERE parent_id=:nodeid $menu_type AND (menu_id IN (
		SELECT parent_id FROM fss_tbl_menu) OR serial_no<=100)
		UNION  
		SELECT menu_id,menu_name,parent_id,serial_no,menu_type,0 AS submenu 
		FROM fss_tbl_menu 
		WHERE parent_id=:nodeid2 $menu_type AND menu_id NOT IN (
		SELECT parent_id FROM fss_tbl_menu) AND serial_no>100 
		ORDER BY parent_id,serial_no";
		// echo debugPDO($query, $param);exit;
		$result=execute_query($query, $param) or die("getmenutree query fail.");
		return new readonlyresultset($result);
	}
		
	function getusermenu($usertypeid)
	{
		$query="SELECT * FROM fss_tbl_user_type_menu  WHERE user_type_id=:usertypeid";
		//echo DebugPDO($query, array('usertypeid' => $usertypeid));exit;
		$result = execute_query($query, array('usertypeid' => $usertypeid)) or die("getusermenu query fail.");
		$str = array();
		if( $result ->rowCount() > 0 )
		{
			while( $row = $result->fetch() )
			{
				$str[]=$row['menu_id'];
			}
		}
		return $str;
	}
	
	function saveusertype($usertypeinfo)
	{
		$user_type_name=$usertypeinfo->get_usertype_name();
		$is_root_admin=$usertypeinfo->get_is_root_admin();
		$query = "INSERT INTO fss_tbl_user_type(user_type_name, is_root_admin) VALUES(:user_type_name, :is_root_admin)";		
		//echo debugPDO($query, array('user_type_name'=>$user_type_name, 'is_root_admin'=>$is_root_admin));exit;
		$result= execute_query($query, array('user_type_name'=>$user_type_name, 'is_root_admin'=>$is_root_admin)) or die("saveusertype query fail.");
		
		if($result)
		{
			$user_type_id =last_instert_id();
			$filter = "user_type_id=$user_type_id";
			$table = 'user_type';
			$type = 'Insert';
			$new_field_arr = array('user_type_name' => $user_type_name, 'is_root_admin' => $is_root_admin);
			$eventlogbol = new eventlogbol();
			$description = $eventlogbol->get_event_description($type, $new_field_arr);
			$userid = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$userid = $_SESSION['YRDCFSH_LOGIN_ID'];
			
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($userid);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			if($eventlogbol->save_eventlog($eventloginfo)) 
				return $user_type_id;
		}
		else 
			return false;				
	}
	
	function get_all_usetype_list($DisplayStart, $DisplayLength, $SortingCols, $cri_str)
	{
		$query = "SELECT SQL_CALC_FOUND_ROWS ut.user_type_id, user_type_name  
		FROM fss_tbl_user_type ut 
		LEFT JOIN fss_tbl_user_type_department ud ON ud.user_type_id = ut.user_type_id 
		$cri_str GROUP BY ut.user_type_id, user_type_name";
		$query .= $SortingCols;
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		
		$result = execute_query($query, array()) or die('get_all_usetype_list query fail.');
		return new readonlyresultset($result);
	}
	
	function check_duplicate_usertype($user_type_name, $user_type_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_user_type WHERE user_type_name = :user_type_name AND user_type_id <> :user_type_id ";
		$param=array('user_type_name'=>$user_type_name,	'user_type_id'=>$user_type_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $param) or die('check_duplicate_usertype query fail.');
		return $result->rowCount();
	}
	
	function delete_user_type($user_type_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "user_type_id=$user_type_id";
		$table = 'user_type';
		$old_data = $eventlogbol->get_old_data($table, $filter);
		
		$query="DELETE FROM fss_tbl_user_type WHERE user_type_id = :user_type_id ";
		// echo debugPDO($query, array(':user_type_id' => $user_type_id));exit;
		if(execute_query($query, array(':user_type_id' => $user_type_id)))
		{				
			$type = 'Delete';				
			$eventlogbol = new eventlogbol();
			$description = $eventlogbol->get_event_description($type, $old_data);
			
			$userid = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$userid = $_SESSION['YRDCFSH_LOGIN_ID'];
			
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($userid);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
	}
	
	function selectusertypebyid($usertypeid)
	{
		$query="SELECT * FROM fss_tbl_user_type WHERE user_type_id = :usertypeid";
		$result = execute_query($query, array('usertypeid' => $usertypeid)) or die("selectusertypebyid query fail.");
		return new readonlyresultset($result);
	}	
	
	function select_all_usertype($cri_str)
	{
		$query="SELECT ut.user_type_id, user_type_name 
		FROM fss_tbl_user_type ut 
		LEFT JOIN fss_tbl_user_type_department ud ON ud.user_type_id = ut.user_type_id 
		$cri_str GROUP BY ut.user_type_id, user_type_name";
		// echo debugPDO($query, array());exit;
		$result = execute_query($query) or die("select_all_usertype query fail.");
		return new readonlyresultset($result);
	}
	
	function update_user_type($usertypeinfo)
	{
		$user_type_id = $usertypeinfo->get_usertype_id();
		$user_type_name = $usertypeinfo->get_usertype_name();
		$is_root_admin=$usertypeinfo->get_is_root_admin();
		
		$user_id = 0;
		if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
			$user_id = $_SESSION['YRDCFSH_LOGIN_ID'];
		
		$eventlogbol = new eventlogbol();
		$table = 'user_type';
		$filter = "user_type_id=$user_type_id";
		$type = 'Update';
		$old_data = $eventlogbol->get_old_data($table, $filter);
		$query = "UPDATE fss_tbl_user_type SET user_type_name=:user_type_name, is_root_admin=:is_root_admin 
		WHERE user_type_id=:user_type_id;";
		$param = array(':user_type_name' => $user_type_name, ':is_root_admin' => $is_root_admin, ':user_type_id' => $user_type_id);
		// echo debugPDO($query, $param);exit;
		if ( execute_query($query, $param) )
		{
			$new_field_arr = array('user_type_name'=>$user_type_name, 'is_root_admin'=>$is_root_admin);
			$description = $eventlogbol->get_event_description($type, $new_field_arr, $old_data);			
			if($description != '')
			{
				$eventloginfo = new eventloginfo();
				$eventloginfo->setaction_type($type);
				$eventloginfo->setuser_id($user_id);
				$eventloginfo->settable_name($table);
				$eventloginfo->setfilter($filter);
				$eventloginfo->setdescription('အသုံးပြုသူအမျိုးအစားအမှတ်='.clean($usertypeid.', '.$description));
				return $eventlogbol->save_eventlog($eventloginfo);
			}
			else
				return TRUE;
		}
		else
			return FALSE;
	}
	
	function delete_usertype_menu_byusertypeid($usertypeid)
	{
		$query = "DELETE FROM fss_tbl_user_type_menu WHERE user_type_id = :usertypeid ";
		$olddata_qry = "SELECT * FROM fss_tbl_user_type_menu WHERE user_type_id = :usertypeid";
		$result =  execute_query($olddata_qry, array(':usertypeid' => $usertypeid));
	
		$delete_menus = "menu_id = ";
		while($delete_menu = $result->fetch() )
			$delete_menus .= $delete_menu['menu_id'] . ', ';		
		$delete_menus_str = substr_replace($delete_menus, "", -2);
		
		$eventlogbol = new eventlogbol();
		$table = 'user_type_menu';
		$filter = "user_type_id = :usertypeid";		
		$type = 'Delete';
		//echo DebugPDO($query, array(':usertypeid' => $usertypeid));exit;
		
		if( execute_query($query, array(':usertypeid' => $usertypeid)) )
		{
			$description = $delete_menus_str;
			$eventloginfo = new eventloginfo();
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = $_SESSION['YRDCFSH_LOGIN_ID'];	

			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function insert_usertype_menu($usertypeinfo)
	{
		$usertype_id = $usertypeinfo->get_usertype_id();
		$menu_id = $usertypeinfo->get_menulist();
		foreach($menu_id as $val)
		{
			$query = "INSERT INTO fss_tbl_user_type_menu(user_type_id, menu_id) 
			VALUES(:usertype_id,:val)";
			$param = array(':usertype_id' => $usertype_id, ':val' => $val);
			$result = execute_query($query, $param);
		}
		if( $result )
		{
			$filter = "user_type_id = $usertype_id";			
			$table = 'user_type_menu';
			$type = 'Insert';
			$eventlogbol = new eventlogbol();
			$description = 'menu_id = '. implode(', ', $menu_id);
			
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = $_SESSION['YRDCFSH_LOGIN_ID'];	
			
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function delete_usertype_department_byusertypeid($usertypeid)
	{
		$query="DELETE FROM fss_tbl_user_type_department WHERE user_type_id = :usertypeid";		
		$olddata_qry = "SELECT * FROM  fss_tbl_user_type_department WHERE user_type_id = :usertypeid";
		$result =  execute_query($olddata_qry, array(':usertypeid' => $usertypeid));
		
		$delete_departmentid = "department_id = ";
		while($delete_department = $result->fetch() )
			$delete_departmentid .= $delete_department['department_id'] . ', ';		
		$delete_department_str = substr_replace($delete_departmentid, "", -2);
		
		$eventlogbol = new eventlogbol();
		$table = 'user_type_department';
		$filter = "user_type_id = :usertypeid";
		$type = 'Delete';
		//echo DebugPDO($query, array(':usertypeid' => $usertypeid));exit;
		if( execute_query($query, array(':usertypeid' => $usertypeid)) )
		{
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = $_SESSION['YRDCFSH_LOGIN_ID'];
				
			$description = $delete_department_str;
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function insert_usertype_department($usertypeinfo)
	{
		$usertype_id = $usertypeinfo->get_usertype_id();
		$department_id = $usertypeinfo->get_departmentlist();
		// echo $department_id;exit;
		foreach($department_id as $val)
		{
			$query = "INSERT INTO  fss_tbl_user_type_department(user_type_id, department_id) 
			VALUES(:usertype_id, :val);";
			$result = execute_query($query, array(':usertype_id' => $usertype_id, ':val' => $val));
			// echo debugPDO($query, array(':usertype_id' => $usertype_id, ':val' => $val));
		}
		// exit;
		
		if( $result )
		{
			$filter = "user_type_id = $usertype_id";
			$table = 'user_type_department';
			$type = 'Insert';
			$eventlogbol = new eventlogbol();
			$description = 'ဌာန အမှတ်= '. implode(', ', $department_id);
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id(0);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return  FALSE;
	}
	
	function delete_usertype_application_byusertypeid($usertypeid)
	{
		$query="DELETE FROM fss_tbl_user_type_application_type WHERE user_type_id = :usertypeid";		
		$olddata_qry = "SELECT * FROM  fss_tbl_user_type_application_type WHERE user_type_id = :usertypeid";
		$result =  execute_query($olddata_qry, array(':usertypeid' => $usertypeid));
		
		$delete_application_type_id = "application_type_id = ";
		while($delete_application_type = $result->fetch() )
			$delete_application_type_id .= $delete_application_type['application_type_id'] . ', ';		
		$delete_application_str = substr_replace($delete_application_type_id, "", -2);
		
		$eventlogbol = new eventlogbol();
		$table = 'user_type_application_type';
		$filter = "user_type_id = :usertypeid";
		$type = 'Delete';
		//echo DebugPDO($query, array(':usertypeid' => $usertypeid));exit;
		if( execute_query($query, array(':usertypeid' => $usertypeid)) )
		{
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = $_SESSION['YRDCFSH_LOGIN_ID'];
				
			$description = $delete_application_str;
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function insert_usertype_application($usertypeinfo)
	{
		$usertype_id = $usertypeinfo->get_usertype_id();
		$application_type_id = $usertypeinfo->get_applicationlist();
		foreach($application_type_id as $val)
		{
			$query = "INSERT INTO  fss_tbl_user_type_application_type(user_type_id, application_type_id) 
			VALUES(:usertype_id, :val);";
			$result = execute_query($query, array(':usertype_id' => $usertype_id, ':val' => $val));
		}
		
		if( $result )
		{
			$filter = "user_type_id = $usertype_id";
			$table = 'user_type_application_type';
			$type = 'Insert';
			$eventlogbol = new eventlogbol();
			$description = 'လုပ်ငန်းအမျိုးအစား အမှတ်= '. implode(', ', $application_type_id);
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id(0);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return  FALSE;
	}
	
	function delete_usertype_security_byusertypeid($usertypeid)
	{
		$query="DELETE FROM fss_tbl_user_type_security_type WHERE user_type_id = :usertypeid";		
		$olddata_qry = "SELECT * FROM  fss_tbl_user_type_security_type WHERE user_type_id = :usertypeid";
		$result =  execute_query($olddata_qry, array(':usertypeid' => $usertypeid));
		
		$delete_security_type_id = "security_type_id = ";
		while($delete_security_type = $result->fetch() )
			$delete_security_type_id .= $delete_security_type['security_type_id'] . ', ';		
		$delete_security_str = substr_replace($delete_security_type_id, "", -2);
		
		$eventlogbol = new eventlogbol();
		$table = 'user_type_security_type';
		$filter = "user_type_id = :usertypeid";
		$type = 'Delete';
		//echo DebugPDO($query, array(':usertypeid' => $usertypeid));exit;
		if( execute_query($query, array(':usertypeid' => $usertypeid)) )
		{
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = $_SESSION['YRDCFSH_LOGIN_ID'];
				
			$description = $delete_security_str;
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function insert_usertype_security($usertypeinfo)
	{
		$usertype_id = $usertypeinfo->get_usertype_id();
		$security_type_id = $usertypeinfo->get_securitylist();
		foreach($security_type_id as $val)
		{
			$query = "INSERT INTO  fss_tbl_user_type_security_type(user_type_id, security_type_id) 
			VALUES(:usertype_id, :val);";
			$result = execute_query($query, array(':usertype_id' => $usertype_id, ':val' => $val));
		}
		
		if( $result )
		{
			$filter = "user_type_id = $usertype_id";
			$table = 'user_type_security_type';
			$type = 'Insert';
			$eventlogbol = new eventlogbol();
			$description = 'လုံခြုံမှု့အဆင့်အတန်း အမှတ်= '. implode(', ', $security_type_id);
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id(0);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return  FALSE;
	}
	
	//end of update//	
	function get_header_menu($parentid,$usertypeid)
	{
		$str = '';
		$permission_str = '';
		$permission_cri = '';
		$param = array();
		if($parentid != 0)
			$str = 'AND serial_no < 100';
		if($usertypeid != 0)
		{
			$permission_str = "LEFT JOIN  fss_tbl_user_type_menu ut ON mu.menu_id=ut.menu_id";
			$permission_cri = "AND user_type_id= :usertypeid";
			$param['usertypeid'] = $usertypeid;
		}
		$query = "SELECT * FROM fss_tbl_menu mu $permission_str
		WHERE parent_id = :parentid $permission_cri $str ORDER BY serial_no";
		$param['parentid'] = $parentid;
		//echo debugPDO($query,$param);echo '<br/>';
		$result = execute_query($query, $param) or die("get_header_menu query fail.");
		return new readonlyresultset($result);
	}
	
	function check_existing_usertype($user_type_id)
	{
		$qry = "SELECT COUNT(1) FROM fss_tbl_user WHERE user_type_id=:user_type_id; ";
		$result=execute_scalar_query($qry,array('user_type_id' => $user_type_id)) or die("check_existing_usertype query fail.");
		if($result)
			return false;
		else 
			return true;
	}
}
?>