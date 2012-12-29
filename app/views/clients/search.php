<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */
?>

<form id="ClientsSearch" method="GET" action="<?php echo HTML::URL(array()); ?>">

<?php

$this->OutputHiddenInputs(array('search[field]', 'search[filter]', 'search[text]'));

if (isset($initialSearchValues)) {
	?><script type="text/javascript">document.initialSearchValues = <?php echo HTML::ToJSON($initialSearchValues); ?>;</script><?php
}

?>

<div style="padding-bottom: 10px;">
<table cellspacing="0" cellpadding="3" border="0" align="center">
<tbody><tr>

	<td><img width="28" height="28" alt="" src="<?php echo HTML::URL('images/search.gif'); ?>" /></td>

	<td nowrap="nowrap"><b>Search for:</b></td>

	<td><select name="search[field]">
		<option value="clientid">Client ID</option>
		<option value="totalreports">Total Reports</option>
		<option value="tags">Tags</option>
	</select></td>

	<td><select name="search[filter]">
		<option value="x">is exactly</option>
		<option value="c">contains (text only)</option>
		<option value="s">starts with (text only)</option>
		<option value="e">ends with (text only)</option>
		<option value="g">is greater than (numbers only)</option>
		<option value="l">is less than (numbers only)</option>
		<option value="i">is not</option>
	</select></td>

	<td><input type="text" name="search[text]" value="" style="width: 250px" /></td>
	<td><input type="submit" value="Add Search Criteria" /></td>

</tr></tbody></table>

<?php
if (count($_GET['filters']) > 0) {
	?>
	<table border="0" align="center" cellspacing="0" cellpadding="0"><tr>
	
	<td style="font-size: 11px;"><a href="<?php echo $this->BookmarkLink(); ?>"><img src="<?php echo HTML::URL('images/bookmark-add22x22.png'); ?>" width="22" height="22" alt="" border="0" /></a></td>
	
	<td>
	<ul>
	<?php
	foreach ($_GET['filters'] as $key => $filter) {
		$parts = explode('-', $filter, 3);
		
		if ($parts[0] == 'tags' && $parts[2] == '') {
			?><li><?php echo HTML::Encode($parts[1] == 'i' ? 'Has at least one tag set' : 'Does not have a tag set');  ?> [<a href="<?php echo HTML::URL(array_merge($_GET, array('removefilter' => $key))); ?>">Remove</a>]</li><?php
		}
		else {
			switch ($parts[0]) {
				case 'clientid': $column = 'Client ID'; break;
				case 'totalreports': $column = 'Total Reports'; break;
				case 'tags': $column = 'Tag'; break;	
			}
			switch ($parts[1]) {
				case 'x': $flag = 'is exactly'; break;
				case 'c': $flag = 'contains'; break;
				case 's': $flag = 'starts with'; break;
				case 'e': $flag = 'ends with'; break;
				case 'g': $flag = 'is greater than'; break;
				case 'l': $flag = 'is less than'; break;
				case 'i': $flag = 'is not'; break;
			}
			?><li><?php echo HTML::Encode($column . ' ' . $flag . ' "' . $parts[2] . '"');  ?> [<a href="<?php echo HTML::URL(array_merge($_GET, array('removefilter' => $key))); ?>">Remove</a>]</li><?php
		}
	}
	?>
	</ul>
	</td></tr></table>
	<?php
}
?>

</div>

</form>

<?php
if ($clients['total'] == 0) {
	?><table class="norecords" align="center" border="0" cellspacing="0" cellpadding="12"><tr><td>No clients found.</td></tr></table><?php
}
else {
	if ($clients['totalpages'] > 1) { ?><div class="pagertop"><?php $this->Pager($clients); ?></div><?php }
	
	?>
	<table cellspacing="0" cellpadding="4" border="1" align="center" class="styledtable">
		<thead>
			<tr>
				<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'clientid', 'sortdir' => $_GET['sortcol'] != 'clientid' || $_GET['sortdir'] == 'desc' ? 'asc' : 'desc')));; ?>">Client ID</a></th>
				<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'totalreports', 'sortdir' => $_GET['sortcol'] != 'totalreports' || $_GET['sortdir'] == 'asc' ? 'desc' : 'asc')));; ?>">Reports</a></th>
				<th>Tags</th>
				<th>Jobs (Total Reports)</th>
			</tr>
		</thead>
		<tbody>
			<?php
			for ($i = 0; $i < count($clients['records']); $i++) {
				$reportTotal = 0;
				?>
				<tr class="<?php echo $i % 2 == 0 ? 'odd' : 'even'; ?>">
					<td valign="top"><a href="<?php echo HTML::URL('clients', 'show', array($clients['records'][$i]['clientid'])); ?>"><?php echo HTML::Encode($clients['records'][$i]['clientid']); ?></a></td>
					<td valign="top" align="right"><a href="<?php echo HTML::URL('reports', 'search', array(), array('filters' => array('clientid-x-' . $clients['records'][$i]['clientid']))); ?>"><?php echo HTML::Encode(number_format(intval($clients['records'][$i]['totalreports']))); ?></a></td>
				
					<td valign="top"><?php
						if (count($clients['records'][$i]['tags']) == 0) {
							echo '&nbsp;';
						}
						else {
							for ($t = 0; $t < count($clients['records'][$i]['tags']); $t++) {
								?><div class="searchlink" style="float: left;">
									<a class="searchlink" href="<?php echo HTML::URL(array_merge($_GET, array('search'=> array('field' => 'tags', 'filter' => 'x', 'text' => $clients['records'][$i]['tags'][$t])))); ?>"><img src="<?php echo HTML::URL('images/search16x16.png'); ?>" border="0" width="16" height="16" alt="Search" hspace="2" vspace="2" /></a>
									<?php echo HTML::Encode($clients['records'][$i]['tags'][$t]) . ($t < count($clients['records'][$i]['tags']) - 1 ? ',&nbsp;' : ''); ?>
								</div><?php
							}
						}
					?></td>
					
					<td valign="top"><?php
						if (count($clients['records'][$i]['jobs']) == 0) {
							echo '&nbsp;';
						}
						else {
							for ($t = 0; $t < count($clients['records'][$i]['jobs']); $t++) {
								$reportTotal += intval($clients['records'][$i]['jobs'][$t]['total']);
								?><span style="white-space: nowrap;"><a href="<?php echo HTML::URL('reports', 'search', array(), array('filters' => array('clientid-x-' . $clients['records'][$i]['clientid'], 'job-x-' . $clients['records'][$i]['jobs'][$t]['job']))); ?>"><?php echo HTML::Encode($clients['records'][$i]['jobs'][$t]['job']); ?></a> (<?php echo HTML::Encode($clients['records'][$i]['jobs'][$t]['total']); ?>)<?php echo $t < count($clients['records'][$i]['jobs']) - 1 ? ',&nbsp;' : '' ?></span><?php
							}
						}
					?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	
	<?php
	if ($clients['totalpages'] > 1) { ?><div class="pagerbottom"><?php $this->Pager($clients); ?></div><?php }
}
?>