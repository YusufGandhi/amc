<?php require "header.php" ?>
	<div style="margin-left: 400px;position:fixed">
		<div><strong>Total Jenderal:<br />Rp. <span id="general_total" style="display:inline;">0</span></strong></div>
	</div>
	<form method="post" id="form1" action="<?php echo $baseURL ?>index.php/station_5/new_invoice" target="_blank">
		<input type="hidden" name="price_lab" id="price_lab" value="0" />
		<input type="hidden" name="price_tindakan" id="price_tindakan" value="0" />
		<input type="hidden" name="price_obat" id="price_obat" value="0" />
		<input type="hidden" name="grand_total" id="grand_total" value="0" />
		<input type="hidden" name="full_name" id="full_name" value="<?php echo $full_name ?>" />
		<input type="hidden" name="salutation" id="salutation" value="<?php echo $salutation ?>" />
		<input type="submit" value="Generate invoice" /><br />
		<table>
			<tr>
				<td><label for="payment_method">Payment method:</label></td>
				<td>
					<select id="payment_method" name="payment_method">
						<option value="">Choose payment method</option>
						<option value="1">Cash</option>
						<option value="2">Credit Card</option>
						<option value="3">Debit card</option>
					</select>
				</td>
			</tr>
			<tr class="card_type" style="visibility: hidden">
				<td>&nbsp;</td>
				<td>
					<select id="card_type" name="card_type">
						<option value="Visa">Visa</option>
						<option value="MasterCard">Master Card</option>
					</select>
				</td>
			</tr>
		</table>
		<h3>PENDAFTARAN / ADMINISTRASI</h3>
		<?php
			
		?>
		<h3>LABORATORIUM</h3>
		Sub-total: <strong>Rp. <span id="total_price_lab">0</span></strong><br />
		<?php			
			$i = 1;
			foreach( $lab as $row ) {
				echo "\t\t<input type=\"checkbox\" class=\"lab\" value=\"$row->id\" alt=\"$row->price\" id=\"lab$i\"><label for=\"lab$i\">$row->type</label><br />\n";
				$i++;
			}
		
		?>
		<h3>TINDAKAN</h3>
		Sub-total: <strong>Rp. <span id="total_price_tindakan">0</span></strong><br />
		<?php
			$i = 1;
			foreach( $tindakan as $row ) {
				echo "\t\t<input type=\"checkbox\" class=\"tindakan\" value=\"$row->id\" alt=\"$row->price\" id=\"tindakan$i\"><label for=\"tindakan$i\">$row->tindakan</label><br />\n";
				$i++;
			}
		?>	
		<h3>OBAT</h3>
		Sub-total: <strong>Rp. <span id="total_price_obat">0</span></strong><br />
		<?php
			$i = 1;
			foreach( $obat as $row ) {
				echo "\t\t<input type=\"checkbox\" class=\"obat\" value=\"$row->id\" alt=\"$row->price\" id=\"obat$i\"><label for=\"obat$i\">$row->nama_obat</label><br />\n";
				$i++;
			}
		?>
		</form>
	
<?php require "footer.php" ?>