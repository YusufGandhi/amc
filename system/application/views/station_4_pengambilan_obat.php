<?php require "header.php" ?>
	<?php if (isset($success) && $success == TRUE ) { ?>
		<div class="success">Successful</div>
	<?php } else if ( isset($failed) && $failed == TRUE ) { ?>
		<div class="error">Failed</div>
	<?php } ?>
	<h3>PENGAMBILAN OBAT <?php echo strtoupper($jenis)?></h3>
	Search Nama Obat <input type="text" id="search_obat" />
	<br /><select id='jenis_obat'>
		<option value='Bebas'>Bebas</option>
		<option value='Terbatas'>Terbatas</option>
	</select>
	<form method='post' action='<?php echo site_url('station_4/update_stock') ?>'>		
		<input type="hidden" name="admin" id="admin" value="<?php echo $this->session->userdata('name') ?>" />				
		<input type="hidden" name="operasi" id="operasi" value="O" />
		<input type="hidden" name="app_id" id="app_id" />
		<ul id='list_obat'>
	
		</ul>
		<input type='submit' value='Check Out' />
	</form>
	<form id="form_update_stock" method="post" action="<?php echo $_SERVER['PHP_SELF'] // echo site_url('station_4/update_stock') ?>">
		<input type="hidden" name="id_obat" id="id_obat" />
		<input type="hidden" name="admin" id="admin" value="<?php echo $this->session->userdata('name') ?>" />		
		<input type="hidden" name="harga_jual" id="harga_jual" />
		<input type="hidden" name="stok" id="stok" />
		<input type="hidden" name="operasi" id="operasi" value="O" />
		<div id="main-table" class="hide" style="padding-top:12px">
			<div style="padding-left:4px;font-size:15x">			
				Detil obat: <span id="nama_obat" style="color:#ff0000;font-weight:bold"></span>
			</div>
			<table class="report">
			  <tr>
				<th>Tanggal keluar</th>
				<th>Satuan</th>
				<th>Harga Jual</th>				
				<th>Stok</th>
				<th>Keluar</th>				
				<th>Admin</th>
				<th>Ket</th>
			  </tr>
			  <tr>
				<td align="center"><?php echo date("M j, Y") ?></td>
				<td id="unit" align="center"></td>
				<td id="harga_jual" align="right"></td>				
				<td id="stock" align="right"></td>
				<td align="center"><input size ="4" class="required digits" type="text" name="jumlah_keluar" id="jumlah_keluar" /></td>				
				<td id="admin"><?php echo $this->session->userdata('name') ?></td>
				<td align="center"><input type="text" name="keterangan" id="keterangan" size="12" /></td>
			  </tr>
			  <tr>
				<td align="right" colspan="8"><input type="submit" value="Submit" /></td>
			  </tr>
			</table>		
		</div>
	
	</form>
	

	<div id="dialog" title="Verify Stock Data">
		<p>
			<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 0 0;"></span>
			Data yang Anda masukkan:
		</p>
		<table>
			<tr>
				<td>Nama item</td>
				<td>:</td>
				<td id="submit_item"></td>
			</tr>
			<tr>
				<td>Jumlah</td>
				<td>:</td>
				<td id="jumlah_submit_item"></td>
			</tr>			
		</table>
		<p>Jika data sudah benar, klik Submit Form.</p>
		<p>jika belum, klik Cancel.</p>
	</div>


<?php require "footer.php" ?>