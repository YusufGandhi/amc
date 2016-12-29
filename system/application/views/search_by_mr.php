<?php require "header.php" ?>
	<form id="form-search" method="post" action="<?php echo site_url('station_2/result'); ?>">
		<p class="ui-widget" style="font-size:12px;"><label for="search_patient">Search patient</label> <input type="text" id="search_patient" /></p>
		<input type="hidden" id="tx_mr_no" name="tx_mr_no" />
	</form>
	<?php if (isset($data_visit)) : ?>
		Visit Details of <?php echo $mr_no ?>
		<table>
			<tr>
				<th>Date</th>
				<th>Visit ID</th>
				<th>Doctor</th>
			</tr>
		<?php 
			$i=1;
			foreach( $data_visit as $row) {			
		?>
			<tr>
				<td><?php echo $row->date ?></td>
				<td><?php echo $row->app_id ?></td>
				<td><?php echo $row->doctor ?></td>
				<td><?php echo anchor_popup(site_url("station_2/visit_details/$row->app_id/$i"),"View",$atts);?></td>
			</tr>
		<?php $i++;} ?>
		</table>
	<?php endif; ?>
<?php require "footer.php" ?>