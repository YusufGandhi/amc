<?php require "header.php" ?>
		<div id="dialog-modal" title="Enter Result" style="font-size:17px">Result for<br /><span id="item" style="font-weight:bold"></span> <span id="result"></span></div>
		<div>
			<table>
				<tr>
					<td>Name</td>
					<td>:</td>					
					<td><?php echo trim($patient->full_name) != "" ? $patient->full_name : $patient->nickname?></td>
					<td width="50px">&nbsp;</td>
					<td>MR #</td>
					<td>:</td>
					<td><?php echo $patient->mr_no ?></td>
				</tr>
				<tr>
					<td>Umur</td>
					<td>:</td>
					<td><?php echo ($patient->yob != 0 ? ($curr_year - $patient->yob ) : "--") ?> year(s)</td>
					<td>&nbsp;</td>
					<td>Date</td>
					<td>:</td>
					<td><?php echo $patient->appointment_date ?></td>
				</tr>
				<tr>
					<td>Sex</td>
					<td>:</td>
					<td><?php echo $patient->sex ?></td>
				</tr>				
				<tr>
					
				</tr>
			</table>
			<!-- BUAT FORMAT: NAMA PANJANG (NICKNAME) -->
		
		</div>
		
		<span id="curr_id" style="display:none"></span>
		<div id="main_result" style="display:none;width:300px;float:right">			
			<h3>RESULT</h3>
			<form method="post" action="<?php echo site_url('station_3/save_result') ?>">
			<input type="hidden" name="check_status" id="check_status" value="D" /><!-- D means DONE for the whole check up -->
			<input id="app_id" name="app_id" type="hidden" value="<?php echo $app_id ?>" />
			<div id="lab_result"></div>
			<input type="submit" value="Submit" style="display:none" />
			</form>
		</div>
		<div id="main_request" style="width:250px;">
			<h3>LABORATORIUM ITEMS</h3>
			<?php
				//if (!empty($specimen)) {
					
					$i = 1;
					if (!empty($lab)) {
						$specimen = "";
						foreach( $lab as $row ) {
							if ($row->specimen != $specimen) {							
								if ($specimen != "") echo "</div>";
								echo "<div class='border-grey1'><div>$row->specimen</div>";
							}
							echo "\t\t\t<div id=\"lab_item_$i\" style='padding:3px'><span id=\"item\">$row->type</span> <span id=\"result\" style='float:right'></span> <input style='float:right' name=\"check[$i]\" id=\"$i\" type=\"checkbox\" class=\"new_lab\" value=\"$row->id\"/><input class=\"item_val\" type=\"hidden\" name=\"lab_value[$i]\" id=\"lab_value[$i]\" /><input type='button' style='display:none' value='Edit' id='but_edit' /></div>\n";
							$i++;
							$specimen = $row->specimen;
						}
						echo "</div>";
					}
				//}
				
				if (!empty($lab_paramita)) {
					$j = 1; // IT'S  THE COUNTER for LAB PARAMITA ONLY
					echo "<h4>PARAMITA LAB RESULT</h4>";
					foreach( $lab_paramita as $row ) {
						echo "\t\t\t<div id=\"lab_item_$i\" style='padding:3px'><span id=\"item\">$row->pemeriksaan</span> <span id=\"result\" style='float:right'></span> <input style='float:right;' name=\"check[$i]\" id=\"$i\" type=\"checkbox\" class=\"new_paramita_lab\" value=\"$row->id\"/><input class=\"item_val\" type=\"hidden\" name=\"paramita_lab_value[$j]\" id=\"lab_value[$i]\" /><input type='button' style='display:none' value='Edit' id='but_edit' /></div>\n";
						$i++;
						$j++;
					}
				}
			?>
		</div>
		
		
	
<?php require "footer.php" ?>