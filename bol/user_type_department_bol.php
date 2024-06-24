<?php
	class user_type_department_bol
	{
		function saveuser_type_department($user_type_department_info)
		{
			$user_type_department_dal = new user_type_department_dal();
			return $user_type_department_dal->saveuser_type_department($user_type_department_info);
		}
		
		function select_department_by_usertype($usertype_id)
		{
			$user_type_department_dal = new user_type_department_dal();
			return $user_type_department_dal->select_department_by_usertype($usertype_id);
		}
		
		function select_department_enables($usertype_id)
		{		
			$user_type_department_dal = new user_type_department_dal();
			$result = $user_type_department_dal->select_department_by_usertype($usertype_id);
			
			if($result->rowCount() > 0)
			{
				while($row = $result->getNext())
				{
					$departmentarr[] = $row['department_id'];
				}
				return $department_enables = implode($departmentarr, ',');
			}
			else 
				return $department_enables ='';
		}
	}
?>