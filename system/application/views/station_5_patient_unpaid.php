<?php require "header.php" ?>
	<table>
		<tr>
			<th>No.</th>
			<th>MR #</th>
			<th>Name</th>
			<th>Sex</th>
			<th>Visit date</th>
			<th>Doctor</th>
		</tr>
		<?php
			$i = 1;
			foreach($result as $row) {
				echo "<tr>\n";
				echo "\t<td>".$i++."</td>\n";
				echo "\t<td>$row->mr_no</td>\n";
				echo "\t<td>$row->nickname</td>\n";
				echo "\t<td>$row->sex</td>\n";
				echo "\t<td>$row->appointment_date</td>\n";
				echo "\t<td>$row->name</td>\n";
				//echo "\t<td><input class=\"pay\" type=\"button\" value=\"Pay now!\" onClick=\"window.location.href('".site_url("station_5/paid_item/$row->appointment_number")."')\" /></td>\n";
				echo "\t<td><a href='".site_url("station_5/paid_item/$row->appointment_number")."'>Payment</a></td>\n";
				echo "</tr>\n";
			}
		?>
	</table>
<?php require "footer.php" ?>