<?php 
	class user_type_security_type_info
	{
		private $user_type_id;
		private $security_type_id;
		
		public function set_usertype_id($value)
		{
			$this->usertype_id = $value;
		}
		public function get_usertype_id()
		{
			return $this->usertype_id;
		}
		
		public function set_security_type_id($value)
		{
			$this->security_type_id = $value;
		}
		public function get_security_type_id()
		{
			return $this->security_type_id;
		}
	}
?>