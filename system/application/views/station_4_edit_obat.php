<?php require "header.php" ?>
	
	<h3>EDIT DATA OBAT</h3>
	<form id='edit_data' method='post' action='<?php echo site_url('station_4/edit_obat') ?>'>
	<input type='hidden' name='admin' value='<?php echo $this->session->userdata('name') ?>' />
	<input type='hidden' name='id_obat' value='<?php echo $details->id ?>' />
		<table>
			<tr>
				<td><label for='nama_obat'>Nama Obat</label></td>
				<td>:</td>
				<td><input class='required' type='text' id='nama_obat' name='nama_obat' value="<?php echo $details->nama_obat ?>" /></td>
			</tr>
			<tr>
				<td><label for='jenis_obat'>Jenis Obat<label></td>
				<td>:</td>
				<td>
					<select name='jenis_obat' id='jenis_obat'>
						<option value="Bebas"<?php if ($details->jenis == "Bebas") echo " selected='selected'"; ?>>Bebas</option>
						<option value="Terbatas"<?php if ($details->jenis == "Terbatas") echo " selected='selected'"; ?>>Terbatas</option>
					</select>				
				</td>
			</tr>
			<tr>
				<td style='vertical-align: top'><label for='unit_obat'>Satuan (unit)<label></td>
				<td style='vertical-align: top'>:</td>
				<td>
					<input type='text' name='unit_obat' id='unit_obat' class='required' value="<?php echo $details->unit ?>" />
					<div style='font-style:italic; font-size:11px'>Co: TABLET, AMPUL, KAPSUL</div>
				</td>
			</tr>			
		</table>		
		<input type='submit' value='Simpan Data Obat' />
	</form>
<?php require "footer.php" ?>