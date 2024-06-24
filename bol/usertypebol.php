<?php
	class usertypebol
	{	
		function getmenu($parentid)
		{
			$usertype=new usertypedal();
			$result=$usertype->getmenu($parentid);
			if($result)
				return $result;
			else 
				die("Error in Business Logic");
		}
		
		function getmenutree($nodeid, $menu_type)
		{
			$usertype=new usertypedal();
			$result=$usertype->getmenutree($nodeid, $menu_type);
			if($result)
				return $result;
			else 
				die("Error in Business Logic");
		}
		
		function getusermenu($usertypeid)
		{
			$usertype=new usertypedal();
			$result=$usertype->getusermenu($usertypeid);
			return $result;		
		}
		
		function saveusertype($usertypeinfo)
		{
			$usertypedal = new usertypedal();
			return $result = $usertypedal->saveusertype($usertypeinfo);			
		}
		
		function get_all_usetype_list($DisplayStart, $DisplayLength, $SortingCols, $cri_str)
		{
			$usertypedal = new usertypedal();
			return $result = $usertypedal->get_all_usetype_list($DisplayStart, $DisplayLength, $SortingCols, $cri_str);
		}
		
		function check_duplicate_usertype($usertypename, $user_type_id = 0)
		{
			$usertypedal = new usertypedal();
			return $result = $usertypedal->check_duplicate_usertype($usertypename, $user_type_id);	
		}
		
		function delete_user_type($usertypeid)
		{
			$usertypedal = new usertypedal();
			return $result = $usertypedal->delete_user_type($usertypeid);	
		}
		
		function selectusertypebyid($usertypeid)
		{
			$usertypedal = new usertypedal();
			return $result = $usertypedal->selectusertypebyid($usertypeid);	
		}

		function update_user_type($usertypeinfo)
		{
			// print_r($usertypeinfo);exit;
			$usertypedal = new usertypedal();			
			if($usertypeid = $usertypeinfo->get_usertype_id())
			{
				if ( $usertypedal->update_user_type($usertypeinfo) )
				{
					if( $usertypedal->delete_usertype_menu_byusertypeid($usertypeid) )
						$result = $usertypedal->insert_usertype_menu($usertypeinfo);
					
					if( $usertypedal->delete_usertype_department_byusertypeid($usertypeid) )
						$result = $usertypedal->insert_usertype_department($usertypeinfo);
					
					$is_root_admin = $usertypeinfo->get_is_root_admin();
					if($is_root_admin == 2 || $is_root_admin == 3)
					{
						if( $usertypedal->delete_usertype_application_byusertypeid($usertypeid) )
							$result = $usertypedal->insert_usertype_application($usertypeinfo);
						if( $usertypedal->delete_usertype_security_byusertypeid($usertypeid) )
							$result = $usertypedal->insert_usertype_security($usertypeinfo);
					}
				}
			}
		}
	
		function get_header_menu($parentid,$usertypeid)
		{
			$usertypedal = new usertypedal();
			$result = $usertypedal->get_header_menu($parentid,$usertypeid);
			return $result;
		}	

		function check_existing_usertype($user_type_id) 
		{
			$usertypedal = new usertypedal();
			return $result = $usertypedal->check_existing_usertype($user_type_id);			
		}
		
		/* For department list */
		function getdepartmenttree($nodeid)
		{
			$usertype=new usertypedal();
			$result=$usertype->getdepartmenttree($nodeid);
			if($result)
				return $result;
			else 
				die("Error in Business Logic");
		}
		
		function getuserdepartment($usertypeid)
		{
			$usertype=new usertypedal();
			$result=$usertype->getuserdepartment($usertypeid);
			return $result;		
		}
		
		function select_all_usertype($cri_str)
		{
			$usertype=new usertypedal();
			$result=$usertype->select_all_usertype($cri_str);
			return $result;		
		}
	}
?>