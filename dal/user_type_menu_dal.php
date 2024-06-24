<?php
	class user_type_menu_dal
	{
		function saveuser_type_menu($user_type_menu_info)
		{		
			$user_type_id=$user_type_menu_info->get_usertype_id();
			$menu_id=$user_type_menu_info->get_menu_id();			
			if ( $menu_id == '' )
				return TRUE;
				
			foreach ($menu_id as $val)
			{
				$new_field_arr = array('menu_id' => $val);
				$query = "INSERT INTO fss_tbl_user_type_menu(user_type_id,menu_id) VALUES(:user_type_id,:menu_id)";				
				//echo debugPDO($query, array(':user_type_id' => $user_type_id,':menu_id' => $val));echo '<br/>';
				$result=execute_query($query,array(':user_type_id' => $user_type_id,':menu_id' => $val)) or die("save_user_type_menu query fail.".$query);
				if($result)
				{
					$filter = "user_type_id=$user_type_id";
					$table = 'user_type_menu';
					$type = 'Insert';					
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
	}
?>