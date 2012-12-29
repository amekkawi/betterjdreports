<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */
?>

<h2>Quick Searches</h2>

<ul>
	<li><a href="<?php echo HTML::URL('reports', 'latest', array(), array('filters' => array('clientid-x-' . $client['clientid']))); ?>">Latest Reports</a></li>
	<li><a href="<?php echo HTML::URL('reports', 'search', array(), array('filters' => array('clientid-x-' . $client['clientid']))); ?>">Reports Search</a></li>
	<li><a href="<?php echo HTML::URL('items', 'search', array(), array('clientid' => $client['clientid'])); ?>">File Search</a></li>
</ul>

<h2>Metadata<?php if ($update) { ?> <span class="normalfont">[<a href="#">Edit</a>]</span><?php } ?></h2>

<blockquote>
	<table cellspacing="0" cellpadding="6" border="1" class="styledtable">
		<thead>
			<tr>
				<?php if ($update) { ?><th>&nbsp;</th><?php } ?>
				<th>Type</th>
				<th>Data</th>
			</tr>
		</thead>
		<tbody>
			<?php
			for ($i = 0; $i < count($metadata); $i++) {
				?>
				<tr class="<?php echo $i % 2 == 0 ? 'odd' : 'even'; ?>">
					<?php if ($update) { ?>
						<td valign="top"><a href="<?php echo HTML::URL('metadata', 'update', array($client['clientid'], $metadata[$i]['type'])); ?>"><img src="<?php echo HTML::URL('images/edit16x16.png'); ?>" width="16" height="16" alt="" border="0" /></a>
							<a href="<?php echo HTML::URL('metadata', 'remove', array($client['clientid'], $metadata[$i]['type'])); ?>"><img src="<?php echo HTML::URL('images/remove16x16.png'); ?>" width="16" height="16" alt="" border="0" /></a></td>
					<?php } ?>
					<td valign="top" nowrap="nowrap"><b><?php echo HTML::Encode($metadata[$i]['type']); ?></b></td>
					<td valign="top"><?php echo HTML::Pre($metadata[$i]['data']); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	
	<div style="margin-top: 12px;"><img src="<?php echo HTML::URL('images/add16x16.png'); ?>" align="left" width="16" height="16" alt="" border="0" hspace="6" /> <a href="<?php echo HTML::URL('metadata', 'add', array($client['clientid'])); ?>">Add Metadata</a></div>
</blockquote>

<h2>Tags<?php if ($update) { ?> <span class="normalfont">[<a href="#">Edit</a>]</span><?php } ?></h2>

<blockquote>
	<form method="GET" action="<?php echo HTML::URL('tags', 'add'); ?>">
		<input type="hidden" name="clientid" value="<?php echo HTML::Encode($client['clientid']); ?>" />
		<input type="hidden" name="returnto" value="<?php echo HTML::Encode(HTML::URL()); ?>" />
		
		<table border="0" cellspacing="0" cellpadding="3" style="position: relative; left: -16px;">
			<?php
			if (count($tags) == 0) {
				?><tr>
					<td>&nbsp;</td>
					<?php if ($update) { ?><td>&nbsp;</td><?php } ?>
					<td><i>No tags set.</i></td>
				</tr><?php
			}
			else {
				for ($i = 0; $i < count($tags); $i++) {
					?><tr>
						<td><img src="<?php echo HTML::URL('images/tag16x16.png'); ?>" width="16" height="16" alt="" /></td>
						<?php if ($update) { ?><td><a href="<?php echo HTML::URL('tags', 'remove', array(), array('tag' => $tags[$i]['tag'], 'clientid' => $client['clientid'], 'returnto' => HTML::URL() )); ?>"><img src="<?php echo HTML::URL('images/remove16x16.png'); ?>" width="16" height="16" alt="" border="0" /></a></td><?php } ?>
						<td><a href="<?php echo HTML::URL('clients', 'search', array(), array('filters' => array('tags-x-' . $tags[$i]['tag']))); ?>"><?php echo HTML::Encode($tags[$i]['tag']); ?></a></td>
					</tr><?php
				}
			}
			
			if ($update) {
				?>
				<tr>
					<td>&nbsp;</td>
					<td><img src="<?php echo HTML::URL('images/add16x16.png'); ?>" width="16" height="16" alt="" /></td>
					<td><input type="text" name="tag" style="width: 150px;" /> <input type="submit" value="Add Tag" /></td>
				</tr>
				<?php
			} ?>
		</table>
	</form>
</blockquote>

<h2>Jobs<?php if ($purge && count($jobs) > 0) { ?> <span class="normalfont">[<a href="#">Purge</a>]</span><?php } ?></h2>

<blockquote>
<?php
if (count($jobs) == 0) {
	?><table class="norecords" border="0" cellspacing="0" cellpadding="12"><tr><td>No jobs found.</td></tr></table><?php
}
else {
	?>
	<table cellspacing="0" cellpadding="4" border="1" class="styledtable">
		<thead>
			<tr>
				<?php if ($purge) { ?><th class="purgecol">&nbsp;</th><?php } ?>
				<th>Job</th>
				<th>Reports</th>
			</tr>
		</thead>
		<tbody>
			<?php
			for ($i = 0; $i < count($jobs); $i++) {
				?>
				<tr class="<?php echo $i % 2 == 0 ? 'odd' : 'even'; ?>">
					<?php if ($purge) { ?><td class="purgecol"><a href="<?php echo HTML::URL('reports', 'search', array(), array('purge' => $jobs[$i]['clientid'], 'filters' => array('job-x-' . $jobs[$i]['job']))); ?>"><img src="<?php echo HTML::URL('images/remove16x16.png'); ?>" width="16" height="16" alt="" border="0" /></a></td><?php } ?>
					<td><a href="<?php echo HTML::URL('reports', 'search', array(), array('filters' => array('clientid-x-' . $jobs[$i]['clientid'], 'job-x-' . $jobs[$i]['job']))); ?>"><?php echo HTML::Encode($jobs[$i]['job']); ?></a></td>
					<td align="right"><?php echo HTML::Encode($jobs[$i]['jobcount']); ?></td>
				</tr>
				<?php
			}
			?>
			<tr class="<?php echo $i % 2 == 0 ? 'odd' : 'even'; ?>">
				<?php if ($purge) { ?><td class="purgecol"><a href="<?php echo HTML::URL('reports', 'search', array(), array('purge' => $client['clientid'])); ?>"><img src="<?php echo HTML::URL('images/remove16x16.png'); ?>" width="16" height="16" alt="" border="0" /></a></td><?php } ?>
				<td><b>Total</b></td>
				<td align="right"><?php echo HTML::Encode($client['totalreports']); ?></td>
			</tr>
		</tbody>
	</table>
	<?php
}
?>
</blockquote>

<h2>Recent History</h2>

<blockquote>
<?php
if ($history['total'] == 0) {
	?><table class="norecords" align="center" border="0" cellspacing="0" cellpadding="12"><tr><td>No history.</td></tr></table><?php
}
else {
	?>
	<table cellspacing="0" cellpadding="4" border="1" class="styledtable">
		<thead>
			<tr>
				<th>Target</th>
				<th>User</th>
				<th>Description</th>
				<th>Date/Time</th>
			</tr>
		</thead>
		<tbody>
			<?php
			for ($i = 0; $i < count($history['records']); $i++) {
				?>
				<tr class="<?php echo $i % 2 == 0 ? 'odd' : 'even'; ?>">
					<td valign="top"><?php echo HTML::Encode($history['records'][$i]['target']); ?></td>
					<td valign="top"><?php echo HTML::Encode($history['records'][$i]['userid']); ?></td>
					<td valign="top"><?php echo HTML::Encode($history['records'][$i]['description']); ?></td>
					<td valign="top" nowrap="nowrap"><?php echo HTML::Encode(date('D, j M Y g:i:s A T', intval($history['records'][$i]['eventtime']))); ?></td>
					</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<?php
}
?>
</blockquote>