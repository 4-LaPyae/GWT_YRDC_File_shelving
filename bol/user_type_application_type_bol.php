<?php
	class user_type_application_type_bol
	{
		function saveuser_type_application($user_type_application_type_info)
		{
			$user_type_application_type_dal = new user_type_application_type_dal();
			return $user_type_application_type_dal->saveuser_type_application($user_type_application_type_info);
		}
		
		function select_application_by_usertype($usertype_id)
		{
			$user_type_application_type_dal = new user_type_application_type_dal();
			return $user_type_application_type_dal->select_application_by_usertype($usertype_id);
		}
		
		function select_application_type_enables($usertype_id)
		{
			$user_type_application_type_dal = new user_type_application_type_dal();
			$result = $user_type_application_type_dal->select_application_by_usertype($usertype_id);
			
			if($result->rowCount() > 0)
			{
				while($row = $result->getNext())
				{
					$app_arr[] = $row['application_type_id'];
				}
				return $application_enables = implode($app_arr, ',');
			}
			else 
				return $application_enables ='';
		}
	}
?>