<?php require "header.php" ?>
	<center><h2>REPORT STOCK OBAT</h2></center>
	Pilih bulan dan tahun laporan yang ingin dilihat:<br />
	<form method="post" action="<?php echo site_url('station_4/report') ?>">
	<?php
	/*<input type="radio" name="type_report" id="type1" /><label for="type1">Daily</label> <span id="daily">Date: <input type="text" id="datepicker" size="30" /></span><br />*/?>
	
	<span id="monthly"><select id="report_month" name="report_month">
		<?php
			for ($i = 1; $i <= 12; $i++)
				echo("\t\t<option value=\"$i\">".date('F',mktime(0,0,0,$i,1,2010))."</option>\n");
		?>
	</select>
	<select id="report_year" name="report_year">
		<?php
			$curr_year = date("Y");
			for ($i = $curr_year; $i >= $curr_year-10; $i--)
				echo("\t\t<option value=\"$i\">$i</option>\n");
		?>
	</select>
	
	<button id="show_monthly_report">Generate</button></span>
	</form>
	<?php if (isset($report_data)) : ?>
	<div id="report">
		<?php
		if ($report_data != FALSE) {
			echo "<form method='post' target='_blank' action='".site_url('station_4/report/pdf')."'>";
			echo "<input type='hidden' value='$report_month' name='report_month' />";
			echo "<input type='hidden' value='$report_year' name='report_year' />";
			echo "<div><input type='submit' value='Download Laporan $month_name $report_year' /></div>";
			echo "</form>";			
			echo "<center><h3>".strtoupper("Laporan Stok Obat Bulan $month_name $report_year")."</h3></center>";
			
			foreach( $report_data as $row ) {
				//echo "<div><span id='nama_obat' style='color:#ff0000;font-weight:bold'>$row->nama_obat</span> (<span style='font-style:italic;font-size:smaller'>Stok Awal</span>: <span style='font-weight:bold;'>$row->saldo_awal_bulan</span>)</div>";
				echo "<div><span id='nama_obat' style='color:#ff0000;font-weight:bold'>$row->nama_obat</span></div>";
				
				$i = 1;
				//$saldo = $row->saldo_awal_bulan;
				echo "<table class='report'>";
				echo "<tr>";
				echo "<th>No.</th>";
				echo "<th>RM #</th>";
				echo "<th>Tanggal</th>";
				echo "<th>Jumlah</th>";											
				echo "<th>Harga Jual</th>";
				echo "<th>Total</th>";			
				echo "<th>Sisa Stok</th>";
				echo "</tr>";
				
				$class = "odd";
				foreach($transaction[$row->id_obat] as $row2) {
					//$saldo -= $row2->amount;
					echo "<tr class='$class'>";
					echo "<td>$i</td>";
					echo "<td>$row2->mr_no</td>";
					echo "<td>$row2->operation_date</td>";
					echo "<td align='right'>$row2->amount</td>";				
					echo "<td align='right'>Rp. ".number_format($row2->price, 0, ',', '.')."</td>";
					echo "<td>Rp. ". number_format(($row2->amount * $row2->price), 0, ',', '.')."</td>";
					echo "<td align='right'>".$row2->sisa."</td>";				
					echo "</tr>";
					$i++;
					$class == "odd" ? $class="even" : $class="odd";
				}
				
				echo "</table>";
				//echo "Stok Akhir: $row->saldo_akhir_bulan<br /><br />";
				//echo "$row->id_obat $row->nama_obat $row->saldo_awal_bulan $row->saldo_akhir_bulan";
				//echo "<br />";
			}
		} else { ?>
		TIDAK ADA OBAT KELUAR
		<?php } ?>
	</div>
	<?php endif; ?>
<?php require "footer.php" ?>