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
<title><?php echo HTML::Encode(Config::Get('appname') . ($pagetitle != "" ? ": ".$pagetitle : "")); ?></title>
<?php
$this->includeCSS('css/general.css');
$this->includeJavascript('js/dblog.js');
$this->outputIncludes();

if (!isset($userinfo)) $userinfo = Session::Get('userinfo');

?>
<script type="text/javascript">document.baseurl = '<?php echo HTML::JavascriptStringEncode(HTML::URL('')); ?>'; document.dblog = <?php echo HTML::ToJSON($GLOBALS['DBLOG'])?>;</script>
</head>

<body>

<div id="Wrapper">

<div id="Header">
	<div id="TitleBlock">
		<div id="Title"><a href="<?php echo HTML::URL(''); ?>"><span><?php echo HTML::Encode(Config::Get('appname'))?></span></a></div>
		<div id="UserInfo">
			<div id="UserInfo-Links"><a href="<?php echo HTML::URL('users', 'view', array(AC::GetLoggedInUser())); ?>">Profile</a> - <a href="<?php echo HTML::URL('users', 'logout'); ?>">Logout</a></div>
			<div id="UserInfo-UserID"><?php echo HTML::Encode($userinfo['name']); ?> (<?php echo HTML::Encode(AC::GetLoggedInUser()); ?>)</div>
		</div>
	</div>
	<div id="Navi">
		<div id="NaviInner">
			<div id="NaviCenter">
				<ul>
					<li<?php if (isset($selectednavi) && $selectednavi == "Home") echo ' class="selected"'; ?>><a href="<?php echo HTML::URL(''); ?>">Home</a></li>
					<li<?php if (isset($selectednavi) && $selectednavi == "Latest") echo ' class="selected"'; ?>><a href="<?php echo HTML::URL('reports', 'latest'); ?>">Latest</a></li>
					<li<?php if (isset($selectednavi) && $selectednavi == "Clients") echo ' class="selected"'; ?>><a href="<?php echo HTML::URL('clients', 'search'); ?>">Clients</a></li>
					<li<?php if (isset($selectednavi) && $selectednavi == "Reports") echo ' class="selected"'; ?>><a href="<?php echo HTML::URL('reports', 'search'); ?>">Reports</a></li>
					<li<?php if (isset($selectednavi) && $selectednavi == "Files") echo ' class="selected"'; ?>><a href="<?php echo HTML::URL('items', 'search'); ?>">File Search</a></li>
					<li<?php if (isset($selectednavi) && $selectednavi == "Admin") echo ' class="selected"'; ?>><a href="<?php echo HTML::URL('admin'); ?>">Admin</a></li>
				</ul>
			</div>
			<div style="clear: both;">
		</div>
	</div>
</div>

<div id="Content">
	<?php if (is_string($pagetitle) && $pagetitle != "") { ?><h1><?php echo HTML::Encode($pagetitle); ?></h1><?php } ?>
	<?php echo $viewhtml; ?>
</div>

</div>

</body>
</html>
