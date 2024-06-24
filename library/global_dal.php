<?php
	function last_instert_id()
	{
		global $conn;
		return $conn->lastInsertId();	
	}
	
	function get_in_query_string($in_string)
	{
		$type_arr = explode(',',$in_string);
		$cri = "";
		$types = array();
		for($i = 0; $i < count($type_arr); $i++)
		{
			$cri .= (($cri != "") ? "," : "" ) . ":str$i";
			$types["str$i"] = $type_arr[$i];
		}
		return array($cri, $types);
	}
	
	function execute_non_query($query, $param=array())		//return effected row counts on success query and false on fail query
	{
		global $conn;	
		$retbol = false;
		$result = $conn->prepare($query);
		if( !$result )
		{
			return FALSE;
		}
		
		if( count($param) > 0 )
			$retbol = $result->execute($param);
		else
			$retbol = $result->execute();
		if($retbol)
			return $result->rowCount();
		else
			return false;
	}
	
	function execute_scalar_query($query, $param=array())		//return true on success query and false on fail query
	{
		global $conn;
		$result=$conn->prepare($query);
		if(!$result)
		{
			$err_arr = $conn->errorInfo();
			die($err_arr[2] . " " . $query);
		}
		
		if(count($param)>0)
			$result->execute($param);
		else
			$result->execute();
			
		$result->bindColumn(1, $retvalue);
		$result->fetch();
		return $retvalue;
	}
		
	function execute_query($query, $param=array())	//return result on sucess query, die on fail query
	{
		global $conn;	
		$result=$conn->prepare($query);
		if(!$result)
		{
			/*$err_arr = $conn->errorInfo();
			die($err_arr[2] . " " . $query);*/
			return FALSE;
		}
		
		if(count($param)>0)
			$result->execute($param);
		else
			$result->execute();
		
		return $result;
	}
	
	function debugPDO($raw_sql, $parameters)
	{
		$keys = array();
		$values = $parameters;
		foreach ($parameters as $key => $value) 
		{
			// check if named parameters (':param') or anonymous parameters ('?') are used
			if (is_string($key)) 
			{
				if (substr($key, 0, 1) === ':')
					$keys[] = '/'.$key.'/';
				else
					$keys[] = '/:'.$key.'/';
			} 
			else 
			{
				$keys[] = '/[?]/';
			}
			// bring parameter into human-readable format
			if (is_string($value)) {
				$values[$key] = "'" . $value . "'";
			} elseif (is_array($value)) {
				$values[$key] = implode(',', $value);
			} elseif (is_null($value)) {
				$values[$key] = 'NULL';
			}
		}
		$raw_sql = preg_replace($keys, $values, $raw_sql, 1, $count);
		return $raw_sql;
	}
?>