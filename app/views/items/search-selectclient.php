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
$this->OutputHiddenInputs(array('clientid', 'path', 'size', 'sizeflag'));
?>

<script type="text/javascript">
$(function() {
	$('#SizeFlag')[0].selectedIndex = 0;
	$('#SizeFlag').val('<?php echo isset($_GET['sizeflag']) ? HTML::JavascriptStringEncode($_GET['sizeflag']) : ''; ?>');
});
</script>

<table cellspacing="0" cellpadding="3" border="0" align="center">
<tbody>

<tr>
	<td nowrap="nowrap" colspan="2" align="right"><b>Client ID (required):</b></td>
	<td><input type="text" name="clientid" value="<?php if (is_string($_GET['clientid'])) echo HTML::Encode($_GET['clientid']); ?>" style="width: 250px" /></td>
</tr>

<tr>
	<td colspan="3">&nbsp;</td>
</tr>

<tr>
	<td nowrap="nowrap" colspan="2" align="right"><b>Path starts with:</b></td>
	<td><input type="text" name="path" value="<?php if (is_string($_GET['path'])) echo HTML::Encode($_GET['path']); ?>" style="width: 250px" /></td>
</tr>

<tr>
	<td colspan="2" align="right">and/or</td>
	<td>&nbsp;</td>
</tr>

<tr>
	<td nowrap="nowrap"><b>Size:</b></td>
	<td><select id="SizeFlag" name="sizeflag">
		<option value="g">is greater than</option>
		<option value="l">is less than</option>
		<option value="x">is exactly</option>
		<option value="i">is not</option>
	</select></td>
	<td><input type="text" name="size" value="<?php if (is_string($_GET['size'])) echo HTML::Encode($_GET['size']); ?>" style="width: 250px" /></td>
</tr>

<tr>
	<td colspan="2">&nbsp;</td>
	<td><input type="submit" value="Next &gt;&gt;" /></td>
</tr>

</tbody></table>

<?php
if (count($errors) > 0) {
	?><table align="center" border="0" cellspacing="0" cellpadding="0"><tr><td><ul><?php
	foreach ($errors as $error) {
		?><li><?php echo HTML::Encode($error); ?></li><?php
	}
	?></ul></td></tr></table><?php
}
?>
</form>