<?php
	class user_type_security_type_bol
	{
		function saveuser_type_security($user_type_security_type_info)
		{
			$user_type_security_type_dal = new user_type_security_type_dal();
			return $user_type_security_type_dal->saveuser_type_security($user_type_security_type_info);
		}
		
		function select_security_by_usertype($usertype_id)
		{
			$user_type_security_type_dal = new user_type_security_type_dal();
			return $user_type_security_type_dal->select_security_by_usertype($usertype_id);
		}
		
		function select_security_type_enables($usertype_id)
		{
			$user_type_security_type_dal = new user_type_security_type_dal();
			$result = $user_type_security_type_dal->select_security_by_usertype($usertype_id);
			
			if($result->rowCount() > 0)
			{
				while($row = $result->getNext())
				{
					$security_arr[] = $row['security_type_id'];
				}
				return $security_enables = implode($security_arr, ',');
			}
			else 
				return $security_enables ='';
		}
	}
?>