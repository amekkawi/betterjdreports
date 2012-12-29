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

<?php $this->OutputHiddenInputs('verify'); ?>

<p><input type="checkbox" id="Verify" name="verify" value="1" /><label for="Verify"><b> Yes, purge <?php echo count($_GET['filters']) > 0 ? '' : 'ALL'; ?> reports from the &quot;<?php echo HTML::Encode($clientid); ?>&quot; client<?php echo count($_GET['filters']) > 0 ? ' which match the following filters:' : '.'; ?></b></label></p>

<?php
if (count($_GET['filters']) > 0) {
	?><ul><?php
	foreach ($_GET['filters'] as $key => $filter) {
		$parts = explode('-', $filter, 3);
		
		if ($parts[0] == 'tags' && $parts[2] == '') {
			?><li><?php echo HTML::Encode($parts[1] == 'i' ? 'Has at least one tag set' : 'Does not have a tag set');  ?> [<a href="<?php echo HTML::URL(array_merge($_GET, array('removefilter' => $key))); ?>">Remove</a>]</li><?php
		}
		else {
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
			?><li><?php echo HTML::Encode($column . ' ' . $flag . ' "' . $parts[2] . '"');  ?></li><?php
		}
	}
	?></ul><?php
}
?>

<p style="padding-top: 16px;"><input type="submit" value="Purge Reports" /> - or - <a href="<?php echo HTML::URL('reports', 'search', array(), $_GET); ?>">Cancel</a></p>

</form>
