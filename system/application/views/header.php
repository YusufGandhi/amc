<!DOCTYPE html>
<html>
<head>
	<title>Angsamerah Company Information System<?php echo (isset($title)? (" - ".$title):"") ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $baseURL ?>css/ui-darkness/jquery-ui-1.8.7.custom.css" />
	<script src="<?php echo $baseURL ?>js/jquery-1.4.4.min.js" type="text/javascript"></script>
	<script src="<?php echo $baseURL ?>js/jquery-ui-1.8.7.custom.min.js" type="text/javascript"></script>
	<?php if (isset($form_validator) && $form_validator == TRUE) { ?> <script src="<?php echo $baseURL ?>js/jquery.validate.js" type="text/javascript"></script> <?php } ?>
	<?php if (isset($auto_grow) && $auto_grow == TRUE) { ?> <script src="<?php echo $baseURL ?>js/jquery.autogrowtextarea.js" type="text/javascript"></script><?php } ?>
	<!--<script src="<?php echo $baseURL ?>scripts/jquery.js" type="text/javascript"></script>-->
	<link rel="stylesheet" type="text/css" href="<?php echo $baseURL ?>master.css" />
	
	<!-- testing for the chat app -->
	<script src="<?php echo $baseURL ?>js/jquery.ui.chatbox.js" type="text/javascript"></script>
	<script src="<?php echo $baseURL ?>js/chatboxManager.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $baseURL ?>css/jquery.ui.chatbox.css" />
	<!-- end of testing script -->
	
	
	<script type="text/javascript">
		$(document).ready( function() {
			
			var get_first_name = function(name) {
				var x = name.split(" ");
				return (x[0] == 'dr.' ? x[1]  : x[0]);
			};
			
			var username = get_first_name('<?php echo $this->session->userdata('name') ?>');//(name_array[0] == 'dr.' ? name_array[1]  : name_array[0]);
			/*
			 * NEW FEATURE-2013: CHAT BOX
			 */
			 
			 // script is taken from http://magma.cs.uiuc.edu/wenpu1/chatbox.html#
			var last_message_id;
			$.get("<?php echo site_url('chat/get_last_message_id') ?>", {}, function(data) {
				last_message_id = data;
			});
			
			var check_message = function() {
				$.ajax({
					url: '<?php echo site_url('chat/check_message') ?>',
					type: 'POST',
					data: 'id=<?php echo $this->session->userdata('id') ?>&last_message_id=' + last_message_id,
					dataType: 'json',
					success: function(response) {
								if(response.response == 'YES') {
									var user_id = response.message.sender_id;
									var box_id = "box" + user_id;
									chatboxManager.addBox(box_id,
															{
															  id: username,
															  user:{key : "value"},																 
															  title : response.message.sender_name,
															  messageSent : function(id, user, msg) {
																
																$.ajax({
																	url: '<?php echo site_url('chat/save') ?>',
																	type: 'POST',													
																	data: 'message=' + msg + '&sender_id=' + <?php echo $this->session->userdata('id') ?> + '&receiver_id=' + user_id,
																	success: function(response) {
																					if (response != "False") {
																						$("#box"+user_id).chatbox("option", "boxManager").addMsg(username, msg);
																						last_message_id = response;
																					}
																			 }
																});
															  }
															});
									$("#box"+user_id).chatbox("option", "boxManager").addMsg(get_first_name(response.message.sender_name), response.message.content)
									$("div#box"+user_id+".ui-widget-content.ui-chatbox-log").css("color","black");
									last_message_id = response.message.id;
								}
							}
					
				});
			};
			
			var check_new_message = function(group_id) {
				if(last_message_id > 0) {
					$.ajax({
						url: '<?php echo site_url('chat/check_new_message') ?>',
						type: 'POST',
						data: 'group_id=' +group_id+ '&last_message_id=' +last_message_id,
						dataType: 'json',
						success: function(response) {
										if(response.response == 'YES') {
											$.each(response.message, function(idx, data) {
												$("#chat_div").chatbox("option", "boxManager").addMsg(data.sender_id, data.content);
											});
											last_message_id = response.last_id;
										}
								 }		
					
					});
				}
			};
			
			var check_online_users = function() {
				$.ajax({
					url: '<?php echo site_url('chat/check_online_users/'.$this->session->userdata('id')) ?>',
					type: 'POST',
					data: 'id=<?php echo $this->session->userdata('id') ?>',
					dataType: 'json',
					success: function(response) {
								$('#online-users').find('div#online-username').remove();
								if(response.response == 'YES') {
									$.each(response.message, function(idx, data) {
										$("#online-users").append("<div id='online-username' user='"+data.id+"'>"+data.name+"</div>");
									});
									$("div#online-username")
									.hover(
										function() {
											$(this).css('background-color','blue').css('color','white').css('cursor','pointer');
											//alert("hover");
										},
										function() {
											$(this).css('background-color','white').css('color','black');
										}
									)
									.click( function() {
										var user_id = $(this).attr('user');
										var box_id = "box" + user_id;
										chatboxManager.addBox(box_id,
																{
																  id: username,
																  user:{key : "value"},																 
																  title : $(this).html(),
																  messageSent : function(id, user, msg) {
																	
																	
																	$.ajax({
																		url: '<?php echo site_url('chat/save') ?>',
																		type: 'POST',													
																		data: 'message=' + msg + '&sender_id=' + <?php echo $this->session->userdata('id') ?> + '&receiver_id=' + user_id,
																		success: function(response) {
																						if (response != "False") {
																							$("#box"+user_id).chatbox("option", "boxManager").addMsg(username, msg);
																							last_message_id = response;
																						}
																				 }
																	});
																  }
																});
										$("div#box"+user_id+".ui-widget-content.ui-chatbox-log").css("color","black");
									});
								}
							 }
				});
				
			};
			
			// this line is for retrieving (refreshing the script every 2 seconds
			// to check new messages
			setInterval(function() {
				check_message();
			}, 2000);
			
			check_online_users();
			
			
			
			// END OF CHATBOX FEATURE
		
		
		<?php if ($this->session->userdata("station") == '2') : ?>
				
				<?php if(isset($exam)) : ?>
				var total_tindakan = total_lab = total_obat = 0;				
				
				$("input[type='checkbox'][class='tindakan']").click( function() {
					total_tindakan = 0;
					$("input[type='checkbox'][class='tindakan']:checked").each( function () {
						total_tindakan += parseInt($(this).val());
					});					
					$("#price_tindakan").val(total_tindakan);
				});
				
				$("input[type='checkbox'][class='lab']").click( function() {
					
					// IF THE CHECKED GROUP WAS 
					if($(this).is(':checked') && ($(this).attr('group') != '') ) {
						//if ( $(this).attr('group') != '' )
							$("input[type='checkbox'][class='lab'][group="+ $(this).attr('group') +"]").attr('checked','true');													
					} else if ($(this).attr('group') != '')  {
						//if ( $(this).attr('group') != '' )
							$("input[type='checkbox'][class='lab'][group="+ $(this).attr('group') +"]").attr('checked','');							
					}
					total_lab = 0;
					$("input[type='checkbox'][class='lab']:checked").each( function () {
						total_lab += parseInt($(this).val());
					});
					$("#price_lab").val(total_lab);
				});
				
				// UPDATE JANUARY 5, 2012
				// WHEN PAPSMEAR PROCEDURE IS CLICKED,
				// IT WILL AUTOMATICALLY GENERATE THE PARAMITA LAB CHECK
				$("input[type='checkbox'][name='tindakan[6]']").click( function() {
					if($(this).is(':checked')) {
						ajax_pramita('Pap');
					} else {
						// PARAMITA LAB CHECK ID FOR PAPSMEAR = 277
						// THAT'S WHY THE ID FOR LI IS 277 BELOW
						$("ul#item_list_paramita > li#277").remove();
					}
				});
				
				// END OF UPDATE JAN 5, 2012
				
				$("input[type='checkbox'][class='obat']").click( function() {
					total_obat = 0;
					$("input[type='checkbox'][class='obat']:checked").each( function () {
						total_obat += parseInt($(this).val());
					});
					$("#price_obat").val(total_obat);
					
					// FUNCTION TO SHOW OR HIDE THE DOSAGE AND AMOUNT OF MEDICINE
					// CHECK WHETHER THE CHECKBOX IS CHECKED OR NOT
					if ($(this).is(":checked")) {
						$("input[type='text'][name='dosis["+ $(this).attr('alt') +"]']").show().addClass("required");
						$("input[type='text'][name='amount["+ $(this).attr('alt') +"]']").show().addClass("required digit").focus();
					} else {
						$("input[type='text'][name='dosis["+ $(this).attr('alt') +"]']").hide().removeClass("required").val("");
						$("input[type='text'][name='amount["+ $(this).attr('alt') +"]']").hide().removeClass("required digit").val("");
					}
					//alert($(this).is(":checked") + " " + $(this).val());
				});
				
				$('input#next_appointment').click( function() {
					$('div#next_app_details').toggle('slow');
					$("select#dd_list_hour_start").toggleClass("required");
					$("select#dd_list_hour_end").toggleClass("required");
					$("select#dd_list_nurse").toggleClass("required");
				});
				
				$("#schedule").load("<?php echo $baseURL ?>index.php/station_2/generate_doctor_schedule", {doctor_id: $("input#id_doctor").val(), date: "<?php echo date("Y-m-d") ?>"});
				//$("#schedule").html("<span style=\"font-size:26px;color:#bababa;text-align:center;\"><br /><br />Select the schedule<br />on the left side<br />to see the available schedule</span>");
				
				$("#datepicker").datepicker({
					onSelect: function(dateText, inst) {
									$("input#date").val(dateText);
									$("select#dd_list_hour_start").val("");
									$("select#dd_list_hour_end").val("");
									show_schedule();								
							  },
					dateFormat: 'yy-mm-dd',
					changeMonth: true,
					changeYear: true,
					minDate: 0
				});
				
				function show_schedule() {
					var timeOut;
					clearTimeout(timeOut);
					var active_date = $("input#date").val();
					var doctor_id = $("input#id_doctor").val();
					$("#schedule").fadeOut("normal");
					
					// **** TIMEOUT IS USED SO IT GIVES TIME FOR DIV TO DISAPPEAR FIRST AND IT FADES IN WITH ACTUAL CONTENT
					timeOut = setTimeout(function() {$("#schedule").load("<?php echo $baseURL ?>index.php/station_2/generate_doctor_schedule", {doctor_id: doctor_id, date: active_date}, function() {					
						$(this).fadeIn("normal");
					});}, 250);
				}
				
				$("select#dd_list_hour_start").change( function() {
					$(".td-selected").removeClass("td-selected");
					var start = parseInt($(this).val());
					var end = parseInt($("select#dd_list_hour_end").val());
					//alert(end);
					var room = parseInt($("select#dd_list_room").val());
					//var check;
					if ( start > end || isNaN(end)) {				
						$("select#dd_list_hour_end").val(start);
						end = start;
					}
					highlight_schedule(start, end, room);
					
					
				});			
				
				$("select#dd_list_hour_end").change( function() {
					$(".td-selected").removeClass("td-selected");
					var start = parseInt($("select#dd_list_hour_start").val());
					var end = parseInt($("select#dd_list_hour_end").val());
					var room = $("select#dd_list_room").val();
					if ( start > end ) {
						$("select#dd_list_hour_start").val(end);
						start = end;
					}
					highlight_schedule(start, end, room);										
				});
				
				$("select#dd_list_room").change( function() {
					if ( $("select#dd_list_hour_start").val() != 0 ) {
						$(".td-selected").removeClass("td-selected");
						var start = parseInt($("select#dd_list_hour_start").val());
						var end = parseInt($("select#dd_list_hour_end").val());
						if ( start <= end ) {
							var room = $(this).val();
							highlight_schedule(start, end, room);					
						}
					}
				});
				
				function check_doctor_schedule(div) {
					if (!($(div).length))
						return false;
					return true;
				}
				
				function highlight_schedule(start, end, room) {
					var div;
					var result;
					for (var i = start; i <= end ; i++) {
						div = "#sch_"+i+"-"+room;
						result = check_doctor_schedule(div);
						if (result) 
							$(div).parent("td").addClass("td-selected");
						else {
							$(".td-selected").removeClass("td-selected");
							alert("Time is not available");
							$("select#dd_list_hour_start").val("");
							$("select#dd_list_hour_end").val("");
							break;
						}					
					}
					
				}
				
				$("input#search_item_paramita").autocomplete({
					source: 
							function(req, add)
							{
								$.ajax({
									url: '<?php echo $baseURL ?>index.php/station_2/source_paramita',
									dataType: 'json',
									type: 'POST',
									data: req,
									success:    
										function(data)
										{
											if(data.response =='true')
											{
												add(data.message);										
											} else {
												$("input#search_item_paramita").removeClass('ui-autocomplete-loading');
												$("ul.ui-autocomplete").hide();
											}
										}
								});
							},	
					minLength: 1,
					select: 
						function(event,ui) {
							// CHECK WHETHER THE ITEM HAS BEEN CHOSEN OR NOT
							// IF HAS BEEN CHOSEN PREVIOUSLY, THEN REJECT THE SELECTION
							if ( $("ul#item_list_paramita input#paramita_price[name$='["+ ui.item.id +"]']").length == 0 ) {
								$("ul#item_list_paramita").append("<li id='" + ui.item.id +"'>" + ui.item.label +" (" + ui.item.klasifikasi + ") <a class='hide button' alt='Remove item' href='javascript:' onClick=\"$(this).parent().remove();$('#total_price_paramita').val( parseInt($('#total_price_paramita').val()) - parseInt($(this).siblings('input#paramita_price').val()) );if($(this).parent().attr('id')=='277') $('input#tindakan6').attr('checked',false);\">x</a> <input type='hidden' id='paramita_price' name='paramita_price["+ ui.item.id +"]' value='" + ui.item.harga + "' /></li>");
								
								// addition JANUARY 5, 2012
								// IF PAPSMEAR CHOSEN, PROCEDURE PAPSMEAR MUST BE TICKED
								if( ui.item.id == "277" ) {
									$("input[type='checkbox'][name='tindakan[6]']").attr("checked","checked");
								}
								// ** end of addition
								
								$('ul#item_list_paramita > li').bind({
									mouseenter: function() {
										$(this).children('a').removeClass('hide');
										//alert($(this));
									},
									mouseleave: function() {
										$(this).children('a').addClass('hide');
									}
								});
								$(this).val("");
								$('#total_price_paramita').val( parseInt($('#total_price_paramita').val()) + parseInt(ui.item.harga) );
							} else {
								$(this).next('span').html(ui.item.label + ' has been chosen' ).css('display','inline').fadeOut(3500);
								$(this).val("");
							}
							return false;
						}
					}).data( "autocomplete" )._renderItem = function( ul, item ) {
						// The function for rendering the item shown in the suggestion list
						// alter the ".append" for custom message
						return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a><span style=\"font-weight:bold;\">" + item.label + "</span><br />" + item.klasifikasi + "</a>" )
						.appendTo( ul );
				};
				
				$('#specimen_accordion').accordion({
					icons: false
				});
				
				$('input.paket').click( function() {
					reset_lab_proc();
					$("#package-price").val($(this).attr("title"));
					$("#package-id").val($(this).attr('alt'));
					switch ($(this).attr('alt')) {
						case "1":							
							$("input[name='lab[1]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[2]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[3]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[5]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='tindakan[1]']").attr("checked","true").attr("value","0").parent("p").hide();							//
							break;
						case "2":
							// GO LIST STARTS FROM HERE DIPLO + PMN FOR FEMALE
							$("input[name='lab[13]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[14]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[15]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[16]']").attr("checked","true").attr("value","0").parent("p").hide();
							// GO LIST ENDS HERE
							
							$("input[name='lab[17]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[18]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[19]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[20]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[21]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='tindakan[2]']").attr("checked","true").attr("value","0").parent("p").hide();
							
							break;
						case "3":
							$("input[name='lab[13]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[14]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[15]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[16]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[17]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[18]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[19]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[20]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[21]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='tindakan[6]']").attr("checked","true").attr("value","0").parent("p").hide();
							ajax_pramita("Pap");							
							break;
						case "4":
							$("input[name='lab[6]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[7]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[10]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='tindakan[1]']").attr("checked","true").attr("value","0").parent("p").hide();
							break;
						case "5A":
							$("input[name='lab[1]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[2]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[3]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[5]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[11]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[12]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[13]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[14]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='tindakan[1]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='tindakan[5]']").attr("checked","true").attr("value","0").parent("p").hide();
							break;
						case "5B":
							$("input[name='lab[1]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[2]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[3]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[5]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[13]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[14]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[15]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[16]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[17]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[18]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[19]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[20]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[21]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='tindakan[1]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='tindakan[6]']").attr("checked","true").attr("value","0").parent("p").hide();
							ajax_pramita("Pap");
							break;
						case "6":
							$("input[name='lab[1]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[2]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[3]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[5]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[13]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[14]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[15]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[16]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[17]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[18]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[19]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[20]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='lab[21]']").attr("checked","true").attr("value","0").parent("p").hide();
							$("input[name='tindakan[1]']").attr("checked","true").attr("value","0").parent("p").hide();
							//$("input[name='tindakan[6]']").attr("checked","true").attr("value","0").parent("p").hide();
							//ajax_pramita("Pap");
							break;
							//*/
							
					}
					$('#paket_accordion').hide('normal');
					$('#package-chosen').html('<H4 STYLE="color:red">PACKAGE #' + $(this).attr('alt') +"</H4>");
					$('#change_paket').show();
				});
				
				$('#change_paket').click( function() {
					reset_lab_proc();
					$('#paket_accordion').show('normal');
					$('#package-chosen').html("");
					$(this).hide();
				});
				
				$('#paket_accordion').accordion({
					icons: false,
					autoHeight: false
				});		
				
				$('#form-exam').validate();
				
				$('textarea').autoGrow();
				
				var max_anamnesa_length = parseFloat($('#anamnesa').attr('maxlength'));
				$('#max_anamnesa').html(digit_grouping(max_anamnesa_length));
				$('#anamnesa').keyup( function() {					
					$('#max_anamnesa').html(digit_grouping(max_anamnesa_length - $(this).val().length));					
				}).keydown( function() {
					$('#max_anamnesa').html(digit_grouping(max_anamnesa_length - $(this).val().length));
				});
								
				function reset_lab_proc() {
					$("input[name^='lab']:checked").each( function() {
						$(this).val($(this).attr('alt'));
					});
					$("input[name^='tindakan']:checked").each( function() {
						$(this).val($(this).attr('alt'));
					});
					$("input[name^='lab']").attr("checked","");
					$("input[name^='lab']").parent("p").show();
					$("#price_lab").val("0");
					$("input[name^='tindakan']").parent("p").show();
					$("input[name^='tindakan']").attr("checked","");
					$("#price_tindakan").val("0");
					$("ul#item_list_paramita > li").remove();
					$('#total_price_paramita').val("0");
					$("#package-id").val("");
				}
				
				// THE FUNCTION USED BY PACKAGE
				// THIS IS TO ADD LAB CHECK ITEM FOR PARAMITA
				/* PARAM data: for passing the for the search\
						 pack
				*/ 
				function ajax_pramita(data) {
					$.post("<?php echo $baseURL ?>index.php/station_2/source_paramita", { "term": data },
							function (data) {
								// TAKE THE FIRST ARRAY IN MESSAGE
								// to see the the details please refer station_2 -> source_paramita
								var x = data.message[0];																			
								$("ul#item_list_paramita").append("<li id='" + x.id +"'>"+ x.label +" (Package) <input type='hidden' id='paramita_price' name='paramita_price["+ x.id +"]' value='0' /></li>");
								//$('#total_price_paramita').val( parseInt($('#total_price_paramita').val()) + parseInt(x.harga) );
							},"json");
				}
				//<input type='text' name='paramita_item["+ ui.item.id +"]' value='" + ui.item.id + "' />
				
				//<input type='button' id='remove_button' value='Remove' onClick='javascript:$(this).parent().remove()' />
				<?php endif; ?>
				
				<?php if(isset($search)) : ?>
					<?php if(isset($search_initial)) : ?>
						$("input#search_patient").focus();
					<?php endif; ?>
				$("input#search_patient").autocomplete({
				source: 
						function(req, add)
						{
							$.ajax({
								url: '<?php echo $baseURL ?>index.php/general/source_autocomplete',
								dataType: 'json',
								type: 'POST',
								data: req,
								success:    
									function(data)
									{
										if(data.response =='true')
										{
											add(data.message);										
										} else {
											$("input#search_patient").removeClass('ui-autocomplete-loading');
											$("ul.ui-autocomplete").hide();
										}
									}
							});
						},	
				minLength: 1,
				select: 
					function(event,ui) {
						//$("#mr_no").text(ui.item.id);
						$("input:hidden#tx_mr_no").val(ui.item.id);
						$("input#search_patient").val(ui.item.label);
						$("form#form-search").submit();
						return false;
					}
				}).data( "autocomplete" )._renderItem = function( ul, item ) {
					// The function for rendering the item shown in the suggestion list
					// alter the ".append" for custom message
					return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a><span style=\"font-weight:bold;\">" + item.label + "</span><br>" + item.id + "</a>" )
					.appendTo( ul );
				};
				<?php endif; ?>
				
				// --- END OF THE GONNA-BE-MOVED PART
		<?php endif; ?>
		
		<?php if ($this->session->userdata("station") == '3') : ?>
			// **** JAVASCRIPT FOR STATION 3 ****//
			//$( "#dialog:ui-dialog" ).dialog( "destroy" );
			<?php if(isset($search)) : ?>
			$("input#search_patient").autocomplete({
				source: 
						function(req, add)
						{
							$.ajax({
								url: '<?php echo $baseURL ?>index.php/station_3/source_autocomplete',
								dataType: 'json',
								type: 'POST',
								data: req,
								success:    
									function(data)
									{
										if(data.response =='true')
										{
											add(data.message);										
										} else {
											$("input#search_patient").removeClass('ui-autocomplete-loading');
											$("ul.ui-autocomplete").hide();
										}
									}
							});
						},	
				minLength: 1,
				select: 
					function(event,ui) {
						//$("#mr_no").text(ui.item.id);
						$("input:hidden#tx_mr_no").val(ui.item.id);
						$("input#search_patient").val(ui.item.label);
						$("form#form-search").submit();
						return false;
					}
				}).data( "autocomplete" )._renderItem = function( ul, item ) {
					// The function for rendering the item shown in the suggestion list
					// alter the ".append" for custom message
					return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a><span style=\"font-weight:bold;\">" + item.label + "</span><br>" + item.id + "</a>" )
					.appendTo( ul );
				};
			<?php endif; ?>
			
			<?php if(isset($insert)) : ?>
			
			/*$("div[id^='lab_item']").click( function() {
				//$(this).find("input[type='checkbox']").attr('checked',true).click();
				
				//$(this + " input[type='checkbox']").attr('checked',true);;
			});*/
			
			var checkbox_id;
			// close_id
			// FOR KNOWING FROM WHERE THE ACTION COMES FROM
			// 0 = UNCHECK THE CHECKBOX AFTER DIALOG CLOSE (CANCEL)
			// 1 = KEEP CHECKED THE CHECKBOX AFTER DIALOG CLOSE (SAVE)
			// DEFAULT VALUE = 0
			var close_id = 0;
			
			$("input.new_lab, input.new_paramita_lab").click( function() {
				//if(!($(this).is(":checked"))) {
					var div = "div#lab_item_" + $(this).attr('id');				
					var item_name = $(div +" > span#item").html();				
					var id_check = $(this).val();				
					var modal = "#dialog-modal";
					$("#curr_id").html($(this).attr('id'));
					checkbox_id = $(this).attr('id');
					$(modal+" > span#item").html(item_name);
					
					/*
					 * DOCUMENTATION 30/12/2011
					 * class new_lab is for Angsamerah lab check
					 * else that means it is for Paramita [NEED CONFIRMATION! TEMPORARY ANALYSIS]
					 */
					if ($(this).hasClass('new_lab'))
						$(modal+" > span#result").load("<?php echo site_url('/station_3/fetch_result_item_lab') ?>", {id_item: id_check});
					else
						$(modal+" > span#result").html("<textarea wrap=\"hard\" cols=\"25\" rows=\"7\" id=\"item_lab_value\"></textarea>"); // "<input type=\"text\" id=\"item_lab_value\" />"
					$("#dialog-modal").dialog("open");
					//$("#lab_result").append($(div));
				//}
			});
	
			$( "#dialog-modal" ).dialog({
				modal: true,
				draggable: false,
				resizable: false,
				autoOpen: false,
				close: function (event, ui) {
					if ( close_id == 0 )
						$("input#" + checkbox_id + "").attr("checked",false);
					//$("input.new_paramita_lab").attr("checked",false);
				},
				buttons: {
					Confirm: function() {
						var val = "#item_lab_value";
						close_id = 1;
						if (jQuery.trim($(val).val()) == "") {
							alert("PLEASE ENTER THE RESULT");
							$(val).focus();
						} else
							setValue($(val).val());
						close_id = 0;
					},
					Pending: function() {
						close_id = 1;
						setValue("Pending");
						close_id = 0;
					},
					Close: function() {						
						
						$(this).dialog('close');
					}
				}
				
			});
			
			/*
			  * function to make set the value into the 
			  */
			function setValue(x) {
				var div = "div#lab_item_" + $("#curr_id").html();
				var parent_div = $(div).parent('div.border-grey1');

				if ( x == "Pending" )
					$("input#check_status").val("P");
					
				$(div + " > input.item_val").val(x);
				$(div + " > span#result").html(x);
				if ($(div + " > input.new_lab").length > 0)	{			
					$(div + " > input.new_lab").hide();
				} else if ($(div + " > input.new_paramita_lab").length > 0) {
					$(div + " > input.new_paramita_lab").hide();
				}
				if ($('#lab_result').children(div).length == 0 )
					$("#lab_result").append($(div));						
				$("#dialog-modal").dialog('close');
				
				if(parseInt($(parent_div).children("div[id^=lab_item]").length) == 0)
					$(parent_div).hide('normal');//.remove();
				if (!($("#main_result").is(":visible"))) $("#main_result").show('normal');
				// hiding the lab request after all the request are checked
				// UPDATE: onlly do this if the div#main_request is not hidden
				// it is necessary since there's an updating mode added to the function
				if (!($("div#main_request div[id^='lab_item']").length) && $("div#main_request").css('display') != 'none') {
					$("div#main_request").hide('normal');
					$('div#main_result').animate({marginRight: '+=450px'},'slow');
					$("input[type='submit']").show('fast');
				}
			}
			
			$("div[id^='lab_item']").hover(
				function() {					
					$(this).addClass('selected');
					if ($(this).find("input[type='checkbox']").css('display') == 'none')
						$(this).find("input[type='button']").show();
				},
				function() {
					$(this).removeClass('selected');
					if ($(this).find("input[type='checkbox']").css('display') == 'none')
						$(this).find("input[type='button']").hide();
				}
			);
			
			$('input#but_edit').click( function() {
				//$(this).siblings("input[type='checkbox']").click();
				var div = $(this).parent('div');
				//alert($(div).children();
				var item_name = $(div).children("span#item").html();				
				var id_check = $(div).children("input[type='checkbox']").val(); // $row->id				
				var modal = "#dialog-modal";
				$("#curr_id").html($(div).children("input[type='checkbox']").attr('id'));
				$(modal+" > span#item").html(item_name);
				//if ($(this).hasClass('new_lab'))
					$(modal+" > span#result").load("<?php echo site_url('/station_3/fetch_result_item_lab') ?>", {id_item: id_check});
				//else
					//$(modal+" > span#result").html("<input type=\"text\" id=\"item_lab_value\" />");
				$("#dialog-modal").dialog("open");
			});
			
			
			<?php endif; ?>
		// end of station  3 script
		<?php endif; ?>
		
		<?php if ($this->session->userdata("station") == '4') : ?>
		// beginning of station 4 script
		
			<?php if (isset($add_data_obat)) : ?>
				$('#add_data').validate();
			<?php endif; ?>
			
			<?php if (isset($report_month)) :?>
				$('#report_month').val("<?php echo $report_month ?>");
				$('#report_year').val("<?php echo $report_year ?>");
			<?php endif; ?>
				
			<?php if (isset($search)) : ?>
				  

		        $('#dialog').dialog({
		            autoOpen: false,
		            width: 400,
		            modal: true,
		            resizable: false,
		            buttons: {
		                "Submit Form": function() {
		                    document.getElementById('form_update_stock').submit();
		                },
		                "Cancel": function() {
		                    $(this).dialog("close");
		                }
		            }
		        });

		        $('form#form_update_stock').submit(function(){
		            $("#submit_item").html($("#nama_obat").html());
		            $("#jumlah_submit_item").html($("#jumlah_masuk").val());
					$("#harga_beli_submit_item").html(digit_grouping($("#harga_baru").val()));
		            $('#dialog').dialog('open');
		            return false;
		        });


				//$("#form_update_stock").validate();
				$("input#search_obat").focus();
				$("input#search_obat").autocomplete({
				source: 
						function(req, add)
						{
							$.ajax({
								url: '<?php echo $baseURL ?>index.php/station_4/obat_autocomplete/<?php echo $jenis?>',
								dataType: 'json',
								type: 'POST',
								data: req,
								success:    
									function(data)
									{
										if(data.response =='true')
										{
											add(data.message);										
										} else {
											$("input#search_obat").removeClass('ui-autocomplete-loading');
											$("ul.ui-autocomplete").hide();
										}
									}
							});
						},	
				minLength: 1,
				select: 
					function(event,ui) {
						$("#main-table").show();
						$("input#search_obat").val(ui.item.label);
						$("input#id_obat").val(ui.item.id);
						$("span#nama_obat").html(ui.item.label);
						$("#unit").html(ui.item.unit);
						$("#harga_beli_lama").html("Rp. " + digit_grouping(ui.item.price));
						$("#stock").html(ui.item.stock);
						$("#stok_lama").val(ui.item.stock);
						$("input#harga_baru").val(ui.item.price);
						$("input#jumlah_masuk").val("");
						//$("#harga_beli_lama").focus();						
						
						//$("#mr_no").text(ui.item.id);
						/*$("input:hidden#tx_mr_no").val(ui.item.id);
						
						$("form#form-search").submit();//*/
						return false;
					}
				}).data( "autocomplete" )._renderItem = function( ul, item ) {
					// The function for rendering the item shown in the suggestion list
					// alter the ".append" for custom message
					return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a><span style=\"font-weight:bold;\">" + item.label + "</span></a>" )
					.appendTo( ul );
				};
				
				// the digit grouping function
				// thanks to http://blog.insicdesigns.com/2008/02/javascript-digit-grouping-function/
				function digit_grouping(nStr){  
				    nStr += '';  
				    x = nStr.split('.');  
				    x1 = x[0];  
				    x2 = x.length > 1 ? '.' + x[1] : '';  
				    var rgx = /(\d+)(\d{3})/;  
				    while (rgx.test(x1)) {  
				        x1 = x1.replace(rgx, '$1' + '.' + '$2');  
				    }  
				    return x1 + x2;  
				}
			<?php endif; ?>
			
			<?php if (isset($search_keluar) && $search_keluar == TRUE) { ?>
				$("#jenis_obat").change( function() {
					$("input#search_obat").val("").focus();
				});
				
				$('#dialog').dialog({
		            autoOpen: false,
		            width: 400,
		            modal: true,
		            resizable: false,
		            buttons: {
		                "Submit Form": function() {
		                    //document.getElementById('form_update_stock').submit();
							$("ul#list_obat").append("<li>"+$("#nama_obat").html()+"<input type='text' name='jumlah["+$("input#id_obat").val()+"]' value='"+$("#jumlah_keluar").val()+"' /><input type='text' name='price["+$("input#id_obat").val()+"]' value='"+$("input#harga_jual").val()+"' /></li>");
							$("#main-table").hide();
							$(this).dialog("close");
		                },
		                "Cancel": function() {
		                    $(this).dialog("close");
		                }
		            }
		        });

		        $('form#form_update_stock').submit(function(){
		            if (parseInt($("#jumlah_keluar").val()) > parseInt($("#stock").html())) {
						alert("Stok yang tersedia tidak mencukupi!");
						$("#jumlah_keluar").val("");
						$("#jumlah_keluar").focus();
					} else {
						$("#submit_item").html($("#nama_obat").html());
			            $("#jumlah_submit_item").html($("#jumlah_keluar").val());
			            $('#dialog').dialog('open');
					}
		            return false;
		        });


				//$("#form_update_stock").validate();
				$("input#search_obat").focus();
				$("input#search_obat").autocomplete({
				source: 
						function(req, add)
						{
							$.ajax({
								url: '<?php echo $baseURL ?>index.php/station_4/obat_autocomplete/'+$("#jenis_obat").val(),
								dataType: 'json',
								type: 'POST',
								data: req,
								success:    
									function(data)
									{
										if(data.response =='true')
										{
											add(data.message);										
										} else {
											$("input#search_obat").removeClass('ui-autocomplete-loading');
											$("ul.ui-autocomplete").hide();
										}
									}
							});
						},	
				minLength: 1,
				select: 
					function(event,ui) {
						$("#main-table").show();
						$("input#search_obat").val(ui.item.label);
						$("input#id_obat").val(ui.item.id);
						$("span#nama_obat").html(ui.item.label);
						$("#unit").html(ui.item.unit);
						//$("#harga_beli_lama").html("Rp. " + digit_grouping(ui.item.price));
						$("#stock").html(ui.item.stock);
						$("#stok").val(ui.item.stock);
						$("input#harga_jual").val(parseInt(ui.item.price * 1.33)); // HARGA JUAL = HARGA BELI + 33% * HARGA BELI
						$("td#harga_jual").html("Rp. " + digit_grouping(parseInt(ui.item.price * 1.33)));
						$("input#jumlah_masuk").val("");
						//$("#harga_beli_lama").focus();						
						
						//$("#mr_no").text(ui.item.id);
						/*$("input:hidden#tx_mr_no").val(ui.item.id);
						
						$("form#form-search").submit();//*/
						return false;
					}
				}).data( "autocomplete" )._renderItem = function( ul, item ) {
					// The function for rendering the item shown in the suggestion list
					// alter the ".append" for custom message
					return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a><span style=\"font-weight:bold;\">" + item.label + "</span></a>" )
					.appendTo( ul );
				};
				
				// the digit grouping function
				// thanks to http://blog.insicdesigns.com/2008/02/javascript-digit-grouping-function/
				function digit_grouping(nStr){  
				    nStr += '';  
				    x = nStr.split('.');  
				    x1 = x[0];  
				    x2 = x.length > 1 ? '.' + x[1] : '';  
				    var rgx = /(\d+)(\d{3})/;  
				    while (rgx.test(x1)) {  
				        x1 = x1.replace(rgx, '$1' + '.' + '$2');  
				    }  
				    return x1 + x2;  
				}
			<?php } ?>
		
		// end of station 4 script
		<?php endif; ?>
		
		<?php if ($this->session->userdata("station") == '5') : ?>
			
			<?php if(isset($report_station5)) : ?>
			/**** JAVASCRIPT FOR STATION 5 ****/
			
				$("#daily,#monthly").hide();
				$("#datepicker").datepicker({
					onSelect: function(dateText, inst) {								
									$("#report").load("<?php echo site_url('/station_5/generate_report') ?>", {report_date: dateText});
									//var timeOut;
									//clearTimeout(timeOut);
									//$("#report").fadeOut("normal");
									
									// **** TIMEOUT IS USED SO IT GIVES TIME FOR DIV TO DISAPPEAR FIRST AND IT FADES IN WITH ACTUAL CONTENT
									/*timeOut = setTimeout(function() {$("#report").load("<?php echo site_url('/station_5/generate_report') ?>", {report_date: dateText}, function() {					
										$(this).fadeIn("normal");
									});}, 250);*/
							  },
					dateFormat: 'yy-mm-dd',
					changeMonth: true,
					changeYear: true
				});
				
				$("#show_monthly_report").click( function() {
					var bulan = $("#report_month").val();
					var tahun = $("#report_year").val();
					$("#report").load("<?php echo site_url('/station_5/generate_report') ?>/"+bulan+"/"+tahun);
				});
							
				$("#type1").click( function () {
					$("#daily").fadeIn("normal");
					$("#monthly").hide();
				});
				
				$("#type2").click( function () {
					$("#monthly").fadeIn("normal");
					$("#daily").hide();
				});
			<?php endif; ?>
			
			<?php if(isset($payment_station5)) : ?>
			
				
				$("#discount").change( function() {
					$("#disc_value").html("Rp. " + digit_grouping( parseInt( $("#total_amount").val() * $(this).val() / 100)));
					$("#disc_amount").val(parseInt( $("#total_amount").val() * $(this).val() / 100 ));
				});
				
				$("#payment_method").change( function() {
					if( parseInt($("#payment_method :selected").val()) > 1 ) {
						$(".card_type").css("visibility","visible");						
					} else {
						$(".card_type").css("visibility","hidden");
						$("#card_number").val("");
						//alert($("#payment_method :selected").val());
					}
				});
				
				$("#payment").submit( function() {
					//$("div#payment-details").html("Thank you for using our services.");
					//return false;
					//alert("Succeed");
					$("#payment-details").hide();//html("Thank you for using our services.");
					$("#extra_msg").show();
				});
				
				$("#card_number").click( function() {
					if ( $(this).val() == "Credit card number" )
						$(this).val("");
				});
				
				
				function total_price() {
					return total_tindakan + total_lab + total_obat + doctor_fee + admin_fee;
				}
				
				function setVal(inpt, value) {
					var divID = "#" + inpt;
					$(divID).val(value);
				}
				
				
			
			<?php endif; ?>
			/***** END OF JAVASCRIPT STATION 5 ****/
		<?php endif; ?>
	
	
		<?php if ($this->session->userdata("station") == '1') : ?>
		/**** JAVASCRIPT FOR STATION 1 ****/		
			<?php if(isset($patient_details)) : ?>
			
			/*
			 *  Function for submit button for saving patient's details, with ajax-based submission
			 *  it doesn't work correctly as expected
			 */
			/*$("#save_btn").click( function () {
				//alert( $("input:radio[name=rb_sex]:checked").val() );
				var data = "tx_nickname=" + $("#tx_nickname").val() +
							"&tx_mr_no=" + $("#tx_mr_no").val() +
							"&dd_salutation=" + $("#dd_salutation").val() +
							"&tx_firstname=" + $("#tx_firstname").val() +
							"&tx_middlename=" + $("#tx_middlename").val() +
							"&tx_lastname=" + $("#tx_lastname").val() +
							"&rb_sex=" + $("input:radio[name=rb_sex]:checked").val() +
							"&dd_id_type=" + $("#dd_id_type").val() +
							"&tx_id_no=" + $("#tx_id_no").val() +
							"&tx_street_id=" + $("#tx_street_id").val() +
							"&tx_rt_id=" + $("#tx_rt_id").val() +
							"&tx_rw_id=" + $("#tx_rw_id").val() +
							"&tx_kelurahan_id=" + $("#tx_kelurahan_id").val() +
							"&tx_kecamatan_id=" + $("#tx_kecamatan_id").val() +
							"&tx_kota_id=" + $("#tx_kota_id").val() +
							"&tx_kdpos_id=" + $("#tx_kdpos_id").val() +
							"&tx_street_curr=" + $("#tx_street_curr").val() +
							"&tx_rt_curr=" + $("#tx_rt_curr").val() +
							"&tx_rw_curr=" + $("#tx_rw_curr").val() +
							"&tx_kelurahan_curr=" + $("#tx_kelurahan_curr").val() +
							"&tx_kecamatan_curr=" + $("#tx_kecamatan_curr").val() +
							"&tx_kota_curr=" + $("#tx_kota_curr").val() +
							"&tx_kdpos_curr=" + $("#tx_kdpos_curr").val() +
							"&tx_pob=" + $("#tx_pob").val() +
							"&dd_dob=" + $("#dd_dob").val() +
							"&dd_mob=" + $("#dd_mob").val() +
							"&dd_yob=" + $("#dd_yob").val() +
							"&tx_primary_hp=" + $("#tx_primary_hp").val() +
							"&tx_secondary_hp=" + $("#tx_secondary_hp").val() +
							"&tx_home_phone=" + $("#tx_home_phone").val() +
							"&tx_email_1=" + $("#tx_email_1").val() +
							"&tx_email_2=" + $("#tx_email_2").val() +
							"&dd_citizenship=" + $("#dd_citizenship").val() +
							"&tx_job=" + $("#tx_job").val();
				//alert (data);
				$.ajax({
					url: "<?php $baseURL ?>index.php/control_panel/update_patient_details",
					type: "POST",
					data: data,
					success: function (result) {
								if (result=="Success") {
									$("input[id!=tx_search],textarea,select,#save_btn").attr("disabled", true);
									$("#edit_btn").hide();
								} else 
									alert ("Error saving");
							 }
					
				});
				return false;
			});*/
			var nickname,salut,firstname, middlename, lastname,sex,id_type,id_no,street_id,rt_id,rw_id,kel_id,kec_id,kota_id,kdpos_id,street_curr,rt_curr,rw_curr,kel_curr,kec_curr,kota_curr,kdpos_curr,pob,dob,mob,yob,primary_hp,secondary_hp,home_phone,email_1,email_2,citizenship,job;
			$("#edit_btn").hide();
			$("#edit_btn").click( function () {
				$("input,textarea,select,#save_btn,#cancel_btn").removeAttr("disabled");
				$("input#tx_search, button#edit_btn").attr("disabled", true);	
				nickname = $("#tx_nickname").val();
				salut = $("#dd_salutation").val();
				firstname = $("#tx_firstname").val();
				middlename = $("#tx_middlename").val();
				lastname = $("#tx_lastname").val();
				sex = $("input[type='radio'][name='rb_sex']:checked").val();
				id_type = $("#dd_id_type").val();
				id_no = $("#tx_id_no").val();
				street_id = $("#tx_street_id").val();
				rt_id = $("#tx_rt_id").val();
				rw_id = $("#tx_rw_id").val();
				kel_id = $("#tx_kelurahan_id").val();
				kec_id = $("#tx_kecamatan_id").val();
				kota_id = $("#tx_kota_id").val();
				kdpos_id = $("#tx_kdpos_id").val();
				street_curr = $("#tx_street_curr").val();
				rt_curr = $("#tx_rt_curr").val();
				rw_curr = $("#tx_rw_curr").val();
				kel_curr = $("#tx_kelurahan_curr").val();
				kec_curr = $("#tx_kecamatan_curr").val();
				kota_curr = $("#tx_kota_curr").val();
				kdpos_curr = $("#tx_kdpos_curr").val();
				pob = $("#tx_pob").val();
				dob = $("#dd_dob").val();
				mob = $("#dd_mob").val();
				yob = $("#dd_yob").val();
				primary_hp = $("#tx_primary_hp").val();
				secondary_hp = $("#tx_secondary_hp").val();
				home_phone = $("#tx_home_phone").val();
				email_1 = $("#tx_email_1").val();
				email_2 = $("#tx_email_2").val();
				citizenship = $("#dd_citizenship").val();
				job = $("#tx_job").val();				
			});
			
			$("input[id!=tx_search],textarea,select,#save_btn,#cancel_btn").attr("disabled", true);
			//$("input#tx_search").removeAttr("disabled");
			$("input#ch_same").click( function () {
				if ($(this).is(':checked')) {
					$("#tx_street_curr").val( $("#tx_street_id").val() );
					$("#tx_rt_curr").val( $("#tx_rt_id").val() );
					$("#tx_rw_curr").val( $("#tx_rw_id").val() );
					$("#tx_kelurahan_curr").val( $("#tx_kelurahan_id").val() );
					$("#tx_kecamatan_curr").val( $("#tx_kecamatan_id").val() );
					$("#tx_kota_curr").val( $("#tx_kota_id").val() );
					$("#tx_kdpos_curr").val( $("#tx_kdpos_id").val() );
				}
				
			});
			$("#cancel_btn").click( function() {
				$("#tx_nickname").val(nickname);
				$("#dd_salutation").val(salut);
				$("#tx_firstname").val(firstname);
				$("#tx_middlename").val(middlename);
				$("#tx_lastname").val(lastname);
				$("#rb_sex_"+sex).attr("checked","checked");
				$("#dd_id_type").val(id_type);
				$("#tx_id_no").val(id_no);
				$("#tx_street_id").val(street_id);
				$("#tx_rt_id").val(rt_id);
				$("#tx_rw_id").val(rw_id);
				$("#tx_kelurahan_id").val(kel_id);
				$("#tx_kecamatan_id").val(kec_id);
				$("#tx_kota_id").val(kota_id);
				$("#tx_kdpos_id").val(kdpos_id);
				$("#tx_street_curr").val(street_curr);
				$("#tx_rt_curr").val(rt_curr);
				$("#tx_rw_curr").val(rw_curr);
				$("#tx_kelurahan_curr").val(kel_curr);
				$("#tx_kecamatan_curr").val(kec_curr);
				$("#tx_kota_curr").val(kota_curr);
				$("#tx_kdpos_curr").val(kdpos_curr);
				$("#tx_pob").val(pob);
				$("#dd_dob").val(dob);
				$("#dd_mob").val(mob);
				$("#dd_yob").val(yob);
				$("#tx_primary_hp").val(primary_hp);
				$("#tx_secondary_hp").val(secondary_hp);
				$("#tx_home_phone").val(home_phone);
				$("#tx_email_1").val(email_1);
				$("#tx_email_2").val(email_2);
				$("#dd_citizenship").val(citizenship);
				$("#tx_job").val(job);
				$("input,textarea,select,#save_btn,#cancel_btn").attr("disabled", true);
				$("input#tx_search, button#edit_btn").removeAttr("disabled");
			});
			$("#tx_search").autocomplete({
				source: 
						function(req, add)
						{
							$.ajax({
								url: '<?php echo $baseURL; ?>index.php/control_panel/autocomplete_patient_details',
								dataType: 'json',
								type: 'POST',
								data: req,
								success:    
									function(data)
									{
										if(data.response =='true')
										{
											add(data.message);
										} else {
											$("input#tx_search").removeClass('ui-autocomplete-loading');
											$("ul.ui-autocomplete").hide();
										}
										
									}
							});
						},	
				minLength: 1,
				select: 
					function(event,ui) {
						$("#edit_btn").show();
						$("#tx_nickname").val(ui.item.label);
						$("#mr_no").text(ui.item.mr_no);
						$("#tx_mr_no").val(ui.item.mr_no);
						$("#dd_salutation").val(ui.item.salutation);
						$("#tx_firstname").val(ui.item.first_name);
						$("#tx_middlename").val(ui.item.middle_name);
						$("#tx_lastname").val(ui.item.last_name);
						$("#rb_sex_"+ui.item.sex).attr("checked","checked");
						$("#dd_id_type").val(ui.item.id_type);
						$("#tx_id_no").val(ui.item.id_no);
						$("#tx_street_id").val(ui.item.alamat_id);
						$("#tx_rt_id").val(ui.item.rt_id);
						$("#tx_rw_id").val(ui.item.rw_id);
						$("#tx_kelurahan_id").val(ui.item.kelurahan_id);
						$("#tx_kecamatan_id").val(ui.item.kecamatan_id);
						$("#tx_kota_id").val(ui.item.kota_id);
						$("#tx_kdpos_id").val(ui.item.kdpos_id);
						$("#tx_street_curr").val(ui.item.alamat_curr);
						$("#tx_rt_curr").val(ui.item.rt_curr);
						$("#tx_rw_curr").val(ui.item.rw_curr);
						$("#tx_kelurahan_curr").val(ui.item.kelurahan_curr);
						$("#tx_kecamatan_curr").val(ui.item.kecamatan_curr);
						$("#tx_kota_curr").val(ui.item.kota_curr);
						$("#tx_kdpos_curr").val(ui.item.kdpos_curr);
						$("#tx_pob").val(ui.item.pob);
						$("#dd_dob").val(ui.item.dob);
						$("#dd_mob").val(ui.item.mob);
						$("#dd_yob").val(ui.item.yob);
						$("#tx_primary_hp").val(ui.item.phone_no);
						$("#tx_secondary_hp").val(ui.item.secondary_hp);
						$("#tx_home_phone").val(ui.item.home_phone);
						$("#tx_email_1").val(ui.item.primary_email);
						$("#tx_email_2").val(ui.item.secondary_email);
						$("#dd_citizenship").val(ui.item.citizenship);
						$("#tx_job").val(ui.item.job);
						return false;
					}
			}).data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a><span style=\"font-weight:bold;\">" + item.label + "</span><br>" + item.mr_no + "</a>" )
				.appendTo( ul );
			};
			<?php endif; ?>
			
			<?php if(isset($patient_arrival)) : ?>
			$("input#search_patient_arrival").focus();
			$("input#search_patient_arrival").autocomplete({
				source: 
						function(req, add)
						{
							$.ajax({
								url: '<?php echo $baseURL; ?>index.php/control_panel/source_autocomplete_arrival',
								dataType: 'json',
								type: 'POST',
								data: req,
								success:    
									function(data)
									{
										if(data.response =='true')
										{
											add(data.message);
										} else {
											$("input#search_patient_arrival").removeClass('ui-autocomplete-loading');
											$("ul.ui-autocomplete").hide();
										}
										
									}
							});
						},	
				minLength: 1,
				select: 
					function(event,ui) {
						$("#unique_no").text(ui.item.id);						
						$("input:hidden#id_appointment").val(ui.item.app_id);
						$("#name_patient").text(ui.item.nickname);
						$("#date_hour_appointment").text(ui.item.date+" / "+ui.item.hour+" - "+ui.item.end);
						$("#doctor").text(ui.item.doctor);
						$("#room_no").text(ui.item.room);
						if (ui.item.couple_nickname != null)
							$("#couple_nickname").html(" & " + ui.item.couple_nickname);
						else
							$("#couple_nickname").html("");
						$("#couple_app_id").val(ui.item.couple_app_id);
						return false;
					}
			}).data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a><span style=\"font-weight:bold;\">" + item.nickname + " " + ((item.couple_nickname != null) ? ("& " + item.couple_nickname) : "" )  +"</span><br>" + item.id + " - " + item.date + "</a>" )
				.appendTo( ul );
			};
			
			$("input:reset#bt_reset").click( function() {
				$("span").text("");
			});
			<?php endif; ?>
			
			<?php if(isset($returning_patient)) : ?>			
					
			$("input#search_patient").autocomplete({
				source: 
						function(req, add)
						{
							$.ajax({
								url: '<?php echo $baseURL ?>index.php/control_panel/source_autocomplete',
								dataType: 'json',
								type: 'POST',
								data: req,
								success:    
									function(data)
									{
										if(data.response =='true')
										{
											add(data.message);										
										} else {
											$("input#search_patient").removeClass('ui-autocomplete-loading');
											$("ul.ui-autocomplete").hide();
										}
									}
							});
						},	
				minLength: 1,
				select: 
					function(event,ui) {
						$("#mr_no").text(ui.item.id);
						$("input:hidden#tx_mr_no").val(ui.item.id);
						$("input#search_patient").val(ui.item.label);
						$("div#couple_check").show();
						return false;
					}
			}).data( "autocomplete" )._renderItem = function( ul, item ) {
				// The function for rendering the item shown in the suggestion list
				// alter the ".append" for custom message
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a><span style=\"font-weight:bold;\">" + item.label + "</span><br>" + item.id + "</a>" )
				.appendTo( ul );
			};
			
			$("input#search_patient_couple").autocomplete({
				source: 
						function(req, add)
						{
							$.ajax({
								url: '<?php echo $baseURL ?>index.php/control_panel/source_autocomplete',
								dataType: 'json',
								type: 'POST',
								data: req,
								success:    
									function(data)
									{
										if(data.response =='true')
										{
											add(data.message);										
										} else {
											$("input#search_patient_couple").removeClass('ui-autocomplete-loading');
											$("ul.ui-autocomplete").hide();
										}
									}
							});
						},	
				minLength: 1,
				select: 
					function(event,ui) {
						//$("#mr_no").text(ui.item.id);
						$("input:hidden#tx_mr_no_couple").val(ui.item.id);
						$("input#search_patient_couple").val(ui.item.label);
						$('span#couple_mr_no').html(ui.item.id);						
						return false;
					}
			}).data( "autocomplete" )._renderItem = function( ul, item ) {
				// The function for rendering the item shown in the suggestion list
				// alter the ".append" for custom message
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a><span style=\"font-weight:bold;\">" + item.label + "</span><br>" + item.id + "</a>" )
				.appendTo( ul );
			};
			
			var couple_input = "#tx_nickname, #tx_phone, #tx_mr_no_couple, #search_patient_couple";
			var new_couple = "#tx_nickname, #tx_phone";
			var old_couple = "#tx_mr_no_couple, #search_patient_couple";
			
			$("input:reset#bt_reset").click( function() {
				$("span").text("");
				$("div[id$='couple']").hide();
				$(couple_input).removeClass('required').val("");
				$("span#couple_mr_no").html("");
				
			});
			
			$("form#rp_form").validate();
			
			//$("div#dialog")
			$('#dialog').dialog({
				autoOpen: false,
				width: 400,
				modal: true,
				resizable: false,				
				buttons: {
					"Pasien Lama": function() {
						//document.getElementById('form_update_stock').submit();
						$('#old_patient_couple').show('slow');
						$(old_couple).toggleClass('required').val("");
						$(this).dialog("close");
					},
					"Pasien Baru": function() {
						$('#new_patient_couple').show('slow');
						$(new_couple).toggleClass('required').val("");
						$(this).dialog("close");
					}
				}
			});
			
			$('#with_couple').click( function() {
				if ($(this).is(":checked")) {
					$('#dialog').dialog('open');
					$('input#patient_type').val("RC");
				} else {
					$("div[id$='couple']").hide('slow');
					$(couple_input).removeClass('required').val("");
					$("span#couple_mr_no").html("");
					$('input#patient_type').val("RP");
				}
					
			});
			<?php endif; ?>	
			
			<?php if(isset($report_station1)) : ?>
			$("#daily,#monthly").hide();
			$("#datepicker").datepicker({
				onSelect: function(dateText, inst) {								
								$("#report").load("<?php echo site_url('/control_panel/show_report') ?>", {report_date: dateText});
						  },
				dateFormat: 'yy-mm-dd',
				changeMonth: true					
			});
			
			$("#show_monthly_report").click( function() {
				var bulan = $("#report_month").val();
				var tahun = $("#report_year").val();
				$("#report").load("<?php echo site_url('/control_panel/show_report') ?>/"+bulan+"/"+tahun);
			});
						
			$("#type1").click( function () {
				$("#daily").fadeIn("normal");
				$("#monthly").hide();
			});
			
			$("#type2").click( function () {
				$("#monthly").fadeIn("normal");
				$("#daily").hide();
			});
			<?php endif; ?>
			
			<?php if(isset($datepicker)) : ?>
			/**
				* 	THESE ARE THE COLLECTIONS FOR APPOINTMENT MAKING SCREEN, THE ONE WITH THE DATEPICKER
				*	THE BEGINNING STARTS HERE
				*	REMEMBER THE DATEPICKER IS THE APPOINTMENT MAKING SCREEN	
				*	
			*/
				<?php if(!isset($appointment->appointment_date)) { ?>
			$("#schedule").html("<span style=\"font-size:26px;color:#bababa\"><br /><br />Select the doctor's name<br />on the left side<br />to see the available schedule</span>");
				<?php } else { ?>
			show_schedule();
				<?php } ?>
			
			$("#med_only").click( function() {
				$('#main-table').toggle('normal');
				$('div#schedule').toggle('normal');
			});
			// function to remove and replace all the options in doctor's list
			// according the type of doctor: REGULAR OR SPECIALISTS
			$("input:radio[name=doc_type]").click( function () {				
				var type = $(this).val();
				$.getJSON("<?php echo $baseURL ?>index.php/control_panel/populate_doctor/"+type, function(data) {
					$("#dd_list_doctor option[value!=0]").remove();
					$("#schedule").fadeOut("normal");
					setTimeout(function () {$("#schedule").fadeIn("normal").html("<span style=\"font-size:26px;color:#bababa\"><br /><br />Select the doctor's name<br />on the left side<br />to see the available schedule</span>");},250);
					$.each(data, function(i, item) {
						//alert(item.optionText);
						$("#dd_list_doctor").append('<option value="'+ item.optionVal +'">'+ item.optionText +' ('+ item.optionSex +')</option>');
					});
					
				});
			});

			// FUNCTION TO CHANGE SCHEDULE WHEN USERS CLICK ON DOCTOR SELECT LIST
			function show_schedule() {
				var timeOut;
				clearTimeout(timeOut);
				var active_date = $("input:hidden#date").val();
				var doctor_id = $("select#dd_list_doctor").val();
				$("#schedule").fadeOut("normal");
				
				// **** TIMEOUT IS USED SO IT GIVES TIME FOR DIV TO DISAPPEAR FIRST AND IT FADES IN WITH ACTUAL CONTENT
				timeOut = setTimeout(function() {$("#schedule").load("<?php echo $baseURL ?>index.php/control_panel/generate_doctor_schedule", {doctor_id: doctor_id, date: active_date}, function() {					
					$(this).fadeIn("normal");
				});}, 250);
			}
			// *** END OF CHANGE SCHEDULE FUNCTION
			
			$("input#other_name").bind("focus blur", function(event) {
				$("label.absolute_left").css('display',(event.type=='blur' && !$(this).val()?'':'none'));
			});
			
			$("select#dd_list_doctor").change( function() {
				if($(this).val() != '0') {
					show_schedule();
					if (parseInt($(this).val()) == 16) {
						$('div#other_doctor').show();
						$('label.absolute_left').css('display','');
					} else {
						$("div#other_doctor").hide();
						$("input#other_name").val("");
					}
				} else {
					$("div#other_doctor").hide();
					$("#schedule").fadeOut("normal");
					setTimeout(function () {$("#schedule").fadeIn("normal").html("<span style=\"font-size:26px;color:#bababa\"><br /><br />Select the doctor's name<br />on the left side<br />to see the available schedule</span>");},250);
				}
			});
			
			$("select#dd_list_hour_start").change( function() {
				$(".td-selected").removeClass("td-selected");
				var start = parseInt($(this).val());
				var end = parseInt($("select#dd_list_hour_end").val());
				//alert(end);
				var room = parseInt($("select#dd_list_room").val());
				//var check;
				if ( start > end || isNaN(end)) {				
					$("select#dd_list_hour_end").val(start);
					end = start;
				}
				highlight_schedule(start, end, room);
				
				
			});

			$("select#dd_list_hour_end").change( function() {
				$(".td-selected").removeClass("td-selected");
				var start = parseInt($("select#dd_list_hour_start").val());
				var end = parseInt($("select#dd_list_hour_end").val());
				var room = $("select#dd_list_room").val();
				if ( start > end ) {
					$("select#dd_list_hour_start").val(end);
					start = end;
				}
				highlight_schedule(start, end, room);										
			});
			
			$("select#dd_list_room").change( function() {
				if ( $("select#dd_list_hour_start").val() != 0 ) {
					$(".td-selected").removeClass("td-selected");
					var start = parseInt($("select#dd_list_hour_start").val());
					var end = parseInt($("select#dd_list_hour_end").val());
					if ( start <= end ) {
						var room = $(this).val();
						highlight_schedule(start, end, room);					
					}
				}
			});
			
			function check_doctor_schedule(div) {
				if (!($(div).length))
					return false;
				return true;
			}
			
			function highlight_schedule(start, end, room) {
				var div;
				var result;
				for (var i = start; i <= end ; i++) {
					div = "#sch_"+i+"-"+room;
					result = check_doctor_schedule(div);
					if (result) 
						$(div).parent("td").addClass("td-selected");
					else {
						$(".td-selected").removeClass("td-selected");
						alert("Time is not available");
						$("select#dd_list_hour_start").val(0);
						$("select#dd_list_hour_end").val(0);
						break;
					}					
				}
				
			}
			// ****** BEGINNING OF FUNCTION DATE PICKER JQUERY UI
			
			$("#datepicker").datepicker({
				onSelect: function(dateText, inst) {
								$("input:hidden#date").val(dateText);
								$("select#dd_list_hour_start").val(0);
								$("select#dd_list_hour_end").val(0);
								show_schedule();								
						  },
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
			});
			<?php if(isset($appointment->appointment_date)) :?>
			$('#datepicker').datepicker("setDate", '<?php echo $appointment->appointment_date ?>');
			<?php endif; ?>
			$("input:reset#reset_appointment").click( function() {
					$("td-highlight").removeClass("td-highlight");
					show_schedule();
			});
			
			
			
			//**** END OF FUNCTION DATE PICKER JQUERY UI
			
			/*** 
				*
				*
				*  END OF FUNCTION COLLECTION OF APPOINTMENT MAKING SCREEN WITH DATE PICKER
				*
				*/
			<?php endif; ?>			
			
			<?php if(isset($new_patient)) : ?>
			//*** FUNCTION TO CHECK EXISTING UNIQUE IN NEW PATIENT REGISTRATION
			$("#form_new_patient").validate();
			$("div#couple").hide();
			$("#tx_nickname").focus();
			$("#with_couple").click( function() {				
				$("div#couple").toggle('slow');
				$("input[id*='couple']").toggleClass("required");
				if($(this).is(":checked")) {
					$("#patient_type").val("NC");					
				} else {
					$("#patient_type").val("NP");
				}
					
			});
			/*var delayed;
			$("#tx_unique").keyup(function()
			{
				clearTimeout(delayed);
				var value = this.value;
				if (value) {
					delayed = setTimeout(function() {
						//remove all the class add the messagebox classes and start fading
						
						
						if (value.length < 6) {
							$("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
									{ 
									//add message and change the class of the box and start fading
										$(this).html('Unique number must be 6 characters long').removeClass().addClass('messageboxerror').fadeTo(900,1);
									});	
						} else {
							$("#msgbox").removeClass().addClass('messagebox').text('Checking...').fadeIn("slow");
						//check the username exists or not from ajax
							$.post("<?php echo $baseURL ?>index.php/control_panel/check_unique_no",{ tx_unique:value } ,function(data)
							{
								if(data=='yes') //if username not avaiable
								{
									$("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
									{ 
									//add message and change the class of the box and start fading
										$(this).html('Already taken').addClass('messageboxerror').fadeTo(900,1);
									});		
								}
								else
								{
									$("#msgbox").fadeTo(200,0.1,function()  //start fading the messagebox
									{ 
										//add message and change the class of the box and start fading
										$(this).html("Available").addClass('messageboxok').fadeTo(900,1);	
									});
								}
								
							});
						}
					}, 750);
					return false;
				} else {
					$("#msgbox").html("").fadeOut('slow').removeClass();
				}
			});
			
			/**  Checking submit form for new patient registration **/
			/*$("#form_new_patient").submit( function() {
				if ( $("#msgbox").text() != "Available" ) {
					alert("Unique number must be available!");
					return false;
				}
			});*/
		//*** END OF FUNCTION CHECKING UNIQUE FOR NEW PATIENT REGISTRATION
		<?php endif; ?>
		
	//**** END OF JAVASCRIPT FOR STATION 1 ****//
	<?php endif; ?>
	});
	<?php if(isset($digit_group) && $digit_group == TRUE) : ?>
	// the digit grouping function
	// thanks to http://blog.insicdesigns.com/2008/02/javascript-digit-grouping-function/
	function digit_grouping(nStr){  
		nStr += '';  
		x = nStr.split('.');  
		x1 = x[0];  
		x2 = x.length > 1 ? '.' + x[1] : '';  
		var rgx = /(\d+)(\d{3})/;  
		while (rgx.test(x1)) {  
			x1 = x1.replace(rgx, '$1' + '.' + '$2');  
		}  
		return x1 + x2;  
	}
	<?php endif; ?>
	</script>
	
</head>
<body id="panel">
	<div id="container">
		<div id="chat_div"></div>
		<div id="header"></div>
		<?php if (!isset($no_nav)) { ?>
		<div id="navigation">
			<ul>
				<li><a href="<?php echo site_url('/') ?>">Home</a></li>
				
				<?php if ($this->session->userdata('station') == '1') : ?>
				<!-- beginning of station 1 menu -->
				<li<?php if (isset($menu_1_new_patient)) echo " class='selected'" ?>><a href="<?php echo site_url("control_panel/new_registration") ?>">New Patient</a></li>
				<li<?php if (isset($menu_1_returning_patient)) echo " class='selected'" ?>><a href="<?php echo $baseURL ?>index.php/control_panel/rp_registration">Returning Patient</a></li>
				<li<?php if (isset($menu_1_patient_arrival)) echo " class='selected'" ?>><a href="<?php echo $baseURL ?>index.php/control_panel/patient_arrival">Patient Arrival</a></li>
				<li<?php if (isset($menu_1_edit_appointment)) echo " class='selected'" ?>><a href="<?php echo $baseURL ?>index.php/control_panel/edit_appointment">Edit Appointment</a></li>
				<li<?php if (isset($menu_1_cancel_appointment)) echo " class='selected'" ?>><a href="<?php echo $baseURL ?>index.php/control_panel/cancel_appointment">Cancel Appointment</a></li>
				<li<?php if (isset($menu_1_patient_data)) echo " class='selected'" ?>><a href="<?php echo $baseURL ?>index.php/control_panel/patient_details">Patient Data</a></li>
				<li><a href="<?php echo $baseURL ?>index.php/control_panel/report">Report</a></li>
				
				<!-- end of station 1 menu -->
				<?php endif; ?>
				
				
				<?php if ($this->session->userdata('station') == '2') : ?>
				<!--beginning of station 2 menu -->				
				<li<?php if (isset($menu_2_exam)) echo " class='selected'" ?>><a href="<?php echo site_url("station_2/patient_list") ?>">Examination</a></li>
				<li<?php if (isset($menu_2_history)) echo " class='selected'" ?>><a href="<?php echo site_url("station_2/history") ?>">Medical history</a></li>
				<!-- end of station 2 menu -->
				<?php endif; ?>
				
				<?php if ($this->session->userdata('station') == '3') : ?>
				<!--beginning of station 3 menu -->
				<li<?php if (isset($menu_3_new)) echo " class='selected'" ?>><a href="<?php echo site_url("station_3/list_request/N") ?>">New Lab Request</a></li>
				<li<?php if (isset($menu_3_pending)) echo " class='selected'" ?>><a href="<?php echo site_url("station_3/list_request/P") ?>">Pending Result</a></li>
				<li<?php if (isset($menu_3_report)) echo " class='selected'" ?>><a href="<?php echo site_url("station_3/report") ?>">Report</a></li>
				<li<?php if (isset($menu_3_history)) echo " class='selected'" ?>><a href="<?php echo site_url("station_3/history") ?>">History</a></li>				
				<!-- end of station 3 menu -->
				<?php endif; ?>
				
				<?php if ($this->session->userdata('station') == '4') : ?>
				<!--beginning of station 4 menu -->
				<li <?php if (isset($menu_4_add_obat)) echo " class='selected'" ?>><a href="#">Data Obat</a>
					<ul>
						<li><a href="<?php echo site_url("station_4/add_med_data") ?>">Tambah</a>
						<li><a href="<?php echo site_url("station_4/view_med_data") ?>">View</a>
						
					</ul>
				</li>
				<li <?php if (isset($menu_4_stokist_obat)) echo " class='selected'" ?>><a href="#">Stokist Obat</a>
					<ul>
						<li><a href="<?php echo site_url("station_4/stokist_obat") ?>">Bebas</a>
						<li><a href="<?php echo site_url("station_4/stokist_obat/Terbatas") ?>">Terbatas</a>
						
					</ul>
				</li>
				<li<?php if (isset($menu_4_pengambilan_obat)) echo " class='selected'" ?>><a href="<?php echo site_url("station_4/patient_list") ?>">Pengambilan Obat</a>
					<?php
					/*<ul>
						<li><a href="<?php echo site_url("station_4/pengambilan_obat") ?>">Bebas</a></li>
						<li><a href="<?php echo site_url("station_4/pengambilan_obat_terbatas") ?>">Terbatas</a></li>						
						<li><a href="<?php echo site_url("station_4/patient_list") ?>">Appointment</a></li>						
					</ul>*/
					?>
				</li>
				<li<?php if (isset($menu_4_report)) echo " class='selected'" ?>><a href="<?php echo site_url("station_4/report") ?>">Report</a></li>
					
				<!-- end of station 4 menu -->
				<?php endif; ?>
				
				<?php if ($this->session->userdata('station') == '5') : ?>
				<!-- beginning of station 5 menu -->
				
				<li<?php if (isset($menu_5_patient_billing)) echo " class='selected'" ?>><a href="<?php echo site_url("station_5/patient_billing") ?>">Billing</a></li>
				<li><a href="<?php echo site_url("station_5/report") ?>">Report</a></li>
				<!--<li><a href="<?php echo site_url("station_5/create_file_lagu_sion") ?>">Lagu</a></li>-->
				
				<!-- end of station 5 menu -->
				<?php endif; ?>
				<!-- GENERAL MENU THAT EXISTS IN ALL STATIONS -->
				<li<?php if (isset($menu_change_pass)) echo " class='selected'"; ?>><a href="<?php echo $baseURL ?>index.php/general/change_pass">Change Password</a></li>
				<li><a href="<?php echo $baseURL ?>index.php/general/logout">Logout</a>
			</ul>
		</div>
		<?php } ?>
		<div<?php if(! isset($no_nav)) { ?> id="content"<?php } ?>>
			<div id="user-div">
				 Logged in as <i><?php echo $this->session->userdata('name') ?></i>
			</div>
			<?php if (isset($front_logo)) : ?>
			
			<div id="img" style="float:right;margin-top:10px;"><img src="<?php echo $baseURL."img/$front_logo"; ?>" width="150px" height="150px" /></div>
			<?php endif; ?>
