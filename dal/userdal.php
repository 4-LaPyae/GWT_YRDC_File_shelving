<?php
class userdal
{
	function checkuserlogin($email, $password)
	{
		$password = encryptNET3DES(YRDCFSH_ENCRYPTION_KEY, YRDCFSH_ENCRYPTION_IV, $password);
		//echo $password;exit;
		$query = "SELECT u.*, is_root_admin  
		FROM fss_tbl_user u 
		LEFT JOIN fss_tbl_user_type ut ON ut.user_type_id = u.user_type_id  
		WHERE user_email=:user_email AND password=:password";
		// echo debugPDO($query, array(':user_email'=>$email, ':password'=>$password));exit;
		$result = execute_query ($query, array(':user_email'=>$email, ':password'=>$password) ) or die("checkuserlogin query fail.");
		return $result->fetch(PDO::FETCH_ASSOC);
	}
	
	function selectuser($DisplayStart, $DisplayLength,$SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		
		$query = "SELECT SQL_CALC_FOUND_ROWS u.user_id, u.user_name, u.user_email, u.user_type_id, td.user_type_name, is_active  
		FROM fss_tbl_user u
		LEFT JOIN (
			SELECT ut.user_type_id, user_type_name, department_id, is_root_admin 
			FROM fss_tbl_user_type ut
			LEFT JOIN fss_tbl_user_type_department ud ON ud.user_type_id = ut.user_type_id 
		) td ON td.user_type_id = u.user_type_id  ";
		$query .= $cri_str.' GROUP BY u.user_id, u.user_name, u.user_email, u.user_type_id, td.user_type_name, is_active ';
		$query .= $SortingCols;
		// echo debugPDO($query, $param);exit;
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		$result = execute_query($query,$param) or die("selectuser query fail.");
		return new readonlyresultset($result);
	}
		
	function insert_user($userinfo)
	{
		$user_name = $userinfo->get_user_name();
		$user_email = $userinfo->get_user_email();
		$user_password = $userinfo->get_password();
		$password = encryptNET3DES(YRDCFSH_ENCRYPTION_KEY, YRDCFSH_ENCRYPTION_IV, $user_password);
		$user_type_id = $userinfo->get_user_type_id();
		
		$query = "INSERT INTO fss_tbl_user (user_name, user_email, password, user_type_id, is_active, modified_date) 
		VALUES (:user_name, :user_email, :password, :user_type_id, 1, now());";
		$param=array(':user_name'=>$user_name, ':user_email'=>$user_email,':password'=>$password, ':user_type_id'=>$user_type_id);
			
		//echo debugPDO($query,$param);exit;
		if( execute_non_query($query,$param) )
		{
			$user_id =  last_instert_id();
			$filter = "user_id=$user_id";
			$table = "user";
			$type = "Insert";
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "user_id=:user_id", array(":user_id"=>$user_id));
	
			$new_field_arr =  array('user_name'=>$user_name, 'user_email'=>$user_email, 'password'=>$password, 'user_type_id'=>$user_type_id, 'is_active'=>1);
			$eventlogbol = new eventlogbol();
			$description = $eventlogbol->get_event_description($type, $new_field_arr);

			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = clean($_SESSION['YRDCFSH_LOGIN_ID']);
			
			$eventloginfo = new eventloginfo();
			$eventloginfo->setuser_id($user_id);			
			$eventloginfo->setaction_type($type);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter(clean_jscode($filter));
			$eventloginfo->setdescription(clean_jscode($description));
			$eventloginfo->setencrypt_value($encrypt_value);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function selectuserbyid($id) 
	{
		$query = "SELECT * FROM fss_tbl_user WHERE user_id=:id ;";
		$result = execute_query($query,array('id'=>$id)) or die('selectuserbyid query fail.');
		return new readonlyresultset ($result);
	}	
	
	function update_user($userinfo)
	{
		$user_id = $userinfo->get_user_id();
		$user_name = $userinfo->get_user_name();
		$user_email = $userinfo->get_user_email();
		$user_type_id = $userinfo->get_user_type_id();		
		
		$eventlogbol = new eventlogbol();
		$table = "user";
		$filter = "user_id=:user_id";
		
		$old_data = $eventlogbol->get_old_data($table, "user_id=:user_id", array(":user_id"=>$user_id));
		unset($old_data['encrypt_value']);
		
		$query = "UPDATE fss_tbl_user SET user_name=:user_name, user_email=:user_email, user_type_id=:user_type_id, modified_date=now() 
		WHERE user_id=:user_id ; ";
		
		$param = array(':user_id'=>$user_id, ':user_name'=>$user_name, ':user_email'=>$user_email, ':user_type_id'=>$user_type_id);
		//echo debugPDO($query, $param);exit;
		if( execute_query($query, $param) )
		{
			$securitybol = new securitybol();
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "user_id=:user_id", array(':user_id'=>$user_id));
			
			$type = 'Update';
			$new_field_arr = array('user_id' => $user_id, 'user_name'=>$user_name, 'user_email'=>$user_email, 'user_type_id' => $user_type_id);
			$description = $eventlogbol->get_event_description($type, $new_field_arr, $old_data);
			if($description == '')
				return TRUE;
				
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = clean_jscode($_SESSION['YRDCFSH_LOGIN_ID']);
				
			$eventloginfo = new eventloginfo();
			$eventloginfo->setuser_id($user_id);		
			$eventloginfo->setaction_type($type);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter(clean_jscode($filter));
			$eventloginfo->setdescription(clean_jscode($description));
			$eventloginfo->setencrypt_value($encrypt_value);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function change_user_status($user_id, $status) 
	{
		$eventlogbol = new eventlogbol();
		$table = 'user';
		$filter = "user_id=:user_id";
		$old_data = $eventlogbol->get_old_data($table, "user_id=:user_id", array(':user_id'=>$user_id));
		$encrypt_value = '';
		$param=array(	'status'=>$status, 'user_id'=>$user_id);
		$query = "UPDATE fss_tbl_user SET is_active=:status WHERE user_id=:user_id ; ";		
		
		if( execute_query($query, array(':status'=>$status, ':user_id'=>$user_id)) )
		{
			$type = 'Update';
			$new_field_arr = array('user_id'=>$user_id, 'is_active'=>$status);
			
			$login_user_id = $user_id;
			if(isset($_SESSION['YRDCFSH_LOGIN_ID']))
				$login_user_id = $_SESSION['YRDCFSH_LOGIN_ID'];
				
			$encrypt_value = '';
			$securitybol = new securitybol();
			if( $status != 2 )						
				$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "user_id=:user_id", array(':user_id'=>$user_id));
				
			$type = 'Update';
			$new_field_arr = array('user_id'=>$user_id, 'is_active'=>$status);
			$description = $eventlogbol->get_event_description($type, $new_field_arr, $old_data);
			if( $description == '' )
				return TRUE;
			
			$eventloginfo = new eventloginfo();
			$eventloginfo->setuser_id(clean_jscode($_SESSION['YRDCFSH_LOGIN_ID']));		
			$eventloginfo->setaction_type($type);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter(clean_jscode($filter));
			$eventloginfo->setdescription(clean_jscode($description));
			$eventloginfo->setencrypt_value($encrypt_value);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function check_oldpassword($postoldpassword) 
	{
		$postuserid = 0;
		if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
			$postuserid = clean($_SESSION['YRDCFSH_LOGIN_ID']);

		$postoldpassword = encryptNET3DES(YRDCFSH_ENCRYPTION_KEY, YRDCFSH_ENCRYPTION_IV, $postoldpassword);
		$qry = "SELECT * FROM fss_tbl_user WHERE user_id=:postuserid AND password=:postoldpassword ";
		//echo debugPDO($qry, array(':postuserid'=>$postuserid, ':postoldpassword'=>$postoldpassword));exit;
		$result = execute_query($qry, array(':postuserid'=>$postuserid, ':postoldpassword'=>$postoldpassword));
		return ( $result->rowCount() > 0 ) ? TRUE:FALSE;
	}
	
	function change_password($postoldpassword, $postpassword) 
	{
		$postuserid = 0;
		if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
			$postuserid = clean($_SESSION['YRDCFSH_LOGIN_ID']);

		$postpassword = encryptNET3DES(YRDCFSH_ENCRYPTION_KEY, YRDCFSH_ENCRYPTION_IV, $postpassword);
		$eventlogbol = new eventlogbol();
		$table = 'user';
		$filter = "user_id=$postuserid";
		$old_data = $eventlogbol->get_old_data($table, "user_id=:postuserid", array(':postuserid'=>$postuserid));

		$qry = "UPDATE fss_tbl_user SET password=:postpassword, require_changepassword=0, modified_date=now() WHERE user_id=:postuserid;";
		if( execute_non_query($qry, array(':postpassword'=>$postpassword, ':postuserid'=>$postuserid)) )
		{
			$securitybol = new securitybol();
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "user_id=:postuserid", array(':postuserid'=>$postuserid));
			
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('user_id'=>$postuserid, 'password'=>$postpassword);
			$description = $eventlogbol->get_event_description($type, $new_field_arr, $old_data);
			if( $description == '' )
				return TRUE;
				
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = clean($_SESSION['YRDCFSH_LOGIN_ID']);
				
			$eventloginfo = new eventloginfo();
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->setaction_type($type);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			$eventloginfo->setencrypt_value($encrypt_value);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function change_password_byforce($id, $newpass)
	{
		$newpass = encryptNET3DES(YRDCFSH_ENCRYPTION_KEY, YRDCFSH_ENCRYPTION_IV, $newpass);
		$eventlogbol = new eventlogbol();
		$table = 'user';
		$filter = "user_id=$id";
		
		$old_data = $eventlogbol->get_old_data($table, "user_id=:id", array(':id'=>$id));
		$qry = "UPDATE fss_tbl_user SET password=:newpass, require_changepassword=1, modified_date=now() WHERE user_id=:id ";
		
		if( execute_query($qry, array(':newpass'=>$newpass, ':id'=>$id)) )
		{
			$securitybol = new securitybol();
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "user_id=:id", array(':id'=>$id));
			
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('user_id'=>$id, 'password'=>$newpass);
			$description = $eventlogbol->get_event_description($type, $new_field_arr, $old_data);
			if( $description == '' )
				return TRUE;
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = clean($_SESSION['YRDCFSH_LOGIN_ID']);
				
			$eventloginfo = new eventloginfo();
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->setaction_type($type);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			$eventloginfo->setencrypt_value($encrypt_value);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function delete_user($user_id) 
	{
		$eventlogbol = new eventlogbol();
		$filter = "user_id=:user_id";
		$table = 'user';
		$old_data = $eventlogbol->get_old_data($table, "user_id=:user_id", array(':user_id'=>$user_id));
		$encrypt_value = $old_data['encrypt_value'];
		unset($old_data['encrypt_value']);
		
		$qry = "DELETE FROM fss_tbl_user WHERE user_id=:user_id";
		//echo debugPDO($qry, array(':user_id'=>$user_id) );exit;
		if( execute_query($qry, array(':user_id'=>$user_id) ) )
		{
			$type = 'Delete';
			$eventlogbol = new eventlogbol();
			$description = $eventlogbol->get_event_description($type, $old_data);
			
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = clean($_SESSION['YRDCFSH_LOGIN_ID']);

			$eventloginfo = new eventloginfo();
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->setaction_type($type);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter(clean($filter));
			$eventloginfo->setdescription(clean($description));
			$eventloginfo->setencrypt_value($encrypt_value);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
	}
	
	function get_duplicate_useremail_count($useremail, $user_id) 
	{
		$qry = "SELECT * FROM fss_tbl_user 
		WHERE user_email=:useremail AND user_id<> :user_id; ";		
		//echo debugPDO($qry, array(':useremail' => $useremail, ':user_id'=>$user_id));exit;
		$result = execute_query($qry,  array(':useremail' => $useremail, ':user_id'=>$user_id)) or die("get_duplicate_email_count query fail.");
		if($result->rowCount() > 0)
			return TRUE;
		else 
			return FALSE;
	}	
	
	function select_usertype()
	{
		$query = "SELECT * FROM fss_tbl_user_type; ";
		$result = execute_query($query, array())or die("select_usertype query fail.");
		return new readonlyresultset($result);		
	}
	
	function select_usertype_by_id($id)
	{
		$query = "SELECT user_type_name FROM fss_tbl_user_type WHERE user_id = :id; ";		
		$result = execute_query($query, array('id'=>$id)) or die ("select_usertype_by_id query fail");
		return new readonlyresultset ($result);		
	}
	
	function getMenuURL($user_id,$usertypeid)
	{					
		$query = "SELECT user_type_id, ut.menu_id, menurl.url 
		FROM fss_tbl_user_type_menu AS ut
		INNER JOIN  fss_tbl_menu_url as menurl ON ut.menu_id=menurl.menu_id
		WHERE user_type_id=:usertypeid";

		//echo debugPDO($query,array('usertypeid'=>$usertypeid));
		$result = execute_query($query,array('usertypeid'=>$usertypeid)) or die("getMenuURL query fail.");	
		return new readonlyresultset($result);
	}
	
	function getMenuEnable($usertypeid,$currentpagemenuid)
	{
		$query = "SELECT menu_name 
		FROM fss_tbl_user_type_menu as ut
		inner join fss_tbl_menu as mu on ut.menu_id=mu.menu_id
		where user_type_id=:usertypeid and mu.parent_id in ($currentpagemenuid);";
		//echo DebugPDO($query, array('usertypeid'=>$usertypeid));exit;
		$result = execute_query($query,array('usertypeid'=>$usertypeid)) or die("getMenuEnable query fail.");	
		$result_obj=new readonlyresultset($result);
		if ($result_obj->rowCount() >0 )
		{
			return $result_obj;
		}
		else
		{
			$query = "SELECT menu_name 
			FROM fss_tbl_user_type_menu as ut
			inner join fss_tbl_menu as mu on ut.menu_id=mu.menu_id
			where user_type_id=:usertypeid and mu.parent_id in (select parent_id from fss_tbl_menu where menu_id=:currentpagemenuid);";
			
			$result = execute_query($query,array('usertypeid'=>$usertypeid,'currentpagemenuid'=>$currentpagemenuid)) or die("getMenuEnable 1 query fail.");
			return new readonlyresultset($result);
		}
	}
		
	function check_duplicate_username($username)
	{
		$query = "SELECT * FROM fss_tbl_user WHERE user_name = :username";// AND user_type_id <> '$user_type_id'";
		$result = execute_query($query, array(':username' => $username)) or die('check_duplicate_user query fail.');
		$result_obj =  new readonlyresultset($result);
		if( $result_obj->rowCount() >0 )
			return TRUE;
		else
			return FALSE;
	}
	
	function select_data_byfilter($table_name, $filter_str = ' 1=1 ')
	{
		$query = "SELECT * FROM fss_tbl_$table_name WHERE $filter_str ";
		$result = execute_query($query) or die('select_data_byfilter query fail');
		return new readonlyresultset($result);
	}
	
	/* function update_logout_date()
	{
		$user_id = $_SESSION['YRDCFSH_LOGIN_ID'];
		$query = "UPDATE fss_tbl_user SET logout_date=now() WHERE user_id=:user_id";
		//return execute_non_query($query, array('user_id'=>$user_id));
		$table = 'user';
		if( execute_non_query($query, array('user_id'=>$user_id)) )
		{
			$securitybol = new securitybol();
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "user_id=:id", array(':id'=>$user_id));
			return True;
		}
		else
			return FALSE;
	} */
}
?>