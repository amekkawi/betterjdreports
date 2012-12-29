<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */
?>

<form id="ReportsSearch" method="GET" action="<?php echo HTML::URL(array()); ?>">

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
		<?php if (!isset($_GET['purge'])) { ?>
		<option value="clientid">Client ID</option>
		<?php } ?>
		<option value="job">Job</option>
		<option value="reporttime">Report Time</option>
		<option value="duration">Duration</option>
		<option value="uploadsize">Uploaded Bytes</option>
		<option value="changedfiles">Uploaded Files</option>
		<option value="result">Result</option>
		<option value="errors">Errors</option>	
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
	
	<?php if (is_null($_GET['purge'])) { ?>
	<td style="font-size: 11px;"><a href="<?php echo $this->BookmarkLink(); ?>"><img src="<?php echo HTML::URL('images/bookmark-add22x22.png'); ?>" width="22" height="22" alt="" border="0" /></a></td>
	<?php } ?>
	
	<td>
	<ul>
	<?php
	foreach ($_GET['filters'] as $key => $filter) {
		$parts = explode('-', $filter, 3);
		switch ($parts[0]) {
			case 'clientid': $column = 'Client ID'; break;
			case 'job': $column = 'Job'; break;
			case 'reporttime': $column = 'Report Time'; break;
			case 'duration': $column = 'Duration'; break;
			case 'uploadsize': $column = 'Uploaded Bytes'; break;
			case 'changedfiles': $column = 'Uploaded Files'; break;
			case 'result': $column = 'Result'; break;
			case 'errors': $column = 'Errors'; break;	
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
	?>
	</ul>
	</td></tr></table>
	<?php
}
?>

</div>

</form>

<?php
if ($results['total'] == 0) {
	?><table class="norecords" align="center" border="0" cellspacing="0" cellpadding="12"><tr><td>No reports found.</td></tr></table><?php
}
else {
	
	if (!is_null($_GET['purge'])) {
		?><div class="divcenter" style="padding-bottom: 16px;"><div class="divcenter-inner">
			<a href="<?php echo HTML::URL('reports', 'purge', array($_GET['purge']), $_GET); ?>" style="margin-right: 10px" id="PurgeReports">Purge Matched Reports</a>
			<a href="<?php echo isset($_GET['returnto']) ? HTML::URL($_GET['returnto']) : HTML::URL('clients', 'show', array($_GET['purge'])); ?>" id="CancelPurge">Cancel</a>
		</div></div><div style="clear: both;"></div>
		<script type="text/javascript">
		$('#PurgeReports').button({ color: 'red', focusable: true });
		$('#CancelPurge').button({ focusable: true });
		</script>
		<?php
	}

	if ($results['totalpages'] > 1) { ?><div class="pagertop"><?php $this->Pager($results); ?></div><?php }
	
	?>
	<table cellspacing="0" cellpadding="4" border="1" align="center" class="styledtable">
		<thead>
			<tr>
				<?php if (!isset($_GET['purge'])) { ?>
				<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'clientid', 'sortdir' => $_GET['sortcol'] != 'clientid' || $_GET['sortdir'] == 'desc' ? 'asc' : 'desc')));; ?>">Client</a></th>
				<?php } ?>
				
				<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'job', 'sortdir' => $_GET['sortcol'] != 'job' || $_GET['sortdir'] == 'desc' ? 'asc' : 'desc')));; ?>">Job</a></th>
				<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'reporttime', 'sortdir' => $_GET['sortcol'] != 'reporttime' || $_GET['sortdir'] == 'asc' ? 'desc' : 'asc'))); ?>">Report Time</a></th>
				<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'duration', 'sortdir' => $_GET['sortcol'] != 'duration' || $_GET['sortdir'] == 'asc' ? 'desc' : 'asc')));; ?>">Duration</a></th>
				
				<th>Uploaded <a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'uploadsize', 'sortdir' => $_GET['sortcol'] != 'uploadsize' || $_GET['sortdir'] == 'asc' ? 'desc' : 'asc')));; ?>">Bytes</a>/<a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'changedfiles', 'sortdir' => $_GET['sortcol'] != 'changedfiles' || $_GET['sortdir'] == 'asc' ? 'desc' : 'asc')));; ?>">Files</a></th>
				
				<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'result', 'sortdir' => $_GET['sortcol'] != 'result' || $_GET['sortdir'] == 'desc' ? 'asc' : 'desc')));; ?>">Result</a>
					(<a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'errors', 'sortdir' => $_GET['sortcol'] != 'errors' || $_GET['sortdir'] == 'asc' ? 'desc' : 'asc')));; ?>">Errors</a>)</th>
			</tr>
		</thead>
		<tbody>
			<?php
			for ($i = 0; $i < count($results['records']); $i++) {
				?>
				<tr class="<?php echo $i % 2 == 0 ? 'odd' : 'even'; ?>">
					
					<?php if (!isset($_GET['purge'])) { ?>
					<td><div class="searchlink">
						<a class="searchlink" href="<?php echo HTML::URL(array_merge($_GET, array('search'=> array('field' => 'clientid', 'filter' => 'x', 'text' => $results['records'][$i]['clientid'])))); ?>"><img src="<?php echo HTML::URL('images/search16x16.png'); ?>" border="0" width="16" height="16" alt="Search" hspace="2" vspace="2" /></a>
						<?php echo HTML::Encode($results['records'][$i]['clientid']); ?>
					</div></td>
					<?php } ?>
					
					<td><div class="searchlink">
						<a class="searchlink" href="<?php echo HTML::URL(array_merge($_GET, array('search'=> array('field' => 'job', 'filter' => 'x', 'text' => $results['records'][$i]['job'])))); ?>"><img src="<?php echo HTML::URL('images/search16x16.png'); ?>" border="0" width="16" height="16" alt="Search" hspace="2" vspace="2" /></a>
						<?php echo HTML::Encode($results['records'][$i]['job']); ?>
					</div></td>
					
					<td nowrap="nowrap" align="right"><a class="showvisted" href="<?php echo HTML::URL('reports', 'show', array($results['records'][$i]['clientid'], $results['records'][$i]['job'], $results['records'][$i]['reporttime'])); ?>"><?php echo HTML::Encode(date('D, j M Y', intval($results['records'][$i]['reporttime']))); ?><br/>
						<?php echo HTML::Encode(date('g:i:s A T', intval($results['records'][$i]['reporttime']))); ?></a></td>
					
					<td align="center"><?php
					
					$duration = intval($results['records'][$i]['duration']);
					if ($duration > 60 * 60 * 24) {
						echo HTML::Encode('~' . number_format(round($duration / (60*60), 1), 1) . ' hrs');
					}
					else {
						$duration -= ($hours = floor($duration / (60*60))) * 60 * 60;
						$duration -= ($minutes = floor($duration / 60)) * 60;
						echo HTML::Encode(($hours > 0 ? $hours . ':' . ($minutes < 10 ? '0' : '') : '') . $minutes . ':' . ($duration < 10 ? '0' : '') . $duration);
					}
					
					 ?></td>
					
					<td align="right"><?php echo HTML::Encode($this->FormatBytes($results['records'][$i]['uploadsize'])); ?> of
						<?php echo HTML::Encode($this->FormatBytes($results['records'][$i]['totalsize'])); ?>
						
						<br/>
					
					<?php echo HTML::Encode(number_format($results['records'][$i]['changedfiles'])); ?> of
						<?php echo HTML::Encode(number_format($results['records'][$i]['totalfiles'])); ?></td>
					
					<td><?php echo HTML::Encode($results['records'][$i]['result']);
						if (intval($results['records'][$i]['errors']) > 0)
							echo ' (' . HTML::Encode($results['records'][$i]['errors']) . ')'; ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<?php
	
	if ($results['totalpages'] > 1) { ?><div class="pagerbottom"><?php $this->Pager($results); ?></div><?php }
}
?>