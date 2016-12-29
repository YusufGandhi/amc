<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title>Angsamerah Company Information System<?php echo (isset($title)? (" - ".$title):"") ?></title>
	<script src="<?php echo $baseURL ?>js/jquery-1.4.4.min.js" type="text/javascript"></script>
	<?php if (isset($form_validator) && $form_validator == TRUE) { ?> <script src="<?php echo $baseURL ?>js/jquery.validate.js" type="text/javascript"></script> <?php } ?>
	<?php if (isset($auto_grow) && $auto_grow == TRUE) { ?> <script src="<?php echo $baseURL ?>js/jquery.autogrowtextarea.js" type="text/javascript"></script><?php } ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $baseURL ?>master.css" />
	<script type="text/javascript">
		<?php
			list($blood1,$blood2) = explode('/',$details->blood_pressure);
			echo "var sistole = \"$blood1\";\n";
			echo "var diastole = \"$blood2\";\n";
			echo "var nadi = \"$details->nadi\";\n";
			echo "var temperature = '$details->temperature';\n";
			echo "var breath  = \"$details->breath\";\n";
			//echo "var ph_notes = \"$details->physical_notes\";\n";
			echo "var d_kerja = \"$details->d_kerja\";\n";
			//echo "var d_banding = \"$details->d_banding\";\n";
		//	echo "var other_meds = \"$details->other_meds\";\n";
		?>
		$(document).ready( function() {
			$('input#edit_btn').click( function() {
				
				$('span#show_data, div#show_data').hide();
				$('span#edit_data').show();
				$('input#edit_btn').hide();
				$('div#msg').hide();
				$('span#edit_mode').removeClass('hide');
				//alert("success");
			});
		
			$('input#cancel_btn').click( function() {
				$('input#sistole').val(sistole);
				$('input#diastole').val(diastole);
				$('input#nadi').val(nadi);
				$('select#temperature').val(temperature);
				$('input#breath').val(breath);				
				$('#d_kerja').val(d_kerja);
				$('span#show_data, div#show_data').show();
				$('span#edit_data').hide();
				$('input#edit_btn').show();
				$('span#edit_mode').addClass('hide');
				
			});
			
			$('#visit_details').validate();
			//<?php if(isset($form_validator) && $form_validator == TRUE ) { ?> $("#visit_details").validate(); <?php } ?>
			//$("textarea[name='anamnesa']").autoGrow();
		});
	</script>
</head>
<body>
	<div>
		<div id="simple_header"></div>		
		<div id ="simple_content">
			