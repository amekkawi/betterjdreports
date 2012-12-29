<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title><?php echo HTML::Encode(Config::Get('appname')); ?>: Login</title>
<?php
$this->includeCSS('css/general.css');
$this->outputIncludes();
?>
<style type="text/css">
#Content { width: 500px; }
</style>
<script type="text/javascript">
$(function() { $('#UserID').focus(); });
</script>
<!--AJAX-LOGINREQUESTED-->
</head>

<body>

<div id="Wrapper">

<div id="Header">
	<div id="TitleBlock">
		<div id="Title"><a href="<?php echo HTML::URL(''); ?>"><span><?php echo HTML::Encode(Config::Get('appname'))?></span></a></div>
	</div>
</div>

<div id="Content">

<h1>Login</h1>
<form action="<?php echo HTML::URL(); ?>" method="post">
<?php if (isset($loginmessage)) {
	echo '<p class="login-message">' . HTML::Encode($loginmessage) . '</p>';
}
?>
<table align="center" class="login-fields" border="0" cellspacing="0" cellpadding="4">
	<tbody>
		<tr>
			<th>Username:</th>
			<td><input style="width: 300px" type="text" id="UserID" name="ac_userid" value="<?php if (isset($_POST['ac_userid'])) echo HTML::Encode($_POST['ac_userid']); ?>" /></td>
		</tr>
		<tr>
			<th>Password:</th>
			<td><input style="width: 300px" type="password" id="Password" name="ac_password" /></td>
		</tr>
		<tr>
			<th></th>
			<td><input type="submit" id="Login" value="Login" /></td>
		</tr>
	</tbody>
</table>
</form>

</div>

</div>

</body>
</html>