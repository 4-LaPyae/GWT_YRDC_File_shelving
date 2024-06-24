<?php 
	class user_type_menu_info
	{
		private $user_type_id;
		private $menu_id;
		
		public function set_usertype_id($value)
		{
			$this->usertype_id = $value;
		}
		public function get_usertype_id()
		{
			return $this->usertype_id;
		}
		
		public function set_menu_id($value)
		{
			$this->menu_id = $value;
		}
		public function get_menu_id()
		{
			return $this->menu_id;
		}
	}
?>