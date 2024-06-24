<?php 
	class user_type_application_type_info
	{
		private $user_type_id;
		private $application_type_id;
		
		public function set_usertype_id($value)
		{
			$this->usertype_id = $value;
		}
		public function get_usertype_id()
		{
			return $this->usertype_id;
		}
		
		public function set_application_type_id($value)
		{
			$this->application_type_id = $value;
		}
		public function get_application_type_id()
		{
			return $this->application_type_id;
		}
	}
?>