<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */
?>

<h2 align="center">Bookmarks</h2>

<?php
if (count($bookmarks) == 0) {
	?><table class="norecords" align="center" border="0" cellspacing="0" cellpadding="12"><tr><td>No bookmarks.</td></tr></table><?php
}
else {
	?>

	<table align="center" border="0" celspacing="0" cellpadding="3" border="0">
	<?php
	foreach ($bookmarks as $bookmark) {
		?>
		<tr>
			<td><img src="<?php echo HTML::URL('images/bookmark16x16.png'); ?>" width="16" height="16" alt="" border="0"/></td>
			<td><a href="<?php echo HTML::URL($bookmark['controller'], $bookmark['action'], $bookmark['arguments'], $bookmark['querystring']); ?>"><?php echo HTML::Encode($bookmark['name']); ?></a></td>
		</tr>
		<?php	
	}
	?>
	</table>
	<?php
}
?>

<h2 align="center">Recent History</h2>

<?php
if ($history['total'] == 0) {
	?><table class="norecords" align="center" border="0" cellspacing="0" cellpadding="12"><tr><td>No history.</td></tr></table><?php
}
else {
	/*if ($history['totalpages'] > 1) { ?><div class="pagertop"><?php $this->Pager($history); ?></div><?php }*/
	
	?>
	<table align="center" cellspacing="0" cellpadding="4" border="1" class="styledtable">
		<thead>
			<tr>
				<th>Target</th>
				<th>Description</th>
				<th>Date/Time</th>
			</tr>
		</thead>
		<tbody>
			<?php
			for ($i = 0; $i < count($history['records']); $i++) {
				?>
				<tr class="<?php echo $i % 2 == 0 ? 'odd' : 'even'; ?>">
					<td valign="top" nowrap="nowrap"><?php echo HTML::Encode($history['records'][$i]['target']); ?></td>
					<td valign="top"><?php echo HTML::Encode($history['records'][$i]['description']); ?></td>
					<td valign="top" nowrap="nowrap"><?php echo HTML::Encode(date('D, j M Y g:i:s A T', intval($history['records'][$i]['eventtime']))); ?></td>
					</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	
	<?php
	/*if ($history['totalpages'] > 1) { ?><div class="pagerbottom"><?php $this->Pager($history); ?></div><?php }*/
}
?>