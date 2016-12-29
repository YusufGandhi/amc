<?php require "header.php" ?>
	<form method="post" id="form-exam" action="<?php echo $baseURL ?>index.php/station_2/save_exam">
		<div style="margin:0 auto;width:400px;">
			
			
			
			<input type="hidden" name="app_id" value="<?php echo $app_id ?>" />
			<input type="hidden" name="mr_no" value="<?php echo $patient->mr_no ?>"/>
			<input type="hidden" name="date" id="date" value="<?php echo date("Y-m-d") ?>" />
			<input type="hidden" name="patient_type" value="RP"/>
			<input type="hidden" name="doc_type" value="<?php echo $doctor->type ?>" />
			<input type="hidden" name="other_name" value="<?php echo $doctor->other_doctor_name ?>"/>
			<input type="hidden" name="dd_list_doctor" id="id_doctor" value="<?php echo $doctor->id ?>"/>
			<input type="hidden" name="tx_keluhan" value="Continuation from previous visit" />
			<input type="hidden" name="tx_temp_diagnosis" value="Continuation from previous visit" />
			
			
			<div style="float:right">
				<table>
					<tr>
						<td>MR no.</td>
						<td>: <?php echo $patient->mr_no ?></td>
					</tr>
					<tr>
						<td>Date</td>
						<td>: <?php echo $patient->appointment_date ?></td>
					</tr>
					<tr>
						<td>Visit #</td>
						<td>: <?php echo $visit ?></td>
					</tr>
				</table>
			</div>
			<div>
				<table>
					<tr>
						<td>Name</td>
						<td>: <?php echo (trim($patient->full_name) == "" ? $patient->nickname : $patient->full_name) ?></td>
					</tr>
					<tr>
						<td>Age</td>
						<td>: <?php echo ($patient->yob != 0 ? ($curr_year - $patient->yob ) : "--") ?> years</td>
					</tr>
					<tr>
						<td>Sex</td>
						<td>: <?php echo $patient->sex ?></td>
					</tr>					
				</table>
			</div>			
		</div>
		
		
			<div style="font-size:0.85em">
				Previous visit data:
				
				<?php
					if (!empty($history)) {
						$i = 1;
						$atts = array(
			              'width'      => '600',
			              'height'     => '600',
			              'scrollbars' => 'yes',
			              'status'     => 'no',
			              'resizable'  => 'no',
			              'screenx'    => '0',
			              'screeny'    => '0'
			            );
						foreach($history as $row) {
							if ($i > 1) echo " - ";
							echo anchor_popup(site_url("station_2/visit_details/$row->app_id/$i"),$i,$atts);
							$i++;
						}
					} else {
						echo "No previous visit data available";
					}
				
					if($couple_app_id) {
				?>
					<div style="font-size:0.85em;float:right"><a href="<?php echo site_url("station_2/exam/$couple_app_id") ?>" target="_blank">See the couple details</a></div>
				<?php					
					}
				?>
				<hr />
			</div>
			
		<div>
			<div style="float:right;font-size:12px;width:400px;clear:both">
				<div style="border-bottom:1px">
					<input type='checkbox' name='next_appointment' id='next_appointment' value='Y' /><label for='next_appointment'>Schedule for next Appointment</label>
				</div>
				<div id="next_app_details" class='hide'>
					<div>
						<label for="datepicker">Date</label><input type="text" id="datepicker" value="<?php echo date("Y-m-d") ?>" />
					</div>
				
					<div id="room">
						<table>
							<tr>
								<td>Time</td>
								<td>
									From
									<select name="dd_list_hour_start" id="dd_list_hour_start">
										<option value=''>Select time</option>
									<?php
										foreach ($hour as $row) {
											echo "<option value='$row->id'>$row->hour</option>";
										}
									?>
									</select>
									to
									<select name="dd_list_hour_end" id="dd_list_hour_end">
										<option value=''>Select time</option>
									<?php
										foreach ($hour as $row) {
											echo "<option value='$row->id'>$row->end</option>";
										}
									?>
									</select>
								</td>
							</tr>
							<tr>
								<td>Room</td>
								<td>
									<select name="dd_list_room" id="dd_list_room">
									<?php
										foreach ($room as $row) {
											echo "<option value='$row->id'>$row->room_number</option>";
										}
									?>
									</select>						
								</td>
							</tr>
							<tr>
								<td>Nurse</td>
								<td>
								<select name="dd_list_nurse" id="dd_list_nurse">
									<?php 
										echo "<option value=''>Select Nurse</option>";
										if (!empty($nurse_list)) {
											foreach ($nurse_list as $row)
												echo "<option value='$row->id'>$row->name</option>";
										}
									?>
								</select>
								</td>
							</tr>
						</table>
					</div>				
					<div id="schedule">
					</div>
				</div>
			
				<div class="border-grey" style="width:400px">
					<h3>MEDICINE</h3>
						<input type="hidden" name="price_obat" id="price_obat" value="0" />
						<?php
							if (!empty($obat)) {
								$i = 1;
								echo "<table class='report' width='100%'>";
								echo "	<tr>";
								echo "		<th>Item</th>";
								echo "		<th>Type</th>";
								echo "		<th>Amount</th>";
								echo "		<th>Dosage</th>";
								echo "	</tr>";
								$class = "odd";
								foreach( $obat as $row ) {
									echo "<tr class='$class'>";
									echo "\t\t<td><input type=\"checkbox\" name=\"obat[$row->id]\" class=\"obat\" value=\"$row->price\" alt=\"$row->id\" id=\"obat$i\"><label for=\"obat$i\">$row->nama_obat</label></td><td>$row->jenis</td> <td><input type=\"text\" class=\"amount\" name=\"amount[$row->id]\" size='5' style=\"display:none;\" /> ".strtolower($row->unit)."</td><td><input type=\"text\" class=\"dosis\" name=\"dosis[$row->id]\" style=\"display:none;\" size='5' /></td>\n";
									echo "</tr>";
									$class = $class == "odd" ? "even" : "odd";
									$i++;
								}
								echo "</table>";
							} else
								echo "No Medicine List. Please report to the administrator";
						?>
						<br />
						<label for="other_meds">Other Medicine not listed above:</label><br />
						<textarea id="other_meds" name="other_meds" wrap='hard' cols='40'rows='5'></textarea><br /><br />
						
				</div>
				<div class="border-grey"  style="width:400px">
					<h3>REFERRAL (RUJUKAN)</h3>
					Please write notes below:<br />
					<textarea id="rujukan_notes" name="rujukan_notes" wrap='hard' cols='40'rows='5'></textarea>
				</div>
			</div>
			<div class="border-grey">
				<table border="0">
                  <tr>
                    <td style="vertical-align:top">Nurse</td>
                    <td style="vertical-align:top">:</td>
                    <td style="vertical-align:top"><?php echo $nurse ?></td>
                  </tr>
                  <tr>
                    <td style="vertical-align:top">Hotline Complaint</td>
					<td style="vertical-align:top">:</td>
                    <td style="vertical-align:top"><?php echo nl2br($keluhan) ?></td>
                  </tr>
                  <tr>
                    <td style="vertical-align:top">Temporary Diagnosis</td>
					<td style="vertical-align:top">:</td>
                    <td style="vertical-align:top"><?php echo nl2br($temp_diagnosis) ?></td>
                  </tr>
                </table>
			</div>
			<div class="border-grey">
				<table border="0">
				  <tr>
					<td><label for="main_complaint">Main Complaint:</label> <span class="required">*</span></td>
					<td><input type="text" id="main_complaint" name="main_complaint" class="required" /></td></td>
				  </tr>
				  <tr>
					<td><label for="anamnesa">Anamnesa:</label> <span class="required">*</span></td>
					<td><textarea name="anamnesa" id="anamnesa" rows="5" cols="38" wrap="hard" class="required" maxlength="20000"></textarea><br />Max: <label id="max_anamnesa"></label></td>
				  </tr>
				  
				</table>
			</div>				
			<div class="border-grey">
				<h3>PHYSICAL CHECK</h3>
				<table>
					<tr>					
						<td>Blood Pressure <span class="required">*</span></td>
						<td><input type="text" id="sistole" name="sistole" maxlength="3" size="3" class="required digits" /> / <input type="text" id="diastole" name="diastole" maxlength="3" size="3" class="required digits" /></td>
					</tr>
					<tr>
						<td>Pulse <span class="required">*</span></td>
						<td><input type="text" id="nadi" name="nadi" maxlength="3" size="5" class="required digits" /> times / minute</td>					
					</tr>
					<tr>
						<td>Temperature <span class="required">*</span></td>
						<td>
							<select id="temperature" name="temperature">
							<?php 
								$min = 34.0; // The minimun temperature
								$max = 42.0; // the maximum temperature
								
								$selected = 36;
								
								for ($i = $min ; $i <= $max ; $i += 0.5) {
									echo "<option value=\"$i\"". ($selected == $i ? " selected=\"selected\"" : "" ).">$i</option>";
								}
							
							?>
							</select><sup>o</sup>C
						</td>
					</tr>
					<tr>
						<td>Respiratory Rate (RR) <span class="required">*</span></td>
						<td><input type="text" id="breath" name="breath" maxlength="2" size="2" class="required digits" /></td>
					</tr>
					<tr>
						<td>Notes</td>
						<td><textarea id="notes_physic" name="notes_physic" cols="30" rows="6" wrap='hard'></textarea></td>
					</tr>
				</table>
			</div>
				
				
				<!--   END OF PHYSICAL CHECK UP ITEM LIST  -->
				
			<div class="border-grey">
				<h3>PACKAGE FOR LAB CHECK</h3>
				<input type="hidden" name="package-price" value="0" id="package-price" />
				<input type="hidden" name="package-id" id="package-id" />
				<span id="package-chosen"></span>
				<input type="button" id="change_paket" value="Change Package" style="display:none;" />
				<div id="paket_accordion" style="font-size:15px">
					<h3>Package 1</h3>
					<div>
						<table>
							<tr>
								<td style="vertical-align:top">Lab</td>
								<td style="vertical-align:top">:</td>
								<td>HIV + Sifilis (diagnosa)</td>
							</tr>
							<tr>
								<td>Procedure</td>
								<td>:</td>
								<td>Phlebotomi</td>
							</tr>
						</table>						
						<input type="button" class="paket" value="Choose" alt="1" style="float:right" title="257500" />
					</div>
					<h3>Package 2</h3>
					<div>
						<table>
							<tr>
								<td style="vertical-align:top">Lab</td>
								<td style="vertical-align:top">:</td>
								<td>GO + BV + Trikomonas + Kandida</td>
							</tr>
							<tr>
								<td>Procedure</td>
								<td>:</td>
								<td>Inspekulo</td>
							</tr>
						</table> 
						<input type="button" class="paket" value="Choose" alt="2" style="float:right" title="154500" />
					</div>
					<h3>Package 3</h3>
					<div>
						<table>
							<tr>
								<td style="vertical-align:top">Lab</td>
								<td style="vertical-align:top">:</td>
								<td>GO + BV + Trikomonas + Kandida + Pap smear</td>
							</tr>
							<tr>
								<td>Procedure</td>
								<td>:</td>
								<td>Pap smear</td>
							</tr>
						</table> 
						<input type="button" class="paket" value="Choose" alt="3" style="float:right" title="309000" />
					</div>
					<h3>Package 4</h3>
					<div>
						<table>
							<tr>
								<td style="vertical-align:top">Lab</td>
								<td style="vertical-align:top">:</td>
								<td>Gula Darah Sewaktu + Asam Urat + Kolesterol</td>
							</tr>
							<tr>
								<td>Procedure</td>
								<td>:</td>
								<td>Phlebotomi</td>
							</tr>
						</table>						
						<input type="button" class="paket" value="Choose" alt="4" style="float:right" title="61800" />
					</div>
					<h3>Package 5A</h3>
					<div>
						<table>
							<tr>
								<td style="vertical-align:top">Lab</td>
								<td style="vertical-align:top">:</td>
								<td>HIV + Sifilis (diagnosa) + GO</td>
							</tr>
							<tr>
								<td>Procedure</td>
								<td>:</td>
								<td>Phlebotomi + Anuscopy</td>
							</tr>
						</table>
						<input type="button" class="paket" value="Choose" alt="5A" style="float:right" title="309000" />
					</div>
					<h3>Package 5B</h3>
					<div>
						<table>
							<tr>
								<td style="vertical-align:top">Lab</td>
								<td style="vertical-align:top">:</td>
								<td>Sifilis (diagnosa) + HIV + GO + BV + Trikomonas + Kandida + Pap smear</td>
							</tr>
							<tr>
								<td>Procedure</td>
								<td>:</td>
								<td>Pap smear + Phlebotomi</td>
							</tr>
						</table>
						<input type="button" class="paket" value="Choose" alt="5B" style="float:right" title="412000" />
					</div>
					<h3>Package 6</h3>
					<div>
						<table>
							<tr>
								<td style="vertical-align:top">Lab</td>
								<td style="vertical-align:top">:</td>
								<td>Sifilis (diagnosa) + HIV + GO + BV + Trikomonas + Kandida</td>
							</tr>
							<tr>
								<td>Procedure</td>
								<td>:</td>
								<td>Phlebotomi</td>
							</tr>
						</table>
						<input type="button" class="paket" value="Choose" alt="6" style="float:right" title="302000" />
					</div>
				</div>
				<?php
					//for ($i=1; $i <= 5; ++$i)
						//echo "<input type=\"radio\" class=\"paket\" id=\"paket$i\" name=\"paket\" value=\"$i\" /> <label for=\"paket$i\">Paket $i</label><br />";
				?>
			</div>
				
			<div class="border-grey">
				<input type="hidden" name="price_lab" id="price_lab" value="0" />
				<h3>LABORATORIUM</h3>
				<?php			
					/*$i = 1;
					foreach( $lab as $row ) {
						echo "\t\t<input type=\"checkbox\" name=\"lab[$row->id]\" class=\"lab\" value=\"$row->id\" alt=\"$row->price\" id=\"lab$i\"><label for=\"lab$i\">$row->type</label><br />\n";
						$i++;
					}*/
					
					if(!empty($specimen)) {
						echo "<div id=\"specimen_accordion\" style=\"font-size:15px\">";
						foreach($specimen as $row) {
							echo "<h3>$row->specimen</h3>";
							echo "<div>";
							foreach($lab as $row2) {
								if ($row2->specimen == $row->id) {
									echo "<p><input type='checkbox' alt='$row2->price' class='lab' name='lab[$row2->id]' value='$row2->price' group='$row2->group' id='lab$row2->id' /><label for='lab$row2->id'>$row2->type</label></p>";
								}
							}
							echo "</div>";
						}
						echo "</div>";
					}
				
				?>
			</div>
				
			<div class="border-grey">
				<h3>LABORATORIUM PARAMITA</h3>
				<input type='hidden' id='total_price_paramita' name='total_price_paramita' value='0' />
				<label for="search_item_paramita">Search:</label><input type="text" id="search_item_paramita" /> <span class="hide red"></span>
				<div id="lab_paramita">
					<ul id="item_list_paramita">
					
					</ul>
				</div>
			</div>
			
			<div class="border-grey">
				<h3>PROCEDURE</h3>
				<input type="hidden" name="price_tindakan" id="price_tindakan" value="0" />
				<?php
					$i = 1;
					foreach( $tindakan as $row ) {
						echo "\t\t<p><input type=\"checkbox\" name=\"tindakan[$row->id]\" class=\"tindakan\" value=\"$row->price\" alt=\"$row->price\" id=\"tindakan$i\"><label for=\"tindakan$i\">$row->tindakan</label></p>\n";
						$i++;
					}
				?><br />
				<table>
					<tr>
						<td><label for="service_procedure">Service Procedure <span class="required">*</span></label> </td>
						<td><input type="text" name="service_procedure" id="service_procedure" class="required" /></td>					
					  </tr>
					<tr>
						<td>Working Diagnosis <span class="required">*</span></td>
						<td><input type="text" id="d_kerja" name="d_kerja" class="required" /></td>
					</tr>
					<tr>
						<td style="vertical-align:top">Dual Diagnosis</td>
						<td><textarea id="d_banding" name="d_banding" cols="30" rows="6" wrap='hard'></textarea></td>
				</table>
			</div>
			
			
		</div>
		</div>
		<p><input type="checkbox" value="Y" name="free_doctor_fee" id="free_doctor_fee" /><label for="free_doctor_fee"><strong>GIVE FREE CONSULTATION FEE!</strong></label></p>
		<input type="submit" value="Save Examination Details" />
		</form>
	
<?php require "footer.php" ?>