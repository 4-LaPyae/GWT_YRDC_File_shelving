<?php 
	class user_type_department_info
	{
		private $user_type_id;
		private $department_id;
		
		public function set_usertype_id($value)
		{
			$this->usertype_id = $value;
		}
		public function get_usertype_id()
		{
			return $this->usertype_id;
		}
		
		public function set_department_id($value)
		{
			$this->department_id = $value;
		}
		public function get_department_id()
		{
			return $this->department_id;
		}
	}
?>