<?php
	class eventlogdal
	{
		function save_eventlog($eventloginfo)
		{	
			$user_id = $eventloginfo->getuser_id();
			$action_type = $eventloginfo->getaction_type();
			$table_name = $eventloginfo->gettable_name();
			$table_name = "fss_tbl_$table_name";
			$filter = $eventloginfo->getfilter();
			$description = $eventloginfo->getdescription();
			$encrypt_value = $eventloginfo->getencrypt_value();
			$ip_address = getenv('REMOTE_ADDR');
			$param=array(
			'user_id'=>$user_id,
			'action_type'=>$action_type,
			'table_name'=>$table_name,
			'filter'=>$filter,
			'description'=>$description,
			'encrypt_value'=>$encrypt_value,
			'ip_address'=>$ip_address);
			
			$query = "INSERT INTO fss_tbl_eventlog(user_id, action_date, action_type, table_name, filter, description, encrypt_value, ip_address) 
						VALUES (:user_id, NOW(), :action_type, :table_name, :filter, :description, :encrypt_value, :ip_address)";				
			//echo debugPDO($query ,$param);
			$result=execute_query($query,$param) or die ("save_eventlog query fail.");
			return $result;
		}
		
		function get_old_data($table, $cri_str, $param)
		{			
			$query = "SELECT * FROM fss_tbl_$table WHERE $cri_str";	
			//echo debugPDO($query,$param);exit;
			$result = execute_query($query, $param) or die ("get_old_data query fail.");
			return $result->fetch(PDO::FETCH_ASSOC);
		}
		
		function get_all_old_data($table, $cri_str)
		{
			$qry = "SELECT * FROM fss_tbl_$table WHERE $cri_str";
			$result =  execute_query($qry) or die("get_old_data query fail.");
			$old_data = "";
			$num_fields =$result->columnCount();
			while($row = $result->fetch())
			{
				for($i=0; $i<$num_fields; $i++)
				{
					$old_data .= $result->getColumnMeta($i). "=>" . $row[$i] . ", ";
				}
			}
			return $old_data;
		}
		
		//select all eventlog//
		function select_all_event_log($DisplayStart, $DisplayLength,$SortingCols, $cri_arr) 
		{		
			$cri_str = $cri_arr[0];
			$param = $cri_arr[1];
			$query = "SELECT SQL_CALC_FOUND_ROWS * 
			FROM fss_tbl_eventlog as el
			LEFT JOIN fss_tbl_user as u ON u.user_id = el.user_id ";
			$query .= $cri_str;
			$query .= $SortingCols;		
			if($DisplayLength != 0)
				$query .= " LIMIT $DisplayStart, $DisplayLength";
			//echo debugPDO($query,$param);exit;
			$result = execute_query($query,$param)  or die("select_all_event_log query fail.");
			return new readonlyresultset ($result);
		}
		
		function select_description_by_id($id)
		{
			$query="SELECT description FROM fss_tbl_eventlog WHERE id=:id;"; 
			$result=execute_query($query,array("id"=>$id)) or die("select_description_by_id query fail.");
			return new readonlyresultset($result);
		}
	}
?>