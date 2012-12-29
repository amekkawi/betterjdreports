<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

if (count($report) == 0) {
	?><table class="norecords" align="center" border="0" cellspacing="0" cellpadding="12"><tr><td>Report not found for the client ID, job and report time specified.</td></tr></table><?php
}
else {
?>

<script type="text/javascript">document.operations = <?php echo HTML::toJSON($this->operations); ?>;<?php ?></script>

<div style="padding-bottom: 12px;">
<table align="center" class="styledtable" border="1" cellspacing="0" cellpadding="6">
	<tbody>
		<tr class="odd">
			<th>Client</th>
			<td><div class="searchlink">
					<a class="searchlink" href="<?php echo HTML::URL('reports', 'search', array(), array('filters' => array('clientid-x-' . $report[0]['clientid']))); ?>"><img src="<?php echo HTML::URL('images/search16x16.png'); ?>" border="0" width="16" height="16" alt="Search" hspace="2" vspace="2" /></a>
					<?php echo HTML::Encode($report[0]['clientid']); ?>
				</div></td>
		</tr>
		<tr class="even">
			<th>Job</th>
			<td><div class="searchlink">
					<a class="searchlink" href="<?php echo HTML::URL('reports', 'search', array(), array('filters' => array('clientid-x-' . $report[0]['clientid'], 'job-x-' . $report[0]['job']))); ?>"><img src="<?php echo HTML::URL('images/search16x16.png'); ?>" border="0" width="16" height="16" alt="Search" hspace="2" vspace="2" /></a>
					<?php echo HTML::Encode($report[0]['job']); ?>
				</div></td>
		</tr>
		<tr class="odd">
			<th>Disk</th>
			<td><?php echo HTML::Encode($report[0]['disk']); ?></td>
		</tr>
		<tr class="odd">
			<th>Report Time</th>
			<td><?php echo HTML::Encode(date('D, j M Y g:i:s A T', intval($report[0]['reporttime']))); ?></td>
		</tr>
		<tr class="even">
			<th>Duration</th>
			<td><?php echo HTML::Encode($this->FormatDuration($report[0]['duration'])); ?></td>
		</tr>
		<tr class="odd">
			<th>Uploaded Bytes</th>
			<td><?php echo HTML::Encode($this->FormatBytes($report[0]['uploadsize'])); ?> of
				<?php echo HTML::Encode($this->FormatBytes($report[0]['totalsize'])); ?></td>
		</tr>
		<tr class="even">
			<th>Uploaded Files</th>
			<td><?php echo HTML::Encode(number_format($report[0]['changedfiles'])); ?> of
				<?php echo HTML::Encode(number_format($report[0]['totalfiles'])); ?></td>
		</tr>
		<tr class="odd">
			<th>Result (Errors)</th>
			<td><?php echo HTML::Encode($report[0]['result']); ?> (<?php echo HTML::Encode($report[0]['errors']); ?>)<?php
				if ($report[0]['errors'].'' !== '0') {
					?> <a href="<?php
					echo HTML::URL(array_merge($_GET, array('page' => '1', 'filters' => array('result-i-OK')))); ?>">Search for Errors</a><?php
				}
			?></td>
		</tr>
	</tbody>
</table>
</div>

<form id="ItemsSearch" method="GET" action="<?php echo HTML::URL(array()); ?>">

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
		<option value="logorder">Order</option>
		<option value="type">Operation</option>
		<option value="path">Path</option>
		<option value="size2">Uploaded Bytes</option>
		<option value="starttime">Start Time</option>
		<option value="duration">Duration</option>
		<option value="result">Result</option>
		<option value="detail">Detail</option>	
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
	<table border="0" align="center" cellspacing="0" cellpadding="0"><tr><td>
	<ul>
	<?php
	foreach ($_GET['filters'] as $key => $filter) {
		$parts = explode('-', $filter, 3);
		switch ($parts[0]) {
			case 'logorder': $column = 'Order'; break;
			case 'type': $column = 'Operation'; break;
			case 'path': $column = 'Path'; break;
			case 'size2': $column = 'Uploaded Bytes'; break;
			case 'starttime': $column = 'Start Time'; break;
			case 'duration': $column = 'Duration'; break;
			case 'result': $column = 'Result'; break;
			case 'detail': $column = 'Detail'; break;
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
		?><li><?php echo HTML::Encode($column . ' ' . $flag . ' "' . ($parts[0] == 'type' ? $this->FormatOperation(intval($parts[2])) : $parts[2]) . '"');  ?> [<a href="<?php echo HTML::URL(array_merge($_GET, array('removefilter' => $key))); ?>">Remove</a>]</li><?php
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
if (count($items['records']) == 0) {
	?><table class="norecords" align="center" border="0" cellspacing="0" cellpadding="12"><tr><td>No items found.</td></tr></table><?php
}
else {
	if ($items['totalpages'] > 1) { ?><div class="pagertop"><?php $this->Pager($items); ?></div><?php } ?>
	
<table cellspacing="0" cellpadding="4" border="1" align="center" class="styledtable">
	<thead>
		<tr>
			<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'logorder', 'sortdir' => $_GET['sortcol'] != 'logorder' || $_GET['sortdir'] == 'desc' ? 'asc' : 'desc')));; ?>">Order</a></th>
			<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'type', 'sortdir' => $_GET['sortcol'] != 'type' || $_GET['sortdir'] == 'desc' ? 'asc' : 'desc')));; ?>">Oper</a></th>
			<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'path1', 'sortdir' => $_GET['sortcol'] != 'path1' || $_GET['sortdir'] == 'desc' ? 'asc' : 'desc')));; ?>">Path</a></th>
			<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'size2', 'sortdir' => $_GET['sortcol'] != 'size2' || $_GET['sortdir'] == 'asc' ? 'desc' : 'asc')));; ?>">Uploaded&nbsp;Bytes</a></th>
			<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'starttime', 'sortdir' => $_GET['sortcol'] != 'starttime' || $_GET['sortdir'] == 'desc' ? 'asc' : 'desc')));; ?>">Start Time</a></th>
			<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'duration', 'sortdir' => $_GET['sortcol'] != 'duration' || $_GET['sortdir'] == 'asc' ? 'desc' : 'asc')));; ?>">Duration</a></th>
		</tr>
	</thead>
	<tbody>
		<?php
		for ($i = 0; $i < count($items['records']); $i++) {
			?>
			<tr class="<?php echo $i % 2 == 0 ? 'odd' : 'even'; ?> <?php if ($items['records'][$i]['result'] != 'OK') echo 'errorrow'; ?>">
			
				<td><?php echo HTML::Encode($items['records'][$i]['logorder']); ?></td>
				<td><?php echo HTML::Encode($this->FormatOperation($items['records'][$i]['type'])); ?></td>
				<td><?php echo HTML::Encode($items['records'][$i]['path1'].$items['records'][$i]['path2'].$items['records'][$i]['path3']); ?></td>
				
				<td align="right"><?php echo str_replace(' ', '&nbsp;', HTML::Encode($this->FormatBytes($items['records'][$i]['size2']))); ?> of
					<?php echo str_replace(' ', '&nbsp;', HTML::Encode($this->FormatBytes($items['records'][$i]['size1']))); ?></td>
				
				<td><?php echo HTML::Encode(date('g:i:s A T', intval($items['records'][$i]['starttime']))); ?></td>
				
				<td align="center"><?php
				
				$duration = intval($items['records'][$i]['duration']);
				if ($duration > 60 * 60 * 24) {
					echo HTML::Encode('~' . number_format(round($duration / (60*60), 1), 1) . ' hrs');
				}
				else {
					$duration -= ($hours = floor($duration / (60*60))) * 60 * 60;
					$duration -= ($minutes = floor($duration / 60)) * 60;
					echo HTML::Encode(($hours > 0 ? $hours . ':' . ($minutes < 10 ? '0' : '') : '') . $minutes . ':' . ($duration < 10 ? '0' : '') . $duration);
				}
				
				 ?></td>
				
			</tr>
			<?php
			
			if ($items['records'][$i]['result'] != 'OK') {
				?>
				<tr class="<?php echo $i % 2 == 0 ? 'even' : 'odd'; ?> errorrow">
					<td>&nbsp;</td>
					<td colspan="5" class="errormsg"><i><?php echo HTML::Encode($items['records'][$i]['result']); ?>: </i> <?php echo HTML::Encode($items['records'][$i]['detail']); ?></td>
				</tr>
				<?php
			}
		}
		?>
	</tbody>
</table>
		<?php if ($items['totalpages'] > 1) { ?><div class="pagerbottom"><?php $this->Pager($items); ?></div><?php }
	
	}
}
?>