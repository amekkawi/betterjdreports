<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */
?>

<form method="GET" action="<?php echo HTML::URL(array()); ?>">

<?php
$this->OutputHiddenInputs('name');

if (isset($errors) && count($errors) > 0) {
	echo '<ul>';
	foreach ($errors as $error) {
		echo '<li>' . HTML::Encode($error) . '</li>';
	}
	echo '</ul>';
}

?>

<?php $row = 0; ?>
<table class="styledtable" border="1" cellspacing="0" cellpadding="6">
	<?php if (isset($oldname)) {?>
	<tr class="<?php echo (++$row % 2 == 0 ? 'even' : 'odd'); ?>">
		<th>Old Name:</th>
		<td><?php echo HTML::Encode($oldname); ?></td>
	</tr>
	<?php } ?>
	<tr class="<?php echo (++$row % 2 == 0 ? 'even' : 'odd'); ?>">
		<th><?php if (isset($oldname)) echo 'New '; ?>Name:</th>
		<td><input id="BookmarkName" type="text" name="name" value="<?php echo HTML::Encode($_GET['name']); ?>" style="width: 300px" /></td>
	</tr>
	<tr class="<?php echo (++$row % 2 == 0 ? 'even' : 'odd'); ?>">
		<th>URL:</th>
		<td><a href="<?php echo $testlink; ?>" target="_blank">Click to Test</a></td>
	</tr>
</table>

<div style="padding-top: 12px;"><input type="submit" value="<?php echo HTML::Encode($buttontext); ?>" /></div>

<script type="text/javascript">
$('#BookmarkName').focus();
</script>

</form>