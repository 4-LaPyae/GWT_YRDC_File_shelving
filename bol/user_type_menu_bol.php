<?php
	class user_type_menu_bol
	{
		function saveuser_type_menu($user_type_menu_info)
		{
			$user_type_menu_dal = new user_type_menu_dal();
			return $result = $user_type_menu_dal->saveuser_type_menu($user_type_menu_info);			
		}
	}
?>