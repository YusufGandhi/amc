<?php require "header.php" ?>
			<?php echo $custom_message ?>
			
			<?php 
				if(isset($print_lab)) {
					echo "<br /><br />";
					echo anchor($link,"Print the result",array( 'target' => '_blank'));
					if(isset($hiv_link))
						echo " - ".anchor($hiv_link,"Print HIV result",array( 'target' => '_blank'));
				}
			?>
<?php require "footer.php" ?>