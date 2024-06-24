<?php
	class user_type_security_type_dal
	{
		function saveuser_type_security($user_type_security_type_info)
		{
			$user_type_id = $user_type_security_type_info->get_usertype_id();
			$security_type_id = $user_type_security_type_info->get_security_type_id();
			$result =false;
			
			foreach($security_type_id as $value)
			{	
				$new_field_arr = array('security_type_id' => $value);
				$param = array(':user_type_id' => $user_type_id, ':security_type_id' => $value);	
				$query = "INSERT INTO fss_tbl_user_type_security_type(user_type_id, security_type_id) 
				VALUES (:user_type_id, :security_type_id);";				
			
				if( execute_query($query, $param))
				{
					$filter = "user_type_id=:user_type_id";
					$table = 'user_type_security_type';
					$type = 'INSERT';
					
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
					$result= $eventlogbol->save_eventlog($eventloginfo);		
				}
			}
			if($result)
				return true;
			else 
				return  false;
		}
		
		function select_security_by_usertype($usertype_id)
		{
			$query = "SELECT * FROM fss_tbl_user_type_security_type WHERE user_type_id = :usertype_id ";
			$param = array(':usertype_id'=>$usertype_id);
			$result = execute_query($query, $param) or die('select_security_by_usertype query fail.');
			return new readonlyresultset($result);
		}
	}
?>