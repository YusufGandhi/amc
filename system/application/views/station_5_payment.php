<?php require "header.php" ?>
	<div id="payment-details">
	<form id="payment" method="post" action="<?php echo (site_url(("station_5/print_invoice/$app_id/R"))) ?>" target="_blank">
	<input type="hidden" name="payment_date" id="payment_date" value="<?php echo date("Y-m-d") ?>" />
	<input type="hidden" name="app_id" id="app_id" value="<?php echo $app_id ?>" />
	<input type="hidden" id="total_amount" value="<?php echo ($details->doctor_fee + $details->administration_fee + $details->lab_fee + $details->proc_fee + $details->med_fee + $details->amc_package_fee) ?>" />
	<h3>PAYMENT FOR <?php echo $app_id ?></h3>
	<table>
		<tr class='odd'>
			<td class='header'>Name</td>			
			<td><?php echo $details->salutation ?> <?php echo trim($details->full_name)==""?$details->nickname:$details->full_name; ?></td>
			
		</tr>
		<tr class='even'>
			<td class='header'>Visit Date</td>			
			<td><?php echo $app_date ?></td>						
		</tr>
		<tr class='odd'>
			<td class='header'>Total billing</td>			
			<td>Rp. <?php echo number_format(($details->doctor_fee + $details->administration_fee + $details->lab_fee + $details->proc_fee + $details->med_fee + $details->amc_package_fee),2,',','.'); ?></td>						
		</tr>
		<tr class='even'>
			<td class='header' style='vertical-align:top'>Discount</td>			
			<td>
				<span id='disc_value'>Rp. 0</span><br />
				<select name='discount' id='discount'>
					<?php 
						for ($i = 0; $i <= 100; $i+=5) {
							echo "<option value='$i'>$i%</option>";
						}
					?>
				<select>
				<input type='hidden' name='disc_amount' id='disc_amount' value='0' />
			</td>						
		</tr>
		<tr class='odd'>
			<td class='header'>Visit Date</td>			
			<td><?php echo $app_date ?></td>						
		</tr>		
		<tr class='even'>
			<td class='header'><label for="payment_method">Payment method</label></td>			
			<td>
				<select id="payment_method" name="payment_method">
				<?php
					foreach ($payment_method as $row) {
						echo "<option value=\"$row->id\">$row->method</option>";
					}
				?>
				</select>
			</td>
		</tr>
		<tr class="card_type even" style="visibility: hidden">			
			<td>
				<select id="card_type" name="card_type">
					<option value="1">Visa</option>
					<option value="2">Master Card</option>
				</select>
			</td>
			
			<td>
				<input size="16" maxlength="16" type="text" name="card_number" id="card_number" />
			</td>
		</tr>
		<tr class='odd'>
			<td colspan="2"><input type="submit" value="Generate Receipt" /></td>
		</tr>
	</table>
	</form>
	</div>
<?php require "footer.php" ?>
	