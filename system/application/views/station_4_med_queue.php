<?php require "header.php" ?>
	<?php if (!empty($result)) { ?>
	<h3>PATIENT LIST FOR TAKING MEDICINE</h3>
	<table class='report'>
		<tr>
			<th>No.</th>
			<th>MR #</th>
			<th>Name</th>
			<th>Sex</th>
			<th>Visit date</th>
			<th>Doctor</th>
			<th>Status</th>
		</tr>
		<?php
			$i = 1;
				$class = "odd";
				foreach($result as $row) {
					echo "<tr class='$class'>\n";
					echo "\t<td>".$i++."</td>\n";
					echo "\t<td>$row->mr_no</td>\n";
					echo "\t<td>$row->nickname</td>\n";
					echo "\t<td>$row->sex</td>\n";
					echo "\t<td>$row->appointment_date</td>\n";
					echo "\t<td>$row->name".($row->other_doctor_name==""?'':" ($row->other_doctor_name)")."</td>\n";
					//echo "\t<td><input class=\"pay\" type=\"button\" value=\"Pay now!\" onClick=\"window.location.href('".site_url("station_5/paid_item/$row->appointment_number")."')\" /></td>\n";
					echo "\t<td><a href='".site_url("station_4/medicine_list/$row->appointment_number")."'>View Meds</a></td>\n";
					echo "</tr>\n";
					$class = $class=="odd" ? "even" : "odd";
				}
		?>
	</table>
		<?php	} else { ?>
		<h2>NO PATIENT AT THE MOMENT</h2>
		<?php } ?>
<?php require "footer.php" ?>