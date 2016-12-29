<?php require "header.php" ?>
	<div style='text-align:left'><a href='<?php echo site_url('control_panel') ?>'>Back to Control Panel Station 1</a></div>
	<center><h2>PATIENT APPOINTMENT REPORT</h2></center>
	Choose options below to see the desired report:<br />
	<input type="radio" name="type_report" id="type1" /><label for="type1">Daily</label> <span id="daily">Date: <input type="text" id="datepicker" size="30" /></span><br />
	<input type="radio" name="type_report" id="type2" /><label for="type2">Monthly</label>
	
	<span id="monthly"><select id="report_month" name="report_month">
		<?php
			for ($i = 1; $i <= 12; $i++)
				echo("\t\t<option value=\"$i\">".date('F',mktime(0,0,0,$i,1,2010))."</option>\n");
		?>
	</select>
	<select id="report_year" name="report_year">
		<?php
			$curr_year = date("Y");
			for ($i = $curr_year; $i >= $curr_year-10; $i--)
				echo("\t\t<option value=\"$i\">$i</option>\n");
		?>
	</select>
	<button id="show_monthly_report">Generate</button></span>
	<div id="report"></div>
<?php require "footer.php" ?>