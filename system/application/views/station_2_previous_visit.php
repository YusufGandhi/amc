<?php require "simple_header.php" ?>
		<?php
			if ($doctor->id == $this->session->userdata('id_doctor')) {
				echo "<input type='button' id='edit_btn' value='Edit' />";
				echo "<form id='visit_details' method='post' action='". site_url('station_2/save_edit') ."'>";
				echo "<span id='edit_mode' class='hide'><input type='submit' id='save_btn' value='Save' /> <input type='button' id='cancel_btn' value='Cancel' /></span>";
				if (isset($update_success) && $update_success === TRUE) {
					echo "<div id='msg' class='message'>Successfully updated</div>";
				} else if (isset($update_success) && $update_success === FALSE)
					echo "<div id='msg' class='message'>UPDATE FAILED!</div>";
			}			
		?>
		<center>MR #: <?php echo $patient->mr_no ?></center>
		<div id="details" style="padding-right:10px;">
			<div style="float:left;">
				<table>
					<tr>
						<td>Visit #</td>
						<td>:</td>
						<td><?php echo $no ?></td>
					</tr>
					<tr>
						<td>Name</td>
						<td>:</td>
						<td><?php echo trim($patient->full_name) != "" ? $patient->full_name : $patient->nickname ?></td>
					</tr>
					<tr>
						<td>Age</td>
						<td>:</td>
						<td><?php echo ($patient->yob != 0 ? ($curr_year - $patient->yob ) : "--") ?> years</td>
					</tr>
					<tr>
						<td>Sex</td>
						<td>:</td>
						<td><?php echo $patient->sex ?></td>
					</tr>					
				</table>
			</div>
			<div style="text-align:right;">
				Date: <?php echo $patient->appointment_date ?>
			</div>		
			<div style="clear:both;">
				<hr />
				<div class="border-grey-expand">
					<table width="100%" id="prev_visit">
						<tr class="even" style="vertical-align:top">
							<td class="header" width="20%">Doctor</td>
							<td><?php echo $doctor->name ?></td>
						</tr>
						<tr style="vertical-align:top">
							<td class="header">Nurse</td>
							<td><?php echo $nurse ?></td>
						</tr>					
						<tr class="even" style="vertical-align:top">
							<td class="header">Main Complaint</td>
							<td><?php echo $details->main_complaint ?></td>
						</tr>
						<tr style="vertical-align:top">
							<td class="header">Anamnesa</td>
							<td>
								<span id='show_data'><?php echo nl2br($details->anamnesa) ?></span>
								<span id='edit_data' class='hide'><textarea name="anamnesa" rows="20" cols="50" maxlength="20000" class="required"><?php echo $details->anamnesa ?></textarea></span>
							</td>
						</tr>
					</table>				
				</div>
				<div class="border-grey-expand">
					<center><h3>PHYSICAL CHECK</h3></center>
					<input type='hidden' name='app_id' value='<?php echo $app_id ?>' />
					<input type='hidden' name='visit_count' value='<?php echo $visit_count ?>' />
					<table width="100%" id="prev_visit">
						<tr class="even" style="vertical-align:top">
							<td width="20%" class="header">Blood pressure</td>							
							<td><span id='show_data'><?php if(trim($details->blood_pressure) != "") {echo $details->blood_pressure;list($sistole,$diastole) = explode('/',$details->blood_pressure);} ?></span><span id='edit_data' class='hide'><input type="text" id="sistole" name="sistole" class="required digits" maxlength="3" size="3" value="<?php echo $sistole ?>" /> / <input type="text" class="required digits" id="diastole" name="diastole" maxlength="3" size="3" value="<?php echo $diastole ?>" /></span></td>
						</tr>
						<tr style="vertical-align:top">
							<td class="header">Pulse</td>							
							<td><span id='show_data'><?php echo $details->nadi ?></span><span id='edit_data' class='hide'><input type="text" class="required digits" id="nadi" name="nadi" maxlength="3" size="5" value="<?php echo $details->nadi ?>" /></span> times / minute</td>							 
						</tr>
						<tr class="even" style="vertical-align:top">
							<td class="header">Temp</td>							
							<td>
								<span id='show_data'><?php echo $details->temperature ?></span>
								<span id='edit_data' class='hide'><select id="temperature" name="temperature">
									<?php 
										$min = 34.0; // The minimun temperature
										$max = 42.0; // the maximum temperature
										
										$selected = 36;
										
										for ($i = $min ; $i <= $max ; $i += 0.5) {
											echo "<option value=\"$i\"". ($details->temperature == $i ? " selected=\"selected\"" : "" ).">$i</option>";
										}
									
									?>
								</select></span>
								&deg;C
							</td>
							
						</tr>
						<tr style="vertical-align:top">
							<td class="header">RR</td>							
							<td><span id='show_data'><?php echo $details->breath ?></span><span id='edit_data' class='hide'><input type="text" id="breath" name="breath" maxlength="2" size="2" value="<?php echo $details->breath ?>" /></span></td>							
						</tr>
						<tr class="even" style="vertical-align:top">
							<td class="header">Additional Notes</td>							
							<td><span id='show_data'><?php echo nl2br($details->physical_notes) ?></span>
							<span id='edit_data' class='hide'><textarea id="notes_physic" name="notes_physic" cols="50" rows="6" wrap='hard'><?php echo $details->physical_notes ?></textarea></span></td>
							
						</tr>
					</table>
				</div>
				<!--   END OF PHYSICAL CHECK UP ITEM LIST  -->
				
				<div class="border-grey-expand">
					<center><h3>LABORATORIUM</h3></center>
					<?php			
						if ($details->package_id != "") {
							echo "Lab package: $details->package_id";
						}
						if (!empty($lab)) {
							//echo "\t\t<ol>\n";
							echo "\t\t<table class='report' width='100%'>\n";
								echo "\t\t\t<tr>\n";
								echo "\t\t\t<th>No</th>";
								echo "\t\t\t<th>Item</th>";
								echo "\t\t\t<th>Specimen</th>";
								echo "\t\t\t<th>Result</th>";
								echo "\t\t\t<tr>\n";
								$i = 1;
								$class = "odd";
							foreach( $lab as $row ) {
								//echo "\t\t\t<li>$row->type ($row->specimen) $row->result</li>\n";
								echo "<tr class='$class'>";
								echo "\t\t\t<td>".$i++."</td><td>$row->type</td><td>$row->specimen</td><td>$row->result</td>\n";
								echo "</tr>";
								$class == "odd" ? $class = "even": $class="odd";
							}
							echo "</table>";
							//echo "\t\t</ol>\n";
						} else
							echo "<strong>No lab check request</strong>";
					?>
				</div>
				<div class="border-grey-expand">
					<center><h3>PROCEDURE</h3></center>
					<?php
						if (!empty($tindakan)) {
							echo "\t\t<ol>\n";
							foreach( $tindakan as $row ) {
								echo "\t\t\t<li>$row->tindakan</li>\n";
							}
							echo "\t\t</ol>\n";
						} else
							echo "<strong>No medical procedure taken</strong><br /><br />";
					?>
					<table id="prev_visit" width="100%">
						<tr class="even">
							<td class="header" width="20%">Working Diagnosis</td>
							<td><span id='show_data'><?php echo trim($details->d_kerja) == "" ? "none" : $details->d_kerja ?></span><span id='edit_data' class='hide'><input type="text" id="d_kerja" name="d_kerja" value='<?php echo $details->d_kerja ?>' /></span>
						</tr>
						<tr>
							<td class="header">Dual Diagnosis</td>
							<td><span id='show_data'><?php echo trim($details->d_banding) != "" ? trim(nl2br($details->d_banding)) : "none" ?></span><span id='edit_data' class='hide'><textarea id="d_banding" name="d_banding" cols="30" rows="6" wrap='hard'><?php echo $details->d_banding ?></textarea></span></td>
							<br />
						</tr>
					</table>
				</div>
				<div class="border-grey-expand">
				
					<center><h3>MEDICINE</h3></center>
					<?php
						if (!empty($obat)) {
						
							echo "\t\t<table class='report' width='100%'>\n";
							echo "\t\t\t<tr>\n";
							echo "\t\t\t<th>No</th>";
							echo "\t\t\t<th>Name</th>";
							echo "\t\t\t<th>Amount</th>";
							echo "\t\t\t<th>Dosage</th>";
							echo "\t\t\t<tr>\n";
							$i = 1;
							$class = "odd";	
							foreach( $obat as $row ) {
								echo "<tr class='$class'>";
								echo "\t\t\t<td>".$i++."</td><td>$row->nama_obat</td><td>$row->amount $row->unit</td><td>$row->dosis</td>\n";
								echo "</tr>";
								$class == "odd" ? $class = "even" : $class = "odd";
							}
							echo "\t\t</table>\n";
						} else
							echo "<strong>No medicine given</strong>";
					?><br />
					<?php if (trim($details->other_meds) != "") : ?>
					
					<span id='show_data'><strong>Other Meds:</strong><br /><?php echo trim(nl2br($details->other_meds)) ?></span>
					<?php endif; ?>
					<span id='edit_data' class='hide'><strong>Other Meds:</strong><br /><textarea id="other_meds" name="other_meds" wrap='hard' cols="30" rows="6"><?php echo $details->other_meds ?></textarea></span>
				</div>
				<?php if (trim($details->rujukan_notes) != "") { ?>
				<div class="border-grey-expand">
					<center><h3>REFERRRAL (RUJUKAN)</h3></center>
					<?php echo nl2br($details->rujukan_notes) ?>				
				</div>
				<?php } ?>
				
				<?php if ($doctor->id == $this->session->userdata('id_doctor')) { ?>
					<br />
					<input type='button' id='edit_btn' value='Edit' /><span id='edit_mode' class='hide'><input type='submit' id='save_btn' value='Save' /> <input type='button' id='cancel_btn' value='Cancel' /></span>
					</form>
				<?php } ?>
				<center><a href="javascript:window.close()">Close window</a></center>
			</div>
		</div>
<?php require "footer.php" ?>