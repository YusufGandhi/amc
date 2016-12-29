<?php require "header.php" ?>
	
	<?php if (isset($form_save)) { ?>
		<div class='success'>Success</div>
	<?php } else if (isset($form_failed)) { ?>
		<div class='message'>Failed</div>
	<?php } ?>
	
	<?php 
		if(isset($title))
			echo "<h3>$title</h3>";
		else
			echo "<h3>INPUT DATA OBAT</h3>";
	?>
	<form id='add_data' method='post' action='<?php echo site_url('station_4/add_med_data') ?>'>
	<input type='hidden' name='admin' value='<?php echo $this->session->userdata('name') ?>' />
	<input type='hidden' name='operasi' value='I' />
	
		<table>
			<tr>
				<td><label for='nama_obat'>Nama Obat</label></td>
				<td>:</td>
				<td><input class='required' type='text' id='nama_obat' name='nama_obat' /></td>
			</tr>
			<tr>
				<td><label for='jenis_obat'>Jenis Obat<label></td>
				<td>:</td>
				<td>
					<select name='jenis_obat' id='jenis_obat'>
						<option value="Bebas">Bebas</option>
						<option value="Terbatas">Terbatas</option>
					</select>				
				</td>
			</tr>
			<tr>
				<td style='vertical-align: top'><label for='unit_obat'>Satuan (unit)<label></td>
				<td style='vertical-align: top'>:</td>
				<td>
					<input type='text' name='unit_obat' id='unit_obat' class='required' />
					<div style='font-style:italic; font-size:11px'>Co: TABLET, AMPUL, KAPSUL</div>
				</td>
			</tr>			
			<tr>
				<td><label for='harga_baru'>Harga Obat</label></td>
				<td>:</td>
				<td>Rp. <input type='text' size='5' name='harga_baru' id='harga_baru' class='required digits' /></td>
			</tr>
			<tr>
				<td>Jumlah</td>
				<td>:</td>
				<td><input type='text' size='5' name='jumlah_masuk' id='jumlah_masuk' class='required digits' /></td>
			</tr>
			<tr>
				<td><label for="keterangan">Keterangan</label></td>
				<td>:</td>
				<td><textarea name="keterangan" rows="5" cols="30" id="keterangan"></textarea></td>
			</tr>
		</table>		
		<input type='submit' value='Simpan Data Obat' />
	</form>
<?php require "footer.php" ?>