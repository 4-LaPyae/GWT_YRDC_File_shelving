<?php
	class eventlogbol
	{
		function save_eventlog($eventloginfo)
		{			
			$eventlogdal = new eventlogdal();
			return $eventlogdal->save_eventlog($eventloginfo);
		}		
		
		function get_event_description($type, $new_field_arr, $old_data = array())
		{
			$column_arr = array
										(
											"user_id"  => " အသုံးပြုသူ အမှတ်",
											"user_name"  => " အသုံးပြုသူ အမည်",
											"user_email"  => "အီးမေးလ်",
											"password"  => " လျှို့ဝှက်နံပါတ်",
											"user_type_id"  => " အသုံးပြုသူ အမျိုးအစား",
											"is_active"  => " အသုံးပြုခွင့်အခြေအနေ",
											"require_changepassword"  => "လိုအပ်သည့်လျှို့ဝှက်နံပါတ်",
											
											"user_type_name"  => " အသုံးပြုသူ အမျိုးအစား အမည်",
											"is_root_admin"  => " Super Admin",
											"menu_id"  => " မာတိကာ အမှတ်",
											
											"application_type_id"  => " လုပ်ငန်းအမျိုးအစား အမှတ်",
											"application_type_code"  => " လုပ်ငန်းအမျိုးအစား ကုတ်နံပါတ်",
											"application_type_name"  => " လုပ်ငန်းအမျိုးအစား အမည်",
											
											"customer_id"  => " Customer အမှတ်",
											"customer_name"  => " Customer အမည်",
											"nrc_division_code"=>"ပြည်နယ်/တိုင်း ကုတ်နံပါတ်",
											"nrc_township_code"=>"မြို.နယ် ကုတ်နံပါတ်",
											"nrc_citizen_type"=>"နိုင်ငံသားအမျိုးအစား",
											"nrc_number"=>"နိုင်ငံသားစိစစ်ရေးကတ်ပြားအမှတ်",
											"nrc_text"=>"အမျိုးသားမှတ်ပုံတင်အမှတ်",
											"passport"=>"ပတ်စပို့ နံပါတ်",
											"father_name"=>"အဖ အမည်",
											"date_of_birth"  => " မွေးသက္ကရာဇ်",
											"street"  => " လမ်း အမည်",
											"house_no"  => " အိမ်နံပါတ်",
											
											"file_id"  => " ဖိုင် အမှတ်စဉ်",
											"letter_no"  => " စာအမှတ်",
											"letter_count"  => " စာရွက် အရေအတွက်",
											"letter_date"  => " စာရက်စွဲ",
											"description"  => " အကြောင်းအရာ",
											"to_do"  => " လုပ်ဆောင်ရန်",
											"remark"  => " မှတ်ချက်",
											"from_department_type"  => " ပေးပို့သည့်ဌာန အမျိုးအစား",
											"from_department_id"  => " ပေးပို့သည့်ဌာန အမှတ်",
											"to_department_type"  => " ဖြန့်၀ေသည့်ဌာန အမျိုးအစား",
											"to_department_id"  => " ဖြန့်၀ေသည့်ဌာန အမှတ်",
											"application_description"  => " လုပ်ငန်းအမျိုးအစား အကြောင်းအရာ",
											"application_references"  => " လုပ်ငန်း ဖော်ြပချက်",
											"receiver_customer_id"  => " ပေးပို့သူ အမည်",
											"sender_customer_id"  => " လက်ခံသူ အမည်",
											"destroy_date"  => " ဖျက်ဆီးသည့် အမှတ်",
											"destroy_order_no"  => " ဖျက်ဆီးသည့် အမှတ်စဉ် နံပါတ်",
											"destroy_remark"  => " ဖျက်ဆီးသည့် မှတ်ချက်",
											"destroy_order_employeeid"  => " ဖျက်ဆီးရန် အမိန့်ပေးသည့် ၀န်ထမ်း အမှတ်စဉ်",
											"destroy_order_employee_name"  => " ဖျက်ဆီးရန် အမိန့်ပေးသည့် ၀န်ထမ်း အမည်",
											"destroy_order_designation"  => " ဖျက်ဆီးရန် အမိန့်ပေးသည့် ၀န်ထမ်း၏ရာထူး",
											"destroy_order_department"  => " ဖျက်ဆီးရန် အမိန့်ပေးသည့် ၀န်ထမ်း၏ဌာန",
											"destroy_duty_employeeid"  => " ဖျက်ဆီးရန် တာ၀န်ချခံထားရသည့် ၀န်ထမ်း အမှတ်စဉ်",
											"destroy_duty_employee_name"  => " ဖျက်ဆီးရန် တာ၀န်ချခံထားရသည့် ၀န်ထမ်း အမည်",
											"destroy_duty_designation"  => " ဖျက်ဆီးရန် တာ၀န်ချခံထားရသည့် ၀န်ထမ်း၏ရာထူး",
											"destroy_duty_department"  => " ဖျက်ဆီးရန် တာ၀န်ချခံထားရသည့် ၀န်ထမ်း၏ဌာန",
											"status"  => " ဖော်ြပချက်",
											
											"file_transaction_id"  => " စာဖိုင် အ၀င်အထွက် အမှတ်စဉ်",
											"folder_transaction_id"  => " စာဖိုင်တွဲ အ၀င်အထွက် အမှတ်စဉ်",
											"taken_date"  => " ထုတ်ယူသည့် ရက်စွဲ",
											"given_date"  => " ပြန်အပ်သည့် ရက်စွဲ",
											"taken_employeeid"  => " ထုတ်ယူသည့် ၀န်ထမ်း၏အမှတ်စဉ်",
											"taken_employee_name"  => " ထုတ်ယူသည့် ၀န်ထမ်း၏အမည်",
											"taken_designation"  => " ထုတ်ယူသည့် ၀န်ထမ်း၏ရာထူး",
											"taken_department"  => " ထုတ်ယူသည့် ၀န်ထမ်း၏ဌာန",
											"given_employeeid"  => " ပြန်အပ်သည့် ၀န်ထမ်း၏အမှတ်စဉ်",
											"given_employee_name"  => " ပြန်အပ်သည့် ၀န်ထမ်း၏အမည်",
											"given_designation"  => " ပြန်အပ်သည့် ၀န်ထမ်း၏ရာထူး",
											"given_department"  => " ပြန်အပ်သည့် ၀န်ထမ်း၏ဌာန",
											
											"file_type_id"  => " စာဖိုင်တွဲ အမှတ်စဉ်",
											"file_type_code"  => " စာဖိုင်တွဲ ကုတ်နံပါတ်",
											"file_type_name"  => " စာဖိုင်တွဲ  အမည်",
											
											"folder_id"  => " စာဖိုင်တွဲ အမှတ်စဉ်",
											"rfid_no"  => " RFID No.",
											"folder_no"  => " စာဖိုင်တွဲ နံပါတ်",
											"shelf_row"  => " စင် အထပ်",
											"shelf_column"  => " စင် အကန့်",
											
											"transaction_id"  => " စာဖိုင်တွဲ အ၀င်အထွက်အမှတ်စဉ်",
											
											"gate_id"  => " ဂိတ် အမှတ်စဉ်",
											"gate_code"  => " ဂိတ် ကုတ်နံပါတ်",
											"gate_name"  => " ဂိတ် အမည်",
											
											"location_id"  => " တည်နေရာ အမှတ်စဉ်",
											"location_code"  => " တည်နေရာ ကုတ်နံပါတ်",
											"location_name"  => " တည်နေရာ အမည်",
											
											"security_type_id"  => " လုံခြုံမှု့ အဆင့်အတန်း အမှတ်စဉ်",
											"security_type_code"  => " လုံခြုံမှု့ အဆင့်အတန်း ကုတ်နံပါတ်",
											"security_type_name"  => " လုံခြုံမှု့ အဆင့်အတန်း အမည်",
											
											"shelf_id"  => " စင် သတ်မှတ်ခြင်း အမှတ်စဉ်",
											"shelf_code"  => " စင် သတ်မှတ်ခြင်း  ကုတ်နံပါတ်",
											"shelf_name"  => " စင် သတ်မှတ်ခြင်း  အမည်",
											"no_of_row"  => " စင်၏ အထပ်",
											"no_of_column"  => " စင်၏ အကန့်",
											
											"position_id"=>"ရာထူး အမှတ်စဉ်", 
											"position_name"=>"ရာထူး အမည်", 
											
											"id"  => " Gate Log အမှတ်စဉ်",
											"log_time"  => " Gate Log Time",
											
											"division_id"  => " ပြည်နယ်/တိုင်း အမှတ်စဉ်",
											"division_code"  => " ပြည်နယ်/တိုင်း ကုတ်နံပါတ်",
											"division_name"  => " ပြည်နယ်/တိုင်း အမည်",
											
											"township_id"  => "မြို့နယ် အမှတ်စဉ်",
											"township_code"  => "မြို့နယ် ကုတ်နံပါတ်",
											"township_name"  => "မြို့နယ် အမည်",
											
											"ward_id"  => " ရပ်ကွက် အမှတ်စဉ်",
											"ward_name"  => " ရပ်ကွက် အမည်",
											
											"department_id"  => " ဌာန အမှတ်",
											"department_name"  => " ဌာန အမည်",
											"is_external"  => " ဌာန ပြည်တွင်း/ ပြည်ပ",
											
											"created_by"=>"ထည့်သွင်းသူ",
											"created_date"=>"စတင်ထည့်သွင်းသည့် ရက်စွဲ",
											"modified_by"=>"ပြုပြင်သူ",
											"modified_date"=>"ပြုပြင်သည့် ရက်စွဲ",
											
											"encrypt_value" => "လျှို့ဝှက် code"
										);											
			
			$description = '';
			
			if($type == 'Update')
			{
				foreach($new_field_arr as $key=>$value)
				{ 
					if($old_data[$key] != trim($value))
						$description .= $column_arr[$key] . '>'. $old_data[$key] . '>'. $value .', ';
				}
			}
			else
			{
				foreach($new_field_arr as $key=>$value)
				{
					if ($value != null)
						$description .= $column_arr[$key] . '='. $value .', ';
					//echo $description.'<br/>';
				}//exit;
			}
			return substr($description, 0, -2);
		}
		
		function get_old_data($table, $cri_str, $param = array())
		{
			$eventlogdal = new eventlogdal();
			$result=$eventlogdal->get_old_data($table, $cri_str, $param );
			return $result;
		}
		
		function get_all_old_data($table, $cri_str)
		{
			$eventlogdal = new eventlogdal();
			$result=$eventlogdal->get_all_old_data($table, $cri_str);
			return $result;
		}
		
		function set_encrypt_value($table, $cri_str)		
		{
			$new_value_arr = $this->get_old_data($table, $cri_str);
			
			$encrypt_value = "";
			foreach($new_value_arr AS $key=>$val)
			{			
				if($key != 'encrypt_value')
				$encrypt_value .= $val. ", ";
			}
			$encrypt_value = substr_replace($encrypt_value, "", -2);			
			$encrypted = encrypt($encrypt_value);	
			$query="UPDATE fss_tbl_$table SET encrypt_value =:encrypted WHERE $cri_str;";
			$result=execute_query($query,array("encrypted"=>$encrypted));		
			return $encrypted;
			//$new_value_arr['encrypt_value'] = $encrypted;
		}
		
		//select all //
		function select_all_event_log($DisplayStart , $DisplayLength ,$SortingCols, $cri_arr) 
		{
			$eventlogdal = new eventlogdal ( );
			return $eventlogdal->select_all_event_log ($DisplayStart , $DisplayLength ,$SortingCols, $cri_arr);		
		}
		
		function select_description_by_id($id)
		{
			$eventlogdal = new eventlogdal ();
			return $eventlogdal->select_description_by_id($id);
		}
	}
?>