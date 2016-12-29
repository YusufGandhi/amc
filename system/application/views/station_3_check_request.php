<?php require "header.php" ?>
	<?php if (!empty($result)) { ?>
	<h3>LAB REQUEST LIST</h3>
	<table class='report'>
		<tr>
			<th>No.</th>
			<th>MR #</th>
			<th>Name</th>
			<th>Sex</th>
			<th>Visit date</th>
			<th>Doctor</th>
			<th></th>
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
					echo "\t<td>$row->name".($row->other_doctor_name!=""?" ($row->other_doctor_name)":"")."</td>\n";
					echo "\t<td><a href='".site_url("station_3/check_patient/$row->appointment_number/$stat")."'>See request</a></td>\n";
					echo "</tr>\n";
					$class = $class=="odd"?"even":"odd";
				}
		?>
	</table>
		<?php	
			} 
			else {
				if ($stat == "N") {
					echo "<div class='information'>No new lab check request</div>";
				} else if ($stat == "P") {
					echo "<div class='information'>No pending lab check request</div>";
				}
			} 
		?>
<?php require "footer.php" ?>