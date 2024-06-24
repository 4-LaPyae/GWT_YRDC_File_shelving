<?php
class userbol 
{
	function checkuserlogin($email, $password) 
	{
		$userdal = new userdal();
		return $userdal->checkuserlogin($email, $password);	
	}
	
	function selectuser($DisplayStart , $DisplayLength ,$SortingCols, $cri_arr) 
	{
		$userdal = new userdal();
		return $userdal ->selectuser ($DisplayStart , $DisplayLength ,$SortingCols, $cri_arr);		
	}
	
	function save_user($userinfo) 
	{
		$userdal = new userdal();
		if($userinfo->get_user_id())
			$result = $userdal->update_user($userinfo);
		else					
			$result = $userdal->insert_user($userinfo);
		return $result;		
	}
	
	function get_user_byid($id) 
	{
		$userdal = new userdal();
		$result = $userdal->selectuserbyid ($id);
		return $result->getNext();
	}
	
	function change_user_status($id, $status) 
	{
		$userdal = new userdal();
		return $userdal->change_user_status($id, $status);		
	}
	
	function check_oldpassword($pass) 
	{
		$userdal = new userdal();
		return $userdal->check_oldpassword ($pass);		
	}
	
	//change password by own user 
	function change_password($oldpass, $newpass) 
	{
		$userdal = new userdal();		
		return  $userdal->change_password($oldpass, $newpass);					
	}
	
	//change password by authenticate person
	function change_password_byforce($id, $newpass) 
	{
		$userdal = new userdal();
		return $userdal->change_password_byforce ($id, $newpass);					
	}
	
	function delete_user($userid) 
	{
		$userdal = new userdal();
		return $userdal->delete_user($userid);		
	}
	
	function get_duplicate_useremail_count($useremail, $userid = 0) 
	{
		$userdal = new userdal();
		if ($userdal->get_duplicate_useremail_count($useremail, $userid) > 0)
			return false;
		else
			return true;
	}
	
	function select_usertype()
	{
		$userdal = new userdal();
		return $result = $userdal->select_usertype();		
	}
	
	function select_usertype_by_id($id)
	{
		$userdal = new userdal();
		return $result = $userdal->select_usertype_by_id($id);		
	}
	
	function getMenuURL($userid,$usertypeid)
	{
		$userdal = new userdal();
		return $result = $userdal->getMenuURL($userid,$usertypeid);
	}
	
	public function getMenuEnable($usertypeid,$currentpagemenuid)
	{
		$userdal = new userdal();
		$pageenablearr = array();
		$result = $userdal->getMenuEnable($usertypeid,$currentpagemenuid);
		while($row = $result->getNext())
		{
			$pageenablearr[$row['menu_name']] = true;
		}
		return $pageenablearr;
	}
	
	function check_duplicate_username($username)
	{
		$userdal = new userdal();
		return $result = $userdal->check_duplicate_username($username);	
	}
	
	function is_used_in_table($table_name, $filter_str)
	{
		$userdal = new userdal();
		$select_result = $userdal->select_data_byfilter($table_name, $filter_str);
		if( $select_result->rowCount() )
			return TRUE;
		else
			return FALSE;
	}
	
	/* function update_logout_date()
	{
		$userdal = new userdal();
		return $userdal->update_logout_date();
	} */
}
?>