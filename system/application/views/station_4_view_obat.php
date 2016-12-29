<?php require "header.php" ?>
	<?php if(isset($form_save)) : ?>
	<div class='success'>Update success!</div>
	<?php endif; ?>
	<h3>DATA OBAT</h3>
	<?php 
	//echo $this->table->generate($results); 
		if (!empty($results)) {
			$class = "odd";
			echo "<table class='report'>";
			echo "	<tr>";
			echo " 		<th>ID no.</th>";
			echo " 		<th>Nama Obat</th>";
			echo " 		<th>Unit</th>";
			echo " 		<th colspan='2'>Price</th>";
			echo " 		<th>Tipe</th>";
			echo " 		<th>Stok</th>";			
			echo " 		<th></th>";			
			echo "	</tr>";
			foreach($results as $row) {
				echo "	<tr class='$class'>";
				echo "		<td>$row->id</td>";
				echo "		<td>$row->nama_obat</td>";
				echo "		<td align='right'>$row->unit</td>";
				echo "		<td style='border-right:0'>Rp.</td>";
				echo "		<td align='right'>".number_format($row->price,0,",",".") ."</td>";
				echo "		<td>$row->jenis</td>";
				echo "		<td align='right'>$row->current_stock</td>";
				echo "		<td align='center'><a href='".site_url("station_4/edit_obat/$row->id")."'><img src='".$baseURL."img/b_edit.png' alt='Edit' /></a></td>";
				echo "	</tr>";
				$class = $class=="odd"?"even":"odd";
			}
			echo "</table>";
		}
	?>
	
	<center><?php echo $this->pagination->create_links(); ?></center>
<?php require "footer.php" ?>