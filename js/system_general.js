function escapeHtml_encode(str) 
{
	if( str )
	{
		return str
		  .replace(/&lt;/ig, "<")
		  .replace(/&gt;/ig, ">")
		  .replace(/&quot;/ig, '"')
		  .replace(/&#039;/g, "'")
		  .replace(/&amp;/ig, "&");
	}
	else
		return '';
}
	
function isEnglishOnly(val, title)
{
	if( val != '' )
	{
		if( /^([a-zA-Z0-9\-\/\\])+$/i.test(val) )
		{
			text_flag = true;
			clean_action_message('#alert_msg');
		}
		else
		{
			text_flag = false;
			show_action_message(title +' ကို အင်္ဂလိပ်ဂဏန်းသာ ထည့်ပေးပါရန်!', 'alert_msg');
		}
		return text_flag;
	}
	return true;
}

// for nrc
function show_card_table(ori_tbl, tblid_1, tblid_2)
{
	jQuery('#'+ori_tbl).css('display' , 'flex');
	jQuery('#'+tblid_1).css('display' , 'none');
	jQuery('#'+tblid_2).css('display' , 'none');
	clean_action_message('#alert_msg');
}

/** start of admin site functions by msd **/
function getcri_nrc_township(division_code, id, township_code)
{
	jQuery.post('form_exec.php',{'nrc_division_code' : division_code, 'nrc_township_code' : township_code},
	function (result)
	{
		jQuery('#selnrctownship' + id).html(result);
	});
}

function get_nrc_township(division_code, id, township_code)
{
	$('#selnrctownship'+id).next().next().next().val("loading");
	$('#selnrctownship'+id).next().next().next().next().remove();
	$.post('form_exec.php',{'nrc_division_code' : division_code, 'nrc_township_code' : township_code},
	function (result)
	{
		var isfocussel = false;
		if($('#selnrctownship'+id).next().next().next().is(":focus"))
			isfocussel = true;
			
		create_autocomplete(['selnrctownship'+id], township_code, '', result);

		if(isfocussel == true)
			$('#selnrctownship'+id).next().next().next().focus();
	});
}

function change_image(status,id)
{
	if(status==0)
	{	
		jQuery("#hid_delimg"+id).attr("value",1);
		jQuery("#shownofile"+id).css("display","block");
		jQuery("#btnback"+id).css("display","inline-block");
		jQuery("#show_old_image"+id).css("display","none");
		jQuery("#btnremoveimg"+id).css("display","none");
		jQuery("#img_button"+id).css("display","none");
		jQuery("#img_delete"+id).css("display","none");
	}
	else
	{
		jQuery("#hid_delimg"+id).attr("value",0);
		jQuery("#shownofile"+id).css("display","none");
		jQuery("#btnback"+id).css("display","none");
		jQuery("#show_old_image"+id).css("display","block");
		jQuery("#btnremoveimg"+id).css("display","inline-block");
		jQuery("#img_button"+id).css("display","inline-block");			
	}
}

function get_township_by_division_id(division_id, prefix)
{
	$('#divtownship').html('<img src="' + movepath + 'images/loadingpic.gif" />');
	$.post('form_exec.php',{'division_id' : division_id, 'prefix' : prefix},
	function (result)
	{
		$('#divtownship').html(result);	
		$('#'+prefix+'sel_township_name').attr('value', '');
		$('#'+prefix+'sel_ward_name').attr('disabled', true);
		$('#'+prefix+'sel_ward_name').attr('value', '');
	});
}

function get_division_ward_by_township_id(township_id, prefix)
{
	$('#divward').html('<img src="' + movepath + 'images/loadingpic.gif" />');
	$.post('form_exec.php',{'township_id' : township_id, 'prefix' : prefix}, 
	function (result_str)
	{
		if( exec_callback_sessionexpire(result_str) )
		{
			$('#divward').html(result_str['ward']);
			if(township_id != '')
			{
				$('#divdistrict').html(result_str['district']);
				$('#'+prefix+'sel_township_name').html(result_str['township']);					
				$('#'+prefix+'sel_ward_name').attr('disabled', false);
			}
			else
			{
				$('#'+prefix+'sel_ward_name').attr('disabled', true);
			}
		}
	}
	, 'json');
}

function get_division(township_id, id)
{
	if ( id == undefined )
		id = "";
	$('#divdivision'+id).html('<img src="' + movepath + 'images/loadingpic.gif" />');
	$.post('form_exec.php', {'get_township_division_id':township_id, 'inputid':id},
	function (result)
	{
		$('#divdivision'+id).html(result);
	});
}

function get_ward(township_id, id, selected_arr)
{
	if( township_id )
	{
		if ( id == undefined )
			id = '';
		if( selected_arr == undefined )
			selected_arr = ['', ''];
		$('#divward'+id).html('<img src="' + movepath + 'images/loadingpic.gif" />');
		$.post('form_exec.php', {'get_township_id':township_id, 'inputid':id},
		function (result_arr)
		{
			result_str = result_arr; // direct using parameter sometimes get error
			if( exec_callback_sessionexpire(result_arr) )
			{
				$('#divward'+id).html(result_str['selward']);	
				if( selected_arr[0] )
					$('#selward').val(selected_arr[0]);
			}
		}, 'json');
	}
}

function show_selected_department(dept_type, dept_id)
{
	$('#'+dept_id).next().next().next().val("loading");
	$('#'+dept_id).next().next().next().next().remove();
	$.post('form_exec.php',{'department_types' : dept_type}, function (result)
	{
		var isfocussel = false;
		if($('#'+dept_id).next().next().next().is(":focus"))
			isfocussel = true;
		
		create_autocomplete([dept_id], '', '', result);
		
		if(isfocussel == true)
			$('#'+dept_id).next().next().next().focus();
	});
}

function show_selected_to_department(dept_type, dept_id)
{
	$.post('form_exec.php',{'department_types' : dept_type}, function (result)
	{
		$('#' + dept_id).html(result);
		$('#' + dept_id).multipleSelect('refresh');
		$('#' + dept_id).multipleSelect('checkAll');
	});
}