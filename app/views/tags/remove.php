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
$this->OutputHiddenInputs('verify');
?>

<p>Remove the '<?php echo HTML::Encode($_GET['tag']); ?>' tag from the client <?php echo HTML::Encode($_GET['clientid'])?>?</p>

<p style="padding-top: 16px;"><input type="submit" name="verify" value="Confirm"/> - or - <a href="<?php echo isset($_GET['returnto']) ? $_GET['returnto'] : HTML::URL('clients', 'show', array($_GET['clientid'])); ?>">Cancel</a></p>

</form>