<?php require "header.php" ?>
	<h3>PATIENT VISIT HISTORY</h3>
	<div>
		<form id="form-search" method="post" action="<?php echo site_url('station_2/result'); ?>">
			<p class="ui-widget" style="font-size:12px;"><label for="search_patient">Search patient</label> <input type="text" id="search_patient" /></p>
			<input type="hidden" id="tx_mr_no" name="tx_mr_no" />
		</form>
	</div>
	<?php if (isset($data_visit)) : ?>
		<div>
			<div id="data-header"></div>
			<div id="data-body">
				<table>
					<tr>
						<td><strong>MR #</strong></td>
						<td><strong>:</strong></td>
						<td><?php echo $mr_no?></td>
					</tr>
					<tr>
						<td><strong>Name</strong></td>
						<td><strong>:</strong></td>
						<td><?php echo trim($patient_details->full_name) != "" ? $patient_details->full_name : $patient_details->nickname ?></td>
					</tr>
					<tr>
						<td><strong>Sex</strong></td>
						<td><strong>:</strong></td>
						<td><?php echo $patient_details->sex ?></td>
					</tr>
				</table>
			</div>
		</div>
		<table class='report'>
			<tr>
				<th>No</th>
				<th>Date</th>
				<th>Visit ID</th>
				<th>Doctor</th>
				<th>Link</th>
				<th>Editable</th>
			</tr>
		<?php 
			$i=1;
			$class = "odd";
			foreach( $data_visit as $row) {	
		
				echo "<tr class='$class'>";
				echo "	<td align='right'>$i</td>";
				echo "	<td>$row->date</td>";
				echo "	<td>$row->app_id</td>";
				echo "	<td>$row->doctor</td>";
				echo "	<td>".anchor_popup(site_url("station_2/visit_details/$row->app_id/$i"),"View",$atts)."</td>";
				echo "  <td align='center'>".($row->doctor_id == $this->session->userdata('id_doctor') ? "<img src='".$baseURL."img/check-icon-16.png' alt='Editable' />" : "") ."</td>";
				echo "</tr>";
		
				$class = ($class == "odd") ? "even" : "odd";
				$i++;
			}
			
		?>
		</table>
	<?php endif; ?>
<?php require "footer.php" ?>