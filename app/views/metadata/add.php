<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */
?>

<form method="GET" action="<?php echo HTML::URL(); ?>">

<table border="0" cellspacing="0" cellpadding="4">
	<tbody>
		<tr>
			<th align="right" nowrap="nowrap">Client ID:</th>
			<td><?php echo HTML::Encode($clientid); ?></td>
		</tr>
		<tr>
			<th align="right">Type:</th>
			<td><input type="text" name="type" value="<?php if (isset($_GET['type'])) echo HTML::Encode($_GET['type']); ?>" style="width: 200px;" /></td>
		</tr>
		<tr>
			<th align="right" valign="top">Data:</th>
			<td><textarea name="data" style="width: 700px;" rows="15"><?php if (isset($_GET['data'])) echo HTML::Encode($_GET['data']); ?></textarea></td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td><input type="submit" value="<?php echo $this->pageTitle; ?>" /> - or - <a href="<?php echo isset($_GET['returnto']) ? HTML::URL($_GET['returnto']) : HTML::URL('clients', 'show', array($clientid)); ?>">Cancel</a></td>
		</tr>
	</tbody>
</table>

</form>