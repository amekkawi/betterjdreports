<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */
?>

<form id="LatestSearch" method="GET" action="<?php echo HTML::URL(array()); ?>">

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
		<option value="job">Job</option>
		<option value="reporttime">Report Time</option>
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
	
	<td style="font-size: 11px;"><a href="<?php echo $this->BookmarkLink(); ?>"><img src="<?php echo HTML::URL('images/bookmark-add22x22.png'); ?>" width="22" height="22" alt="" border="0" /></a></td>
	
	<td>
	<ul>
	<?php
	foreach ($_GET['filters'] as $key => $filter) {
		$parts = explode('-', $filter, 3);
		switch ($parts[0]) {
			case 'clientid': $column = 'Client ID'; break;
			case 'job': $column = 'Job'; break;
			case 'reporttime': $column = 'Report Time'; break;
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
if (count($latest['records']) == 0) {
	// TODO
}
else {

	if ($latest['totalpages'] > 1) { ?><div class="pagertop"><?php $this->Pager($latest); ?></div><?php }
	?>

<table cellspacing="0" cellpadding="4" border="1" align="center" class="styledtable">
	<thead>
		<tr>
			<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'clientid', 'sortdir' => $_GET['sortcol'] != 'clientid' || $_GET['sortdir'] == 'desc' ? 'asc' : 'desc')));; ?>">Client</a></th>
			<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'job', 'sortdir' => $_GET['sortcol'] != 'job' || $_GET['sortdir'] == 'desc' ? 'asc' : 'desc')));; ?>">Job</a></th>
			<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'reporttime', 'sortdir' => $_GET['sortcol'] != 'reporttime' || $_GET['sortdir'] == 'asc' ? 'desc' : 'asc')));; ?>">Last Report</a> (Age)</th>
			<th><a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'result', 'sortdir' => $_GET['sortcol'] != 'result' || $_GET['sortdir'] == 'desc' ? 'asc' : 'desc')));; ?>">Result</a>
				(<a href="<?php echo HTML::URL(array_merge($_GET, array('sortcol' => 'errors', 'sortdir' => $_GET['sortcol'] != 'errors' || $_GET['sortdir'] == 'asc' ? 'desc' : 'asc')));; ?>">Errors</a>)</th>
		</tr>
	</thead>
	<tbody>
		<?php
		for ($i = 0; $i < count($latest['records']); $i++) {
			?>
			<tr class="<?php echo $i % 2 == 0 ? 'even' : 'odd'; ?>">
				
				<td><div class="searchlink">
					<a class="searchlink" href="<?php echo HTML::URL(array_merge($_GET, array('search'=> array('field' => 'clientid', 'filter' => 'x', 'text' => $latest['records'][$i]['clientid'])))); ?>"><img src="<?php echo HTML::URL('images/search16x16.png'); ?>" border="0" width="16" height="16" alt="Search" hspace="2" vspace="2" /></a>
					<?php echo HTML::Encode($latest['records'][$i]['clientid']); ?>
				</div></td>
				
				<td><div class="searchlink">
					<a class="searchlink" href="<?php echo HTML::URL(array_merge($_GET, array('search'=> array('field' => 'job', 'filter' => 'x', 'text' => $latest['records'][$i]['job'])))); ?>"><img src="<?php echo HTML::URL('images/search16x16.png'); ?>" border="0" width="16" height="16" alt="Search" hspace="2" vspace="2" /></a>
					<?php echo HTML::Encode($latest['records'][$i]['job']); ?>
				</div></td>
				
				<td><a class="showvisted" href="<?php echo HTML::URL('reports' , 'show', array($latest['records'][$i]['clientid'], $latest['records'][$i]['job'], $latest['records'][$i]['reporttime'])); ?>"><?php echo HTML::Encode(date('D, j M Y g:i:s A T', intval($latest['records'][$i]['reporttime']))); ?></a>
				
				(<?php
				$timediff = time() - intval($latest['records'][$i]['reporttime']);
				if ($timediff < 60) {
					echo $timediff . '&nbsp;sec';
				}
				elseif ($timediff < 60 * 60) {
					echo ceil($timediff / 60) . '&nbsp;min';
				}
				elseif ($timediff < 60 * 60 * 24) {
					echo round($timediff / 60 / 60, 1) . '&nbsp;hours';
				}
				else {
					echo round($timediff / 60 / 60 / 24, 1) . '&nbsp;days';
				}
				
				?>)</td>
				<td><?php echo HTML::Encode($latest['records'][$i]['result']); ?><?php
				
					if (intval($latest['records'][$i]['errors']) > 0) {
						?> <a href="<?php echo HTML::URL('reports' , 'show', array($latest['records'][$i]['clientid'], $latest['records'][$i]['job'], $latest['records'][$i]['reporttime']), array('filters' => array('result-i-OK'))); ?>">(<?php
						echo HTML::Encode($latest['records'][$i]['errors']);
						?>)</a><?php
					} ?></td>
			</tr>
			<?php
		} ?>
	</tbody>
</table>

	<?php
	if ($latest['totalpages'] > 1) { ?><div class="pagerbottom"><?php $this->Pager($latest); ?></div><?php }
}
?>
