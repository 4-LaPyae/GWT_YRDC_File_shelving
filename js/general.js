	var oTable;
	function Get_Cookie( check_name ) 
	{
		// first we'll split this cookie up into name/value pairs
		// note: document.cookie only returns name=value, not the other components
		var a_all_cookies = document.cookie.split( ';' );
		var a_temp_cookie = '';
		var cookie_name = '';
		var cookie_value = '';
		var b_cookie_found = false; // set boolean t/f default f

		for (i=0; i<a_all_cookies.length; i++ )
		{
			// now we'll split apart each name=value pair
			a_temp_cookie = a_all_cookies[i].split( '=' );

			// and trim left/right whitespace while we're at it
			cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

			// if the extracted name matches passed check_name
			if ( cookie_name == check_name )
			{
				b_cookie_found = true;
				// we need to handle case where cookie has no value but exists (no = sign, that is):
				if ( a_temp_cookie.length > 1 )
				{
					cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
				}
				// note that in cases where cookie is initialized but no value, null is returned
				return cookie_value;
				break;
			}
			a_temp_cookie = null;
			cookie_name = '';
		}
		if ( !b_cookie_found )
		{
			return null;
		}
	}

	function check_valid_email(email)
	{
		var atpos=email.indexOf("@");
		var dotpos=email.lastIndexOf(".");
		if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length)
			return false;
		else 
			return true;
	}

	function validateEmail(email) 
	{
		var re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if( re.test(email) )
			return true;
		else
			return false;
	}
	
	function create_sessionexpire_dialog_html()
	{
		//Close before dialog
		jQuery('*').dialog('close');
		
		var htmlstr = '<div id="sessiondialog"  style="display:none;" >'+ 
					'	<form method="post" id="frmdialog" name="frmdialog" class="frmdialog" >' +
					'		<p id="" class="blue pad5 center" >Session Expire! Please Login again.</p>' +
					'		<center><input type="button" value="Login" onclick="window.location.reload();" /></center>' +
					'	</form>'
					'</div>';
		$('body').append(htmlstr);
		$('#sessiondialog').dialog ({
			autoOpen: true,
			title:'<h3>Session Expire </h3>',
			resizable: false,
			draggable: true,
			modal:true,
			overlay: {opacity: 0.2, background: "black"},
			width: 450,
			height:'auto',
			close: function() {
				window.location.reload();
			}
		});		
		jQuery('button, input:submit, input:button, input:reset').button();	
	}
	
	function exec_callback_sessionexpire(result_arr)
	{
		if(result_arr['sessionexpire'] == 1)
		{
			create_sessionexpire_dialog_html();
			return false;
		}
		else
			return true;		
	}
	
	function isDate(datestr, format)
	{
		var isValid = true;
			
		try{
			var objdate = jQuery.datepicker.parseDate(format, datestr, null);
			if (jQuery.datepicker.formatDate(format, objdate, null) == datestr)  //validate reverse
				return true;
			else
				return false;			
		}
		catch(error)
		{
			isValid = false;
		}
		return isValid;
	}
	
	function trim(s)
	{
		return s.replace(/^\s+|\s+$/g,"");
	}
	
	function updateTips(tips, t)
	{
		if( t == '' )
			tips.html(t);
		else
			tips.html(t).effect("highlight",{},1500);
	}
	
	function IsPositiveNumeric(strString) //  check for valid numeric strings	
	{
	   var strValidChars = "0123456789";
	   var strChar;
	   var blnResult = true;
	   
	   if( strString.length == 0 ) return false;

	   //  test strString consists of valid characters listed above
	   for (i = 0; i < strString.length && blnResult == true; i++)
	   {
		  strChar = strString.charAt(i);
		  if (strValidChars.indexOf(strChar) == -1)
		  {
			 blnResult = false;
		  }
	   }
	   return blnResult;
	}
	
	//Ger Screen Resolution
	var x,y;
	function getformsize()
	{
		// for all except Explorer
		if (self.innerHeight) 
		{
			x = self.innerWidth;
			y = self.innerHeight;	
		} 
		// Explorer 6 Strict Mode
		else if (document.documentElement && document.documentElement.clientHeight) 
		{
			x = document.documentElement.clientWidth;
			y = document.documentElement.clientHeight;
			
		} 
		// other Explorers
		else if (document.body) 
		{
			x = document.body.clientWidth;
			y = document.body.clientHeight;
		}	
	}
	
	function chk_key(event, escapeKeys)
	{
		// the keycode for the key pressed
		var keyCode = event.which;
		//alert(keyCode);
		// 48-57 Standard Keyboard Numbers
		var isStandard = (keyCode > 47 && keyCode < 58);	
		// 96-105 Extended Keyboard Numbers (aka Keypad)
		var isExtended = (keyCode > 95 && keyCode < 106);	
		// 9 Tab,8 Backspace, 46 Forward Delete ,37 Left Arrow, 38 Up Arrow, 39 Right Arrow, 40 Down Arrow 
		var validKeyCodes = ',9,8,37,38,39,40,46,';
		validKeyCodes += escapeKeys;
		var isOther = ( validKeyCodes.indexOf(',' + keyCode + ',') > -1 );
		//alert(validKeyCodes.indexOf(',' + keyCode + ','));
		if ( isStandard || isExtended || isOther )
			return true;
		else 
			return false;
	}
	
	function chk_myanmarchar(event, val, escapeKeys)
	{
		// 16, 222
		var keyCode = event.which;
		// 9 Tab,8 Backspace, 35 End, 36 Home, 46 Forward Delete ,37 Left Arrow, 38 Up Arrow, 39 Right Arrow, 40 Down Arrow 2 Right Click
		var validKeyCodes = ',9,8,35,36,37,38,39,40,46,';
		validKeyCodes += escapeKeys;
		var isOther = ( validKeyCodes.indexOf(',' + keyCode + ',') > -1 );
		//alert(validKeyCodes.indexOf(',' + keyCode + ','));
		if ( isOther )
			return true;
		else 
			return false;
		/* alert(keyCode);
		alert('value = ' + val );
		alert(val.charCodeAt()); */
	}
	
	function confirm_delete_popup(label, dialog_id)
	{
		var str = '<div class="modal fade modaldiv animated bounceInDown" id="'+dialog_id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">\
			<div class="modal-dialog">\
				<div class="modal-content rounded-0">\
					<!-- <div class="modal-header py-4">\
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" >\
						<span aria-hidden="true">&times;</span></button>\
						<h4 class="modal-title" id="myModalLabel">အတည်ပြုခြင်း</h4>\
					</div>-->\
					<div class="modal-body text-center py-5">\
						<svg class="icon i-lg text-warning"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-notification" /></svg>\
						<h4 class="modal-title my-2" id="myModalLabel">အတည်ပြုခြင်း</h4>\
						<form class="my-3" role="form" method="post" enctype="multipart/form-data" action="">\
							<div id="delete_message"></div>\
						</form>\
						<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="continue_delete()">ဖျက်မည်</button>\
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">မဖျက်ပါ</button>\
					</div>\
				</div>\
			</div>\
		</div>';
		$('body').append(str);	
		$('#delete_message').html(label);
	}
	
	function confirm_inform_popup(label, title)
	{
		var str = '<div class="modal fade modaldiv animated bounceInDown" id="modal-inform" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">\
						<div class="modal-dialog">\
							<div class="modal-content">\
								<div class="modal-header">\
									<button type="button" class="close" data-dismiss="modal" aria-label="Close" >\
									<span aria-hidden="true">&times;</span></button>\
									<h4 class="modal-title" id="inform_title"></h4>\
								</div>\
								<div class="modal-body">\
									<form class="form-horizontal" role="form" action="">\
									<div class="form-group">\
										<label class="col-md-12 control-label" id="inform_message" style="text-align:left;"></label>\
									</div>\
									</form>\
								</div>\
								<div class="modal-footer">\
									<button type="button" class="btn btn-warning" data-dismiss="modal" onclick="" >OK</button>\
								</div>\
							</div>\
						</div>\
					</div>';		
		$('body').append(str);	
		$('#inform_message').html(label);
		$('#inform_title').html(title);
	}
	
	function confirm_status_popup(label, dialog_id)
	{
		var str = '<div class="modal fade modaldiv animated bounceInDown" id="'+dialog_id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">\
			<div class="modal-dialog">\
				<div class="modal-content rounded-0">\
					<div class="modal-body text-center py-5">\
						<svg class="icon i-lg text-warning"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-notification" /></svg>\
						<h4 class="modal-title my-2" id="myModalLabel">အတည်ပြုခြင်း</h4>\
						<form class="my-3" role="form" method="post" enctype="multipart/form-data" action="">\
							<div id="delete_message"></div>\
						</form>\
						<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="continue_changestatus()">အတည်ပြုမည်</button>\
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">အတည်မပြုပါ</button>\
					</div>\
				</div>\
			</div>\
		</div>';
		$('body').append(str);	
		$('#delete_message').html(label);
	}
	
	function Numericvaluewithoutminus(evevtobj,obj,isdecimal)
	{
		var KeyCode;
		if(document.all)
		{
			KeyCode=evevtobj.keyCode;
		}
		else
		{
			KeyCode=evevtobj.which;
		}
		
		var str=obj.value;
		//return IsNumeric(str,isdecimal);
		
		if((KeyCode==45) && str=="")
		{
			return false;
		}
		if(KeyCode==46)
		{
			if(isdecimal==true)
			{
				if(str.indexOf(".")>-1)
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		
		if((KeyCode ==0) || (KeyCode ==8))
		{
			return true;
		}
		 
		
		if((KeyCode<48 || KeyCode>=58) && (KeyCode !=46))
		{
			return false;
		}
		return true;
	}
	
	function is_unicodedigit(str)
	{
		if( str.length > 0 )
		{
			for(i=0; i<str.length; i++)
			{
				var c = str.charCodeAt(i);
				if( c == 46 || ( c>=48 && c<=57 ) || ( c>=4160 && c<=4169 ) )  //digit ascii code range for english and myanmar (mm3 and zawgyi use same code range)
					continue;
				else
				{
					return false;
				}	
			}
		}
		return true;
	}
	
	function stringToAscii(s)
	{
	  var ascii="";
	  if(s.length>0)
		for(i=0; i<s.length; i++)
		{
		  var c = ""+s.charCodeAt(i);
		  while(c.length < 3)
		   c = "0"+c;
		  ascii += c+'-';
		}
	  return(ascii);
	}
	
	function smtMask(obj, format, type)
	{	
		/**
		* Develoed by SMT
		* oNumberMask = new Mask("#,###", "number");
		* oNumberMask.attach(smt);
		*/
		
		oNumberMask = new Mask(format, type);
		oNumberMask.attach(obj);
	}
	
	function pass_htmltag(tag_name)
	{
		return tag_name.replace(/</g,"< ");
	}
	
	var globalalertclass;
	function clean_action_message(obj_id, alertclass)
	{
		var action_obj = '#validateTips';
		if( obj_id != undefined )
			action_obj = obj_id;
		if( alertclass == undefined )
			alertclass = globalalertclass;
		jQuery(action_obj).html('').removeClass(alertclass);
	}
	
	function show_action_message(message, alertclass, obj_id)
	{
		var action_obj = 'validateTips';
		globalalertclass = alertclass;
		if( obj_id != undefined )
			action_obj = obj_id;	
		jQuery('#' + action_obj).html(message).addClass(alertclass).effect("highlight", {}, 200);
	}
	
	function close_dialog()
	{
		jQuery('#divdialog').dialog('close');
	}
	
	function hidebutton_showloadingimage()
	{
		$('#divbuttons').hide();
		$('#divprogress').addClass('pos-rlt py-5 mt-5').html('<div class="d-loading loading-sm" data-text="Processing ..."></div>');
	}
	
	function hideloadingimage_showbutton()
	{
		$('#divbuttons').css('display', 'block');
		$('#divprogress').empty();
	}
	
	function create_loadingimage_dialog( dialog_id, title, movepath, size)
	{
		var str = '<div class="modal fade modaldiv animated bounceInDown" id="'+dialog_id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"  >\
						<div class="modal-dialog '+size+'">\
							<div class="modal-content rounded-0">\
								<div class="modal-header py-3">\
									<h4 class="modal-title text-md" id="myModalLabel">'+title+'</h4>\
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">\
									<span aria-hidden="true">&times;</span></button>\
								</div>\
								<div class="modal-body"><div class="py-5 pos-rlt"><div class="d-loading loading-sm" data-text=" "></div></div></div>\
							</div>\
						</div>\
						</div>';
		$('body').append(str);
	}
	
	function create_dialog_html( dialog_id, title, bodycontent)
	{
		var str = '<div class="modal fade modaldiv animated bounceInDown" id="'+dialog_id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">\
					<div class="modal-dialog">\
						<div class="modal-content rounded-0">\
							<div class="modal-header py-3">\
								<h4 class="modal-title text-md" id="dialog-title"></h4>\
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">\
								<span aria-hidden="true">&times;</span></button>\
							</div><div id="bodycontent"></div></div>\
						</div>\
					</div>';
		$('body').append(str);
		jQuery('.modal-body, .modal-footer').remove();
		$('#dialog-title').html(title);
		$('#bodycontent').html(bodycontent);
	}
	
	// For Add and Edit Callback
	function add_and_update_exec_callback_dialog(result_obj)
	{
		if( result_obj.sessionexpire == 1 )
			create_sessionexpire_dialog_html();
		else
		{
			if( result_obj.success == 1 )
			{
				$('*').modal('hide');
				show_action_message(result_obj.message, result_obj.alertclass);
				oTable.fnStandingRedraw();
			}
			else
			{
				form_id = 'alert_msg';
				if( result_obj['success'] == 2 )
					form_id = 'frmdialog';	
				show_action_message(result_obj['message'],'', form_id);
				hideloadingimage_showbutton();
			}
		}
	}
	
	// For Delete
	function delete_exec_callback(result_obj)
	{
		if( result_obj.sessionexpire == 1 )
			create_sessionexpire_dialog_html();
		else
		{
			jQuery('#divmsg1').dialog('close');
			show_action_message(result_obj['message'], result_obj['alertclass']);			
			if( result_obj.success == 1 )
			{
				oTable.fnStandingRedraw();
			}
		}
	}
	
	function hack_htmltag(tagname)
	{
		/**
		* Created by SMT
		* Used for Delete Popup
		**/
		return tagname.replace("<","< ");
	}
	
	function get_datatable_paging_cookie(page_name, oSettings)
	{
		if( oSettings == undefined )
		{
			if(jQuery.cookie(page_name + '[iDisplayStart]') == null)
				jQuery.cookie(page_name + '[iDisplayStart]', 0);
			
			if(jQuery.cookie(page_name + '[iDisplayLength]') == null)
				jQuery.cookie(page_name + '[iDisplayLength]', 10);
			
			ilength = parseInt(jQuery.cookie(page_name + '[iDisplayLength]'));
			istart = parseInt(jQuery.cookie(page_name + '[iDisplayStart]'));
		}
		else
		{
			jQuery.cookie(page_name + '[iDisplayLength]', oSettings._iDisplayLength);
			jQuery.cookie(page_name + '[iDisplayStart]', oSettings._iDisplayStart);
		}
	}
	
	function get_datatable_sorting_cookie(page_name, defaultSorting, aaSorting)
	{
		if(defaultSorting == undefined)defaultSorting = "1,'desc'";
		if( aaSorting == undefined )
		{
			if( jQuery.cookie(page_name + '[aaSorting]') == null )	//added by zmn to save sorting state 
			{
				jQuery.cookie(page_name + '[aaSorting]', "[["+defaultSorting+"]]");  //need to set default sorting state
			}
			aasorting = eval('(' + jQuery.cookie(page_name + '[aaSorting]') + ')'); 	//convert json string to json object
		}
		else
		{
			jQuery.cookie(page_name + '[aaSorting]', aaSorting);
		}
	}
	
	function updatecontrol(parctl, parvalue)   // added by zmn to fix in IE6 not recognize null problem
	{
		if( parvalue )
			jQuery(parctl).val(parvalue);
	}
	
	function clear_page_cookie(page_name, cookie_name_arr)
	{
		for(val in cookie_name_arr)
		{
			jQuery.cookie(page_name + '[' + cookie_name_arr[val] + ']', null);
		}
	}
	
	function save_criteria_in_cookie(page_name, cookie_name_arr)
	{
		var id_arr = ['cri_seldepartment', 'seldepartmentname'];
		for(val in cookie_name_arr)
		{
			if( jQuery.inArray(cookie_name_arr[val], id_arr) == -1 )
				value = jQuery('#' + cookie_name_arr[val] ).val();				
			else
				value = get_selected_value(cookie_name_arr[val]);
			
			jQuery.cookie(page_name + '[' + cookie_name_arr[val] + ']', value);
		}
		jQuery.cookie(page_name + '[iDisplayStart]', null);//reset Paging
	}
	
	function get_criteria_text(page_name, cookie_name_arr)
	{
		var value_arr = [];
		var id_arr = ['cri_seldepartment', 'seldepartmentname'];
		var  firstoption_arr = ['အားလုံး', ' -- အားလုံး -- ', 'ရွေးရန်', ' --- ရွေးရန် --- ', '-- ရွေးရန် --'];
		for(val in cookie_name_arr)
		{
			if( jQuery.inArray(val, id_arr) == -1 )
			{
				if ( val == 'txtfromcurrentrankdate' )
				{
					if ( jQuery("#txtfromcurrentrankdate").val() != '' && jQuery("#selcurrentrankdate option:selected").text() != '' && jQuery("#selcurrentrankdatecondition option:selected").text() != '' )
						value_arr.push(cookie_name_arr[val]+jQuery("#txtfromcurrentrankdate").val()+'တွင်'+jQuery("#selcurrentrankdate option:selected").text()+'နှစ်'+jQuery("#selcurrentrankdatecondition option:selected").text()+"၀န်ထမ်းများ");
				}
				else if ( val == 'txttocurrentrankdate' )
				{
					if ( jQuery("#txtfromcurrentrankdate").val() != '' && jQuery("#txttocurrentrankdate").val() != '' && jQuery("#selcurrentrankdate option:selected").text() != '' )
						value_arr.push(cookie_name_arr[val]+" လက်ရှိရာထူးလုပ်သက် "+jQuery("#txtfromcurrentrankdate").val()+" မှ "+jQuery("#txttocurrentrankdate").val()+" ထိ ကာလအတွင်းတွင် လက်ရှိရာထူးလုပ်သက်"+" ("+jQuery("#selcurrentrankdate option:selected").text()+")"+"ပြည့်မည့် ဝန်ထမ်းများ");
				}
				else
				{
					if( jQuery("#"+val+" option:selected").text() == '' )
					{
						if ( jQuery("#"+val).val() != '' )
							value_arr.push(cookie_name_arr[val]+jQuery("#"+val).val());
					}
					else if ( jQuery.inArray(jQuery("#"+val+" option:selected").text(), firstoption_arr) == -1 )
						value_arr.push(cookie_name_arr[val]+jQuery("#"+val+" option:selected").text());
				}
			}
			else // for multi select
			{
				var str = get_selected_text(val, cookie_name_arr[val]);
				if( str !== false)
					value_arr.push(str);
			}
		}
		return value_arr.join(", ");
	}
	
	// get selected text for multi-selectbox
	function get_selected_text(id, label)
	{
		var checked_arr = [];
		// if select all checkbox is checked, get its text
		if( jQuery("#"+ id).next().find("[name=selectAll]").is(":checked") )
			return false;
		else
		{
			jQuery("#"+ id).next().find("[name=selectItem[]]:checked").each(function ()
			{
				checked_arr.push(jQuery(this).parent().text());
			});
			if( checked_arr.length == 0) // if not select set default
				return false;
			else
			{
				var str = checked_arr.join("/ ");
				label += str;
				return label;
			}
		}
	}
	
	function load_criteria_from_cookie(page_name, cookie_name_arr)
	{
		for(val in cookie_name_arr)
		{
			updatecontrol('#' + cookie_name_arr[val], jQuery.cookie ( page_name + '[' + cookie_name_arr[val] + ']' ));
		}
	}
	
	function return_jsonstring_from_cookie(page_name, cookie_name_arr)
	{
		var jsonfilter = {};
		for(val in cookie_name_arr)
		{
			var obj_pro = cookie_name_arr[val];
			var obj_val = jQuery.cookie(page_name + '[' + cookie_name_arr[val] +']');
			Object.defineProperty(jsonfilter, obj_pro, {value : obj_val,writable : true, enumerable : true, configurable : true});			
		}
		var cri_str = JSON.stringify(jsonfilter);
		//alert(cri_str);
		return cri_str;
	}
	
	function set_mmkeyboard(selector)
	{
		if( selector == undefined )
		selector = '';		
		jQuery(selector + " .qwerty,"+selector+" .qwertymulti").after("<a href='#' class='keyboardbtn'><img src='images/tia.png' /></a>");
		
		jQuery(selector + ' .keyboardbtn').click(function() 
		{
			$(this).prev().getkeyboard().reveal();
			return false;
		});	
			
		// QWERTY Text Input
		// The bottom of this file is where the autocomplete extension is added
		// ********************
		//$('.qwerty:first').keyboard({ layout: 'myanmar-azerty' });

		// QWERTY Text Area
		// ********************
		jQuery(selector + ' .qwerty').keyboard({
			layout   : 'myanmar-azerty',
			openOn : ''
		});
		
		jQuery(selector + ' .qwertymulti').keyboard({
			layout   : 'myanmar-multilines-azerty',
			openOn : ''
		});

		// Set up typing simulator extension on ALL keyboards
		jQuery(selector + ' .ui-keyboard-input').addTyping();
	}

	function isnumber(character)
	{
		var theUnicode = character.toString(16).toUpperCase();
		while (theUnicode.length < 4) {
			theUnicode = '0' + theUnicode;
		}
		var arrUnicodeNumber=["\\u0008","\\u0030","\\u0031","\\u0032","\\u0033","\\u0034","\\u0035","\\u0036","\\u0037","\\u0038","\\u0039","\\u1040","\\u1041","\\u1042","\\u1043","\\u1044","\\u1045","\\u1046","\\u1047","\\u1048","\\u1049"];
		theUnicode = '\\u' + theUnicode;
		if(jQuery.inArray(theUnicode, arrUnicodeNumber)<0)return false;
		else return true;
	}
	
	/*function keyDownNumberCheck(e)
	{
		 // Allow: backspace, delete, tab, escape, and enter
		if ( e.keyCode == 46 || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 27 || e.keyCode == 13 || 
		// Allow: Ctrl+A
		(e.keyCode == 65 && e.ctrlKey === true) || 
		// Allow: home, end, left, right
		(e.keyCode >= 35 && e.keyCode <= 39)) {
			// let it happen, don't do anything
			return;
		}
		else {
		// Ensure that it is a number and stop the keypress
		if (e.shiftKey || (e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105 )) e.preventDefault(); 
		}
	}*/

	function isnumeric(control)
	{
		var strvalue = control.value.trim();
		control.value = strvalue;
		for (var i=0; i < strvalue.length; i++) 
		{
			if( !isnumber(strvalue.charCodeAt(i)) )
			{
				control.value = '';
				control.focus();
				return false;
			}
		}
		return true;
	}
	
	function confirm_dialog(message,width, okbuttonText, okbuttonFunction, cancelButtonText)
	{
		$('body').append('<div id="divmsg1" name="divmsg1" style="display: none;" class="mmfont">'+ message +'</div>');		
		$('#divmsg1').dialog (
		{
			autoOpen: true,
			title:'<h3>အတည်ပြုခြင်း</h3>',
			resizable: false,
			draggable: true,
			modal:true,
			overlay: {opacity: 0.2, background: "black"},			
			width: width,
			height:'auto',
			open: function() {
				$('#divmsg1').elastic();
			},
			close: function() { 
					 $(this).empty().remove();	
			} ,
			buttons: {
				"cancel" : {
					  text : cancelButtonText,
					  click : function(){
						$(this).empty().remove();	
					}
				},
				"ok" : {
					  text : okbuttonText,
					  click : function(){
						okbuttonFunction();
						$(this).empty().remove();	
					}
				}
			}		
		});
	}
	
	function checkrequired(tips, o, n)
	{
		if(trim(o.val())=='')
		{
			updateTips(tips, n);
			tips.css({display:""});
			o.focus();	
			return false;
		}
		else
		{
			updateTips(tips, '');
			tips.css({display:"none"});
			return true;
		}
	}
	
	function get_nrc_township(division_code, id, township_code)
	{
		jQuery.post('form_exec.php',{'nrc_division_code' : division_code, 'nrc_township_code' : township_code},
		function (result)
		{
			jQuery('#selnrctownshipemployeenrcno').html(result);
			jQuery('#selnrctownship' + id).html(result);
		});
	}
	
	/* To show loading image */
	function getloading()
	{
		$('#divloading').fadeIn();
	}
	
	function postRedirectURL(url, postdata, multipart) {
		var form = document.createElement("FORM");
		form.method = "POST";
		if(multipart) {
			form.enctype = "multipart/form-data";
		}
		form.style.display = "none";
		document.body.appendChild(form);
		form.action = url;

		var dataitem;
		for (dataitem in postdata) {
			input = document.createElement("INPUT");
			input.type = "hidden";
			input.name = decodeURIComponent(dataitem);
			input.value = decodeURIComponent(postdata[dataitem]);
			form.appendChild(input);
		}
		form.submit();
	}
	
	function chk_all_children(obj)
	{
		var tbl = $(obj).parents('table').attr('id');
		if ( $(obj).prop( "checked" ) )
			$('#'+tbl).find('input').prop('checked', true);
		else
			$('#'+tbl).find('input').prop('checked', false);
	}
	
	function chk_parent(obj)
	{		
		check = false;
		var tbl = $(obj).parents('table').attr('id');
		if ( $(obj).prop( "checked" ) )
			$('#'+tbl).find('th').eq(0).find('input').prop('checked', true);
		else
		{
			$('#'+tbl+' tr:gt(0)').find('input').each(function (i)
			{
				if ( $(this).prop( "checked" ) )
					check = true;
			});
			if ( ! check )
				$('#'+tbl).find('th').eq(0).find('input').prop('checked', false);
		}	
	}
	
	function create_boostract_dialog(title, content, dialog_id, div_width)
	{		
		var str = '<div class="modal fade modaldiv animated bounceInDown" id="'+dialog_id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"  >\
						<div class="modal-dialog">\
							<div class="modal-content">\
								<div class="modal-header">\
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">\
									<span aria-hidden="true">&times;</span></button>\
									<h4 class="modal-title" id="myModalLabel">'+title+'</h4>\
								</div>\
								'+content+'\
							</div>\
						</div>\
						</div>';
		$('body').append(str);
		if (div_width != undefined )
			$('.modal-dialog').width(div_width);
	}
	
	function select_data_exec_call_back(return_obj)
	{
		jQuery('.modal-body, .modal-footer').remove();
		jQuery('.modal-content').append(return_obj.popupdata);
	}

	var globalnavPos;
	function navFixedTop() {
		// cache the element
		var $navBar = $('.navbar-custom');

		// find original navigation bar position
		var navPos = $navBar.offset().top;
		if( navPos == undefined )
			navPos = globalnavPos;

		// on scroll
		$(window).scroll(function() {

			// get scroll position from top of the page
			var scrollPos = $(this).scrollTop();
			//console.log( scrollPos +" : "+ navPos);
			// check if scroll position is >= the nav position
			if (scrollPos >= navPos) {
				$navBar.addClass('affix');
			} else {
				$navBar.removeClass('affix');
			}

		});
		/*var navPos = $('.navbar').offset().top;
		$(window).scroll(function() {
			$('.navbar-custom').affix({
				offset: { top: navPos }
			});
		});*/
	}

	function highlightselected()
	{
		found=false;
		$('a').filter(function (index){

			if(this.href == location.href)
			{
				//Set_Cookie('Co_Root_Page',location.href, '24', '/', '', '');
				found=true;
				return this;
			}
		}).parent().addClass('active').parent().parent().addClass('active').parent().addClass('active');

		if(!found)
		{
			$('a').filter(function (index){
				if(this.href==Get_Cookie('Co_Root_Page'))
				{
					return this;
				}
			}).parent().addClass('active').parent().parent().addClass('active').parent().addClass('active');
		}
	}

	function changeimageroot(objimg,objlevel)
	{
		if(objimg.src.indexOf('minus') > 0)
		{
			objimg.src= movepath + "images/plus-square.svg";
			$('#'+objlevel).hide().removeClass('pl-4');
			//document.getElementById(objlevel).style.display='none';
		}
		else
		{
			objimg.src= movepath + "images/minus-square.svg";
			$('#'+objlevel).fadeIn(50).addClass('pl-4');
			//document.getElementById(objlevel).style.display= 'block' ;
		}
	}
	
	function changepicroot(objimg,objtrname,type)
	{
		if(objimg.src.indexOf('minus') > 0)
		{
			objimg.src= movepath + "images/plus-square.svg";
			if(type == 'district')
				$(".tblregion"+objtrname).css("display","none");
			else
				$(".tblregion"+objtrname).css("display","none"); //.toString()
		}
		else
		{
			objimg.src= movepath + "images/minus-square.svg";
			if(type == 'brand')
				$(".tblregion"+objtrname).css("display","");
			else
				$(".tblregion"+objtrname).css("display","");
		}
	}
	
	// initialize combobox
	function create_autocomplete(id_arr, selected_id, selected_val, data, disabled)
	{
		var nrc_arr = ['selnrcdivisioncustomernrcno', 'selnrctownshipcustomernrcno'];
		var mini_arr = [];
		// var mini_arr = ['selemployeesingle', 'selemployeerace', 'selemployeereligion'];

		if( disabled == undefined )
			disabled = false;
			
		for(val in id_arr)
		{
			var id = id_arr[val];
			var select_obj = '#'+ id;

			// decide combobox should disabled or not
			if( ! disabled )
			{
				// refresh combobox when dynamic data is added
				if( data != undefined )
				{
					jQuery(select_obj).parents('.scombobox').scombobox('toSelect');
					jQuery(select_obj).html(data);
				}
				
				// initialize scombobox with selected options
				$(select_obj).scombobox(
				{
					'full-match': false,
					'forbidInvalid': true,
					'reassignId': false,
					'maxHeight': 300,
					'fillOnTab': false,
					'empty':true,
				});
				$(select_obj).bind('change', function() {
					$(this).valid();
				});
				// for value inserting of update autocomplete box
				jQuery(select_obj).children().removeAttr("selected");
				if( selected_id )
				{
					if( ! selected_val )
						selected_val = selected_id;
					jQuery(select_obj).siblings('.scombobox-value').val(selected_id);
					if(selected_val != 0)
						$(select_obj).siblings('.scombobox-display').addClass('form-control').val(selected_val);$(select_obj).siblings('.scombobox-dropdown-background').css({'display': 'none'});
					jQuery(select_obj).children('option[value="'+selected_id+'"]').attr('selected', 'selected');
				}

				// make mini autocomplete box to selectbox like NRC
				if( jQuery.inArray(id, nrc_arr) != -1 )
				{
					// make small size autocomplete box
					jQuery(select_obj).parents('.scombobox').css({'display':'inline', 'width':'auto'});
					jQuery(select_obj).siblings('.scombobox-dropdown-background').css({'display': 'none'});
					jQuery(select_obj).siblings('.scombobox-dropdown-arrow').css({'width': '36'});
					jQuery(select_obj).siblings('.scombobox-display').addClass('form-control').css({'width': '75'});
					
					// auto select first option for add new
					if( ! selected_id )
					{
						jQuery(select_obj +' option:first').attr('selected', 'selected');
						jQuery(select_obj).siblings('.scombobox-value').val(jQuery(select_obj +' option:first').val());
						jQuery(select_obj).siblings('.scombobox-display').val(trim(jQuery(select_obj +' option:first').text()));
					}
				}
				else
				{
					if( jQuery.inArray(id, mini_arr) != -1 )
					{
						if( id == 'selemployeesingle' )
							dis_width = 77;
						else if ( id == 'selemployeerace' )
							dis_width = 77;	
							
						// make small size autocomplete box
						jQuery(select_obj).parents('.scombobox').css({'display':'inline', 'width':'auto'});
						jQuery(select_obj).siblings('.scombobox-dropdown-background').css({'display': 'none'});
						jQuery(select_obj).siblings('.scombobox-dropdown-arrow').css({'width': '36'});
						jQuery(select_obj).siblings('.scombobox-display').addClass('form-control').css({'width': '75'});
					}
					
					if( ! selected_id )
						jQuery(select_obj).append('<option selected value=""></option>');
				}
			}
			else
			{
				$(select_obj).attr('disabled', true);
				$(select_obj).children().removeAttr("selected");
				$(select_obj).parents('.scombobox').scombobox({disabled: true});
			}
		}
	}
	
	// initialize multi-selectbox
	function create_multi_selectbox(id_arr, cookie_name)
	{
		for( key in id_arr )
		{
			if( id_arr[key].search(',') )
			{
				var arr =  id_arr[key].split(",");
				var text = arr[0];
				var tabindex = arr[1];
			}
			else
			 var text = id_arr[key];
			$("#"+ key).multipleSelect({
				width: '100%',
				maxHeight: 300,
				selectAllText: text,
				input_id: key,
				input_tabindex: tabindex,
				cookie: cookie_name,
				filter: true
			});
		}
	}
	
	// checked selected value on loadpagestate
	function checked_data_onload(id, value)
	{
		if( value != null )
		{
			var value_arr = value.replace(/'/g, "").split(",");
			for( key in value_arr )
			{
				jQuery("#"+ id).next().find('[value="'+value_arr[key]+'"]').attr('checked', true);
			}
		}
		else
			return value;
	}
	
	function removeArrayFunction (myObjects,prop,valu)
	{
		return myObjects.filter(function (val) {
			return val[prop] !== valu;
		});
	}
	
	function readArrayFunction (myObjects,prop,valu)
	{
		var newary = myObjects.reverse();
		return newary.filter(function (val) {
		  return val[prop] == valu;
	  });
	}
	
	function validate_password_rule(str)
	{
		var r1= 8;// mini length 6
		var r2=/[a-zA-Z]/;  //One Char
		var r3=/[0-9]/;  //One Digit
		var r4=/[!@$%^*]/;  //One special=> char whatever you mean by 'special char'
		if (str.length < r1) {
			return false;//("too_short");
		} else if (str.search(r2) == -1) {
			return false;//("no_letter");
		} else if (str.search(r3) == -1) {
			return false;//("no_num");
		} else if (str.search(r4) == -1) {
			return false;//("no_special_char");
		}
		return true;
	}

	function create_from_to_datetimepicker(fromdate_id, todate_id, page_name, cookie_name_arr)
	{
		for(val in cookie_name_arr)
		{
			date_value = $('#' + cookie_name_arr[val] ).val();
			if(cookie_name_arr[val] == "cri_txt_todate")
			{
				if(date_value != '')
					var setToDate = new Date(date_value.split("-").reverse().join("-"));
				else 
					var setToDate = new Date();
				
				//console.log(setToDate);
				$('#'+ fromdate_id).data('datetimepicker').maxDate(setToDate);
				$('#'+ fromdate_id).data('datetimepicker').format('DD-MM-YYYY');
				$('#'+ fromdate_id).on("change.datetimepicker", function (e) {
					$('#'+todate_id).data("datetimepicker").minDate(e.date);
				});
			}
			else if(cookie_name_arr[val] == "cri_txt_fromdate")
			{
				if(date_value != '')
					var setFromDate = new Date(date_value.split("-").reverse().join("-"));
				else 
					var setFromDate = new Date();
				
				//console.log(setFromDate);
				$('#'+ todate_id).data('datetimepicker').minDate(setFromDate);
				$('#'+ todate_id).data('datetimepicker').format('DD-MM-YYYY');
				$('#'+ todate_id).on("change.datetimepicker", function (e) {
					$('#'+fromdate_id).data("datetimepicker").maxDate(e.date);
				});
			}
		}
	}
	
	 /*
     * Fileupload control to save at private temp folder.
     * All uploaded files will be save as temp files.
	 * At server side script, it will need to move to correct path. 
	 * Don't need to use full file path and full folder path
	 * After to select to file at file control, it will upload at temp folder. It will save only file name list at hidden field.
	 * When submit to form, it will post file name list at hidden field. Other action will do at server side.
	 * To delete old files, it will save only file name list at hidden field.
	 * When submit to form, it will post delete file name list at hidden field. Other action will do at server side.
	 * 
     * @param	string	fdivid      		div id for filecontrol name
     * @param	string	oldfile_list		phpthumb old files list to show at file control
	 * @param	string	download_list		download list with show_data_content.php
	 * @param	string	movepath			path to show for icon at file control
	 * @param	boolean	overwrite_existing	new upload preview image will be overwrite to existing preview at file control
	 * @param	string	folder_name			temp folder name to store uploaded temp files
	 * @param	int		maxfilecount		Max files count for filecontrol
	 * @param	int		maxfilesize			Max file size bytes for upload temp files 
	 * @param	array	allowedext			Allowed file extension for filecontrol. eg., ["jpg", "jpeg", "png", "gif", "pdf", "mp4"]
	 * @param	boolean	hidethumbnail		show or hide preview thumbnail at filecontrol
	 * @param	string	showtheme			theme style for filecontrol. now support two theme. 'fa' or 'explorer' ['fa' will show big preview , 'explorer' will show as list view]
	 * @param	string	upload_foldertype	folder type to upload 
	 * @param	string	folder_token		token no for folder to verify folder
	 *
     */
	if(typeof TokenAllowFileUpload !== "undefined") TokenAllowFileUpload = false;
	var gwt_fileinput=[];
	function file_upload_image_upd(fdivid, oldfile_list,download_list,movepath,overwrite_existing,folder_name,maxfilecount,maxfilesize,allowedext,hidethumbnail,showtheme,upload_foldertype, folder_token)
	{
		var fid = fdivid.replace("#","");
		var gwt_file_obj = {inputname: fid , max_file_count:maxfilecount ,total_file_count:0, uploaded_files_ary:[] , old_files_ary:[''] , old_files_ary_caption:[{caption: "", fname:"" , key: 0}] }; 
		var tmpfoundary = readArrayFunction(gwt_fileinput,"inputname", fid.trim());
		if(tmpfoundary.length > 0){
			var tmp_removedarray = removeArrayFunction(gwt_fileinput,"inputname", fid.trim() );
			tmp_removedarray.push(gwt_file_obj);
			gwt_fileinput = tmp_removedarray;
		}else{
			gwt_fileinput.push(gwt_file_obj);
		}
		
		var tmpgwtfile={};
		var tmpinputary = readArrayFunction(gwt_fileinput,"inputname", fid.trim());
		if(tmpinputary.length < 0){
			return false;
		}
		tmpgwtfile=tmpinputary[0];
	
		if(hidethumbnail == undefined) hidethumbnail = false;
		if(showtheme == undefined) showtheme = "fa";
		if(allowedext == undefined) allowedext = ["jpg", "jpeg", "png", "gif"];
		if(upload_foldertype == undefined) upload_foldertype = "";
		if(folder_token == undefined) folder_token = "";
		folder_name = (folder_name=="" || folder_name==undefined)? "" : folder_name;
		
		//to set custom file type at browser filecontrol
		if(allowedext.length > 0){
			var accepttypestr="";
			for(var exi = 0; exi < allowedext.length; exi += 1){
				var acctype = "";
				var exvalue = allowedext[exi];
				if(exvalue=="jpg" || exvalue=="jpeg" || exvalue=="png" || exvalue=="gif")
					acctype = "image/"+exvalue;
				else if(exvalue=="pdf" )
					acctype = "application/pdf";
				else if(exvalue=="mp4" )
					acctype = "video/mp4";
				accepttypestr += (accepttypestr=="")? acctype: ","+acctype;
			}
			$(fdivid).attr("accept", accepttypestr);	
		}
		
		//prepare array to set for filecontrol
		var oldfileslist = "";
		if(oldfile_list!=""){
			var data = oldfile_list.split(',');
			tmpgwtfile.total_file_count = data.length;
			var i=0;
			for(var datai = 0; datai < data.length; datai += 1){
				i=i+1;
				var datastr = data[datai]; 
				var dimg_path = datastr.substring(datastr.search('=')+1, datastr.search('&'));
				dimg_path=decodeURIComponent(dimg_path);
				var bkindex = (dimg_path.lastIndexOf('/'));
				var filename = dimg_path.substr(bkindex+1);
				oldfileslist += (oldfileslist=="")? filename: ","+filename;	 
				if(download_list==""){
					tmpgwtfile.old_files_ary_caption.push( {caption: filename , fname: filename , key: datastr } );
					tmpgwtfile.old_files_ary.push(datastr);
				}else{
					var dwndata = download_list.split(',');
					var dlink = dwndata[datai]; 
					 var fexvalue = filename.substring(filename.lastIndexOf('.')+1, filename.length) || filename;
					if(fexvalue=="jpg" || fexvalue=="jpeg" || fexvalue=="png" || fexvalue=="gif"){
						tmpgwtfile.old_files_ary_caption.push( {caption: filename , fname: filename , key: dlink+'', downloadUrl: dlink} );
						tmpgwtfile.old_files_ary.push(datastr);
					}else{
						if(fexvalue=="mp4"){ 
							tmpgwtfile.old_files_ary_caption.push( {type:'video', filetype: "video/mp4", caption: filename , fname: filename , key: dlink+'', downloadUrl: dlink} );
						}else if(fexvalue=="pdf"){ 
							tmpgwtfile.old_files_ary_caption.push( {type:'pdf', caption: filename , fname: filename , key: dlink+'', downloadUrl: dlink} );
						}else{
							tmpgwtfile.old_files_ary_caption.push( {caption: filename , fname: filename , key: dlink+'', downloadUrl: dlink} );
						}
						tmpgwtfile.old_files_ary.push(dlink);
					}
				}
			}
		}

		var actSetting = {};
		if(download_list==""){
			actSetting = {
				removeClass:'btn btn-sm btn-outline-success',
				uploadClass:'btn btn-sm btn-outline-success',
				zoomClass:'btn btn-sm btn-outline-success',
				removeIcon:'<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-trash" /></svg>',
				uploadIcon:'<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-upload" /></svg>',
				zoomIcon: '<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-zoom-in" /></svg>',
				indicatorNew: '<svg class="icon i-xs align-middle text-warning"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-hand-down" /></svg>'
			}
		}else{
			actSetting ={
				downloadIcon: '<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-cloud-download" /></svg>',
				downloadClass: 'btn btn-sm btn-outline-success',
				downloadTitle: 'Download file',
				removeClass:'btn btn-sm btn-outline-success',
				uploadClass:'btn btn-sm btn-outline-success',
				zoomClass:'btn btn-sm btn-outline-success',
				removeIcon:'<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-trash" /></svg>',
				uploadIcon:'<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-upload" /></svg>',
				zoomIcon: '<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-zoom-in" /></svg>',
				indicatorNew: '<svg class="icon i-xs align-middle text-warning"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-hand-down" /></svg>'
			}
		}
	
		//to set for other actions icons
		var btnicon='<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-link" /></svg>';
		var btns = '<button data-toggle="modal" data-target="#modal-delete"  type="button" class="kv-cust-btn btn btn-sm btn-outline-success" title="Show File URL" {dataKey} data-caption="{caption}" >' +
		''+btnicon+'' +
		'</button>';

		//start load filecontrol
		$(fdivid).fileinput({
			otherActionButtons: btns,
			hideThumbnailContent: hidethumbnail, // hide image, pdf, text or other content in the thumbnail preview
			theme: showtheme, //"fa" or "explorer"
			uploadUrl: "library/upload_temp_file.php", // your upload server url
			uploadExtraData: {fdiv:fid , rnd: folder_name, upload_foldertype: upload_foldertype, folder_token: folder_token},
			uploadAsync: false,
			autoReplace: true,
			overwriteInitial: overwrite_existing ,
			showUploadedThumbs: false,
			initialPreview: tmpgwtfile.old_files_ary ,
			initialPreviewAsData: true,
			initialPreviewConfig: tmpgwtfile.old_files_ary_caption, 
			deleteUrl: "library/fileinput_allowremove.php", //this link will use only for delete request. no need to do delete action at fileinput_allowremove.php
			initialPreviewFileType: 'image', // image is the default and can be overridden in config below
			browseLabel: "Select File",
			browseClass: "btn btn-sm btn-outline-info",
			browseIcon: '<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-photo" /></svg>',
			fileActionSettings: actSetting,
			previewZoomButtonIcons: {
				toggleheader: '<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-resize-vertical" /></svg>',
				fullscreen: '<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-fullscreen" /></svg>',
				borderless: '<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-resize-full" /></svg>',
				close: '<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+movepath+'js/symbol-defs.svg#icon-cancel" /></svg>'
			},
			cancelClass: "outline-secondary",
			dropZoneEnabled: false,
			showPreview: true,
			//initialPreviewAsData: true,
			showCaption: false,
			showUpload: false, // hide upload button
			showRemove: false, // hide remove button
			showClose: false,
			showCancel : false,
			//showDownload : false,
			maxFileCount: maxfilecount,
			maxFileSize: maxfilesize, //unit in KB eg., 2MB=2000, 100MB=100000	
			allowedFileExtensions: allowedext
		
		}).on('fileselect', function(event, numFiles, label) {
			 $(':input[type="submit"]').prop('disabled', true);
			if(typeof TokenAllowFileUpload !== "undefined") TokenAllowFileUpload = false;
		}).on('custom-event', function(event) {
		}).on('filecustomerror', function(event, data) {
		}).on('fileerror', function(event, data, msg) {
		}).on('filebatchselected', function(event, files) {
			if(typeof TokenAllowFileUpload !== "undefined") TokenAllowFileUpload = true;
			$(fdivid).fileinput('upload');
		}).on('filebatchpreupload', function(event, data, previewId, index, jqXHR) {
			if(overwrite_existing){ //check for single file upload
				console.log(allowedext);
				for(var datai = 0; datai < data.filenames.length; datai += 1){
					// do your validation and return an error like below
					var found =  data.filenames[datai];
					if (found == undefined) {
					   return {
						   message: 'Invalid extension for file. Only '+ allowedext.join(', ') +' files are supported. Please remove unsupported file.',
						   data: {key1: 'Key 1', detail1: 'Detail 1'}
					   };
				   }
				}
			}
			if(data.filescount <=0){
				return {
						   message: 'Invalid extension for file. Only '+ allowedext.join(', ') +' files are supported. Please remove unsupported file.',
						   data: {key1: 'Key 1', detail1: 'Detail 1'}
					   };
			}
		   
		}).on('filebatchuploadsuccess', function(event, params) {
			if(typeof TokenAllowFileUpload !== "undefined") TokenAllowFileUpload = false;

			var data = params.response;
			var img_path = "";				
			if(Array.isArray(data)){
				var tmpgwtfile={};
				var tmpinputary = readArrayFunction(gwt_fileinput,"inputname", event.target.id.trim());
				if(tmpinputary.length < 0){
					return false;
				}
				tmpgwtfile=tmpinputary[0];
				
				tmpgwtfile.total_file_count = tmpgwtfile.total_file_count + data.length;
				 for(var datai = 0; datai < data.length; datai += 1){
					var dataobj = data[datai]; 
					var tmpfoundary = readArrayFunction(tmpgwtfile.uploaded_files_ary,"name", dataobj.name.trim());
					if(tmpfoundary.length > 0){
						var tmppath = tmpfoundary[0].path;
						var tmp_removedarray = removeArrayFunction(tmpgwtfile.uploaded_files_ary,"name", dataobj.name.trim());
						tmp_removedarray.push(dataobj);
						tmpgwtfile.uploaded_files_ary = tmp_removedarray;
					}else{
						tmpgwtfile.uploaded_files_ary.push(dataobj);
					}
				 }
				 
			}else{
				 alert("Fail to upload, please remove unsupported files.");
			}
			
			jQuery("#"+ event.target.id.trim()+"_hidfileinputpath").attr("value", img_path);
		}).on('fileuploaded', function(event, params) {
			//now not use , it will use for , upload icon clicked from preview
			var data = params.response;
			if(Array.isArray(data)){
				$(':input[type="submit"]').prop('disabled', false);
			
				var tmpgwtfile={};
				var tmpinputary = readArrayFunction(gwt_fileinput,"inputname", event.target.id.trim());
				if(tmpinputary.length < 0){
					return false;
				}
				tmpgwtfile=tmpinputary[0];
					
				tmpgwtfile.total_file_count = tmpgwtfile.total_file_count + data.length;
				 for(var datai = 0; datai < data.length; datai += 1){
					var dataobj = data[datai]; 
					var tmpfoundary = readArrayFunction(tmpgwtfile.uploaded_files_ary,"name", dataobj.name.trim());
					if(tmpfoundary.length > 0){
						var tmppath = tmpfoundary[0].path;
						var tmp_removedarray = removeArrayFunction(tmpgwtfile.uploaded_files_ary,"name", dataobj.name.trim());
						tmp_removedarray.push(dataobj);
						tmpgwtfile.uploaded_files_ary = tmp_removedarray;
					}else{
						tmpgwtfile.uploaded_files_ary.push(dataobj);
					}
				 }
					
				var tmp_removedarray = [];
				var img_path = "";
				var successlist = $(fdivid).fileinput('getFrames', '.file-preview-success');
				 for(var datai = 0; datai < successlist.length; datai += 1){
					var datastr = successlist[datai]; 
					var fname = datastr.outerText.replace(/(\r\n|\n|\r)/gm,"");
					var bkindex = (fname.indexOf('('));
					var fileExtension = fname.substr(0, bkindex);
					if(datastr.id != undefined){
						var tmpobj = readArrayFunction(tmpgwtfile.uploaded_files_ary,"name", fileExtension.trim());
						if(tmpobj.length > 0){
							var tmppath = tmpobj[0].path;
							img_path += (img_path=="")? tmppath: ","+tmppath;
						}
					}
				 }
				jQuery("#"+ event.target.id.trim()+"_hidfileinputpath").attr("value", img_path);
			}else{
				 alert("Fail to upload, please remove unsupported files.");
			}

		}).on('filebatchuploadcomplete', function(event, files, extra) {
			if(typeof TokenAllowFileUpload !== "undefined") TokenAllowFileUpload = false;
		
			$(':input[type="submit"]').prop('disabled', false);
			var tmpgwtfile={};
			var tmpinputary = readArrayFunction(gwt_fileinput,"inputname", event.target.id.trim());
			if(tmpinputary.length < 0){
				return false;
			}
			tmpgwtfile=tmpinputary[0];
				
			var tmp_removedarray = [];
			var img_path = "";
			var successlist = $(fdivid).fileinput('getFrames', '.file-preview-success');
			 for(var datai = 0; datai < successlist.length; datai += 1){
				var datastr = successlist[datai]; 
				var fname = datastr.outerText.replace(/(\r\n|\n|\r)/gm,"");
				var bkindex = (fname.indexOf('('));
				var fileExtension = fname.substr(0, bkindex);
				if(datastr.id != undefined){
					var tmpobj = readArrayFunction(tmpgwtfile.uploaded_files_ary,"name", fileExtension.trim());
					if(tmpobj.length > 0){
						var tmppath = tmpobj[0].path;
						img_path += (img_path=="")? tmppath: ","+tmppath;
					}
				}
			 }
			jQuery("#"+ event.target.id.trim()+"_hidfileinputpath").attr("value", img_path);

			$('.kv-cust-btn').on('click', function() {
				var $btn = $(this), key = $btn.data('key');
				// do some actions based on the key
				var msg='It can not get Image URL. Please save this file.';
				if(key != undefined) msg=key;
				insert_img_url_popup(msg,'modal-delete');
			}); 
		}).on('filecleared', function(event) {
			jQuery("#"+ event.target.id.trim()+"_hidfileinputpath").attr("value", "");
		}).on('filedeleted', function(event, key, jqXHR, data) {
			//This is used for delete to old files 
			var prevdel=$("#"+ event.target.id.trim()+'_hiddelfileslist').val();
			var tmpgwtfile={};
			var tmpinputary = readArrayFunction(gwt_fileinput,"inputname", event.target.id.trim());
			if(tmpinputary.length < 0){
				return false;
			}
			tmpgwtfile=tmpinputary[0];
			
			var tmpinputcapary = readArrayFunction(tmpgwtfile.old_files_ary_caption,"key", key);
			if(tmpinputcapary.length < 0){
				return false;
			}
			var objdel = tmpinputcapary[0];
			prevdel += (prevdel=="")? objdel.fname : ","+objdel.fname;
			$("#"+ event.target.id.trim()+'_hiddelfileslist').val(prevdel);
			tmpgwtfile.total_file_count = tmpgwtfile.total_file_count - 1;
			if(overwrite_existing){ //remove for single file upload
				jQuery("#"+ event.target.id.trim()+"_hidfileinputpath").attr("value", "");
			}
			
		}).on('filesuccessremove', function(event, deletedid) {
			var tmpgwtfile={};
			var tmpinputary = readArrayFunction(gwt_fileinput,"inputname", event.target.id.trim());
			if(tmpinputary.length < 0){
				return false;
			}
			tmpgwtfile=tmpinputary[0];
			
			//This is used for delete to new temp files 
			var tmp_removedarray = [];
			var img_path = "";
			var successlist = $(fdivid).fileinput('getFrames', '.file-preview-success');
			 for(var datai = 0; datai < successlist.length; datai += 1){
				var datastr = successlist[datai]; 
				var fname = datastr.outerText.replace(/(\r\n|\n|\r)/gm,"");
				var bkindex = (fname.indexOf('('));
				var fileExtension = fname.substr(0, bkindex);
				if(datastr.id != deletedid){
					var tmpobj = readArrayFunction(tmpgwtfile.uploaded_files_ary,"name", fileExtension.trim());
					if(tmpobj.length > 0){
						var tmppath = tmpobj[0].path;
						img_path += (img_path=="")? tmppath: ","+tmppath;
					}
				}
			 }
			tmpgwtfile.total_file_count = tmpgwtfile.total_file_count - 1;
			jQuery("#"+ event.target.id.trim()+"_hidfileinputpath").attr("value", img_path);
		}).on('fileselectnone', function(event) {
		}).on('fileclear', function(event) {
		}).on('filezoomshow', function(event, params) {
		});
				 
		$('.kv-cust-btn').on('click', function() {
			var $btn = $(this), key = $btn.data('key');
			// do some actions based on the key
			insert_img_url_popup(key,'modal-delete');
		}); 
	}
		
	function insert_img_url_popup(label, dialog_id)
	{
		var str = '<div class="modal fade modaldiv animated bounceInDown" id="'+dialog_id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">\
			<div class="modal-dialog">\
				<div class="modal-content rounded-0">\
					<div class="modal-body text-center py-5">\
						<svg class="icon i-lg text-warning"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-notification" /></svg>\
						<h4 class="modal-title my-2" id="myModalLabel">File URL</h4>\
						<form class="my-3" role="form" method="post" enctype="multipart/form-data" action="">\
							<div><textarea  class="js-copytextarea" style="width:100%" id="insert_img_url" name="insert_img_url" /> </div>\
						</form>\
						<button type="button" class="btn btn-success" data-dismiss="modal" onclick="copytextboxdata(\'.js-copytextarea\')">Copy URL</button>\
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>\
					</div>\
				</div>\
			</div>\
		</div>';
		$('body').append(str);	
		$('#insert_img_url').val(label);
	}
	
	function copytextboxdata(txtbox){
		  try {
			var copyTextarea = document.querySelector(txtbox);
			copyTextarea.select();
			var successful = document.execCommand('copy');
			var msg = successful ? 'successful' : 'unsuccessful';
			//console.log('Copying text command was ' + msg);
		  } catch (err) {
			//console.log('Oops, unable to copy');
		  }
	}