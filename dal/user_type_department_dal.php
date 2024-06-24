<?php
	class user_type_department_dal
	{
		function saveuser_type_department($user_type_department_info)
		{
			$user_type_id = $user_type_department_info->get_usertype_id();
			$department_id = $user_type_department_info->get_department_id();
			$result =false;
			
			foreach($department_id as $value)
			{	
				$new_field_arr = array('department_id' => $value);
				$param = array(':user_type_id' => $user_type_id, ':department_id' => $value);	
				$query = "INSERT INTO fss_tbl_user_type_department(user_type_id, department_id) VALUES (:user_type_id, :department_id);";				
			
				if( execute_query($query, $param))
				{
					$filter = "user_type_id=:user_type_id";
					$table = 'user_type_department';
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
		
		function select_department_by_usertype($usertype_id)
		{
			$query = "SELECT * FROM fss_tbl_user_type_department ud 
			LEFT JOIN fss_tbl_department d ON d.department_id = ud.department_id 
			WHERE user_type_id = :usertype_id AND is_external <> 1 ";
			$param = array(':usertype_id'=>$usertype_id);
			// echo debugPDO($query, $param);exit;
			$result = execute_query($query, $param) or die('select_department_by_usertype query fail.');
			return new readonlyresultset($result);
		}
	}
?>