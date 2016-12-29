<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title><?php echo $title ?></title>
		<link rel="stylesheet" href="<?php echo $baseURL ?>login.css" />
		<script src="<?php echo $baseURL ?>js/jquery-1.4.4.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready ( function() {
				//alert("loaded");
				$("input#txt_user_id").focus();
			});
		</script>
		
	</head>
	<body>
		<div id="login">
			<form method="post" action="<?php echo $baseURL ?>index.php/welcome/login">  
			<fieldset>  
			<legend>Login AMC</legend>  
				<table>  
					<tr>  
						<td><label for="txt_user_id">Username</label></td><td><input name="txt_user_id" type="text" id="txt_user_id" size="30" /></td>  
					</tr>  
					<tr>  
						<td><label for="txt_pass">Password</label></td><td><input name="txt_pass" type="password" id="txt_pass" size="30" /></td>  
					</tr>  
					<tr>  
						<td class="submit"></td><td><input type="submit" value="Login" /></td>  
					</tr>
					<?php if (isset($msg)) { ?>
						<tr>
							<td colspan="2" class="message"><?php echo $msg ?></td>
						</tr>
					<?php } ?>    
				</table>

			</fieldset>  
			</form>
		</div>
	</body>
</html>
