			<?php if(isset($extra_msg)) : ?>
			<div id="extra_msg" style="display:none">
				<?php echo $extra_msg; ?>
			</div>
			<?php endif; ?>
		</div> <!-- end of div content -->
				
		<div id="footer">
			<div id="online-users" style="float:right">
				<div>Online users are listed here</div>
			</div>
			&copy;2010<?php echo (date('Y') > 2010 ? "-".date('Y') : "" ); ?> another app by Yusuf Gandhi
		</div>
	</div>
</body>
</html>
