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
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo Config::Get('charset'); ?>" />
<title><?php echo HTML::Encode(Config::GET('appname')); ?>: Database Error</title>
<link href="<?php echo HTML::URL('core/css/error.css'); ?>" type="text/css" rel="stylesheet"/>

<style type="text/css">
ul {
	margin: 7px 0;
	padding-left: 40px;
}
</style>

<script type="text/javascript" src="<?php echo HTML::URL('js/jquery/jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo HTML::URL('js/general.js'); ?>"></script>
<script type="text/javascript" src="<?php echo HTML::URL('js/jquery/ui/jquery.ui.core.js'); ?>"></script>
<script type="text/javascript" src="<?php echo HTML::URL('js/jquery/ui/jquery.ui.widget.js'); ?>"></script>
<script type="text/javascript" src="<?php echo HTML::URL('js/jquery/ui/jquery.ui.mouse.js'); ?>"></script>
<script type="text/javascript" src="<?php echo HTML::URL('js/jquery/ui/jquery.ui.position.js'); ?>"></script>
<script type="text/javascript" src="<?php echo HTML::URL('js/dblog.js'); ?>"></script>
<script type="text/javascript">document.dblog = <?php echo HTML::ToJSON($GLOBALS['DBLOG'])?>;</script>
</head>

<body>
	<div id="Container">
		<h1>Error: Database</h1>
		<p>An error was encountered while accessing the database<?php if (isset($errorin)) echo ' in ' . HTML::Encode($errorin); ?>:</p>
		<p><?php echo HTML::Encode($errormessage); ?><br/>&nbsp;</p>
		<pre><?php if (isset($debuginfo)) echo HTML::Encode($debuginfo); ?></pre>
	</div>
</body>
</html>