<?php require "header.php" ?>
	<center><h2>REPORT LABORATORIUM</h2></center>
	Pilih bulan dan tahun laporan yang ingin dilihat:<br />
	<form method="post" action="<?php echo site_url('station_3/report') ?>">
	<?php
	/*<input type="radio" name="type_report" id="type1" /><label for="type1">Daily</label> <span id="daily">Date: <input type="text" id="datepicker" size="30" /></span><br />*/?>
	
	<span id="monthly"><select id="report_month" name="report_month">
		<?php
			for ($i = 1; $i <= 12; $i++)
				echo("\t\t<option value=\"$i\"".((isset($report_month) && $report_month == $i) ? "selected='selected'": "" ).">".date('F',mktime(0,0,0,$i,1,2010))."</option>\n");
		?>
	</select>
	<select id="report_year" name="report_year">
		<?php
			$curr_year = date("Y");
			for ($i = $curr_year; $i >= $curr_year-10; $i--)
				echo("\t\t<option value=\"$i\"".((isset($report_year) && $report_year == $i) ? "selected='selected'": "" ).">$i</option>\n");
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
			echo "<center><h3>".strtoupper("Laporan Pemeriksaan Lab $month_name $report_year")."</h3></center>";
			
			echo "<table class='report' width='100%'>";
			echo "	<tr>";
			echo "		<th>No.</th>";
			echo "		<th>MR no.</th>";
			echo "		<th>Px Angsamerah</th>";			
			echo "		<th>Px Pramita</th>";
			echo "	</tr>";
			
			$class = "odd";
			$i = 1;
			foreach( $report_data as $row ) {
				//echo "<div><span id='nama_obat' style='color:#ff0000;font-weight:bold'>$row->nama_obat</span> (<span style='font-style:italic;font-size:smaller'>Stok Awal</span>: <span style='font-weight:bold;'>$row->saldo_awal_bulan</span>)</div>";
				//echo "<div><span id='nama_obat' style='color:#ff0000;font-weight:bold'>$row->mr_no</span></div>"; //$row->type ($row->specimen)
				
				echo "	<tr class='$class'>";
				echo " 		<td align='right' style='vertical-align:top;'>$i</td>";
				echo " 		<td align='center' style='vertical-align:top;'>$row->mr_no</td>";
				echo " 		<td style='vertical-align:top'>";
				
				// PRINTING THE ANGSAMERAH CHECK LAB
				// INTO THE REPORT
				if (!empty($transaction[$row->mr_no]['am'])) {
					$j = 1;
					echo "<table>";
					foreach($transaction[$row->mr_no]['am']	as $row2) {
						echo "<tr>";
						echo "<td>$j.</td><td>$row2->type ($row2->specimen)</td><td>$row2->result</td>";
						echo "</tr>";
						$j++;
					}
					echo "</table>";
				}
				echo " 		</td>";
				echo " 		<td style='vertical-align:top'>";
				
				// PRINTING THE PARAMITA REPORT
				// INTO THE REPORT
				if(!empty($transaction[$row->mr_no]['pr'])) {
					$j = 1;
					foreach($transaction[$row->mr_no]['pr']	as $row2) {
						echo "$j. $row2->pemeriksaan ($row2->klasifikasi) = $row2->result<br />";					
						$j++;
					}
				} else {
					echo "<h3>No Pramita lab check</h3>";
				}
				echo "		</td>";
				$i++;
				$class == "odd" ? $class="even" : $class="odd";			
				echo "	</tr>";
			}
			
			
			echo "</table>";
			
			
		} else { ?>
		<h3 style='color:red'>Tidak Ada Pemeriksaan Lab Pada Bulan <?php echo $month_name." ".$report_year ?> </h3>
		<?php } ?>
	</div>
	<?php endif; ?>
<?php require "footer.php" ?>