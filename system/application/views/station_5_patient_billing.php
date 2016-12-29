<?php require "header.php" ?>
	<?php if (!empty($result)) { ?>
	<H3>LIST OF PATIENT BILLING</H3>
	<table class='report'>
		<tr>
			<th>No.</th>
			<th>Appointment no</th>
			<th>MR #</th>
			<th>Name</th>
			<th>Sex</th>
			<th>Visit date</th>
			<th>Doctor</th>
			<th colspan='2'>Generate</th>
		</tr>
		<?php
			$i = 1;
			$class = "odd";
				foreach($result as $row) {
					echo "<tr class='$class'>\n";
					echo "\t<td>".$i++."</td>\n";
					echo "\t<td>$row->appointment_number</td>\n";
					echo "\t<td>$row->mr_no</td>\n";
					echo "\t<td>$row->nickname</td>\n";
					echo "\t<td>$row->sex</td>\n";
					echo "\t<td>$row->appointment_date</td>\n";
					echo "\t<td>$row->name</td>\n";
					//echo "\t<td><input class=\"pay\" type=\"button\" value=\"Pay now!\" onClick=\"window.location.href('".site_url("station_5/paid_item/$row->appointment_number")."')\" /></td>\n";
					echo "\t<td><a href='".site_url("station_5/print_invoice/$row->appointment_number")."' target='_blank'>Invoice</a></td>\n";
					echo "\t<td><a href='".site_url("station_5/payment/$row->appointment_number")."'>Payment</a></td>\n";
					echo "</tr>\n";
					$class = $class=="odd" ? "even" : "odd";
				}
		?>
	</table>
		<?php	} else { ?>
		NO PATIENT AT THE MOMENT
		<?php } ?>
<?php require "footer.php" ?>