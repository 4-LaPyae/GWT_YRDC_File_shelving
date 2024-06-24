<?php
	class eventloginfo
	{
		private $id;
		private $user_id;
		private $action_date;
		private $action_type;
		private $table_name;
		private $filter;
		private $description;
		private $encrypt_value;
		private $ip_address;
		
		public function setid($value)
		{
			$this->id = $value;
		}
		public function getid()
		{
			return $this->id;
		}
		
		public function setuser_id($value)
		{
			$this->user_id = $value;
		}
		public function getuser_id()
		{
			return $this->user_id;
		}
		
		public function setaction_date($value)
		{
			$this->action_date = $value;
		}
		public function getaction_date()
		{
			return $this->action_date;
		}
		
		public function setaction_type($value)
		{
			$this->action_type = $value;
		}
		public function getaction_type()
		{
			return $this->action_type;
		}
		
		public function settable_name($value)
		{
			$this->table_name = $value;
		}
		public function gettable_name()
		{
			return $this->table_name;
		}
		
		public function setfilter($value)
		{
			$this->filter = $value;
		}
		public function getfilter()
		{
			return $this->filter;
		}
		
		public function setdescription($value)
		{
			$this->description = $value;
		}
		public function getdescription()
		{
			return $this->description;
		}
		
		public function setencrypt_value($value)
		{
			$this->encrypt_value = $value;
		}
		public function getencrypt_value()
		{
			return $this->encrypt_value;
		}
		
		public function setip_address($value)
		{
			$this->ip_address = $value;
		}
		public function getip_address()
		{
			return $this->ip_address;
		}
	}
?>