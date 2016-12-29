<?php require "header.php" ?>
<?php if (isset($change_pass) && ($change_pass == TRUE)) { ?>
	Password succesfully changed!
<?php } else if (isset($change_pass) && ($change_pass == FALSE)) { ?>
	Password change failed.
<?php } else { ?>

	<?php if(isset($msg)){ ?>
		<div id="message"><?php echo $msg ?></div>
	<?php } ?>
<h3>CHANGE PASSWORD</h3>
<table border='0'>
	<form method='post' action='<?php echo $baseURL ?>index.php/general/change_pass'>
	<input type="hidden" name="id_admin" value="<?php echo $this->session->userdata('user_id') ?>" />
	<tr>
		<td>Enter your old password</td>
		<td><input type="password" name="tx_old" /></td>
	</tr>
	<tr>
		<td>Enter your new password</td>
		<td><input type="password" name="tx_new" /></td>
	</tr>
	<tr>
		<td>Re-enter your new password</td>
		<td><input type="password" name="tx_new_confirm" /></td>
	</tr>
	<tr>
		<td colspan="2"><input type='submit' value='Submit' />&nbsp;<input type='reset' value='Reset' /></td>
	</tr>
	</form>
</table>
<?php } ?>

<?php require "footer.php" ?>