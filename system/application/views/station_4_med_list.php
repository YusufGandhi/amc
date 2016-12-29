<?php require "header.php" ?>
	<?php if (!empty($result)) { ?>
	<h3>MEDICINE LIST FOR PATIENT</h3>
	<form method='post' action='<?php echo site_url('station_4/save_med')?>'>
	<input type='hidden' name='operasi' value='O' />
	<input type='hidden' name='app_id' value='<?php echo $app_id ?>' />
	<input type="hidden" name="admin" id="admin" value="<?php echo $this->session->userdata('name') ?>" />
	<table class='report'>
		<tr>
			<th>No.</th>
			<th>Medicine Name</th>
			<th>Amount</th>
			<th>Price</th>
			<th>Stock</th>			
			<th>Taken</th>			
		</tr>
		
		<?php
			$i = 1;
			$class = "odd";
				foreach($result as $row) {
					echo "<tr class='$class'>\n";
					echo "\t<td>".$i++."</td>\n";
					echo "\t<td>$row->nama_obat</td>\n";
					echo "\t<td align='right'>$row->jumlah<input type='hidden' name='jumlah[$row->id]' value='$row->jumlah' /></td>\n";
					echo "\t<td align='right'>Rp. ".number_format((int) $row->price,0,",",".")."<input type='hidden' name='price[$row->id]' value='".(int) $row->price."' /></td>\n";
					echo "\t<td align='right'>$row->current_stock</td>\n";					
					echo "\t<td align='center'>". (($row->jumlah < $row->current_stock) ? "<input class='required' type='checkbox' name='id[$row->id]' value='Y' />" : "<img src='".$baseURL."img/x-mark.png' width='15px' height='15px' alt='Stock is not enough' title='Stock is not enough' />" )."</td>\n";					
					echo "</tr>\n";
					$class = $class=="odd" ? "even" : "odd";
				}
		?>
	</table>
	<input type='submit' value='Check Out' />
	</form>
	<?php	} else { ?>
		<h2>NO PATIENT AT THE MOMENT</h2>
	<?php } ?>
		
<?php require "footer.php" ?>