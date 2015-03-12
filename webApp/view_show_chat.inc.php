<?php

// check input
if(!empty($_GET['chat_id']))
	$active_chat_id = intval($_GET['chat_id']);
else
	$active_chat_id = -1;

?>

<style type="text/css">

#main {
	width: 420px;
	max-height: 600px;
	overflow: auto;
}

</style>

<div id="main"></div>

<script src="http://code.jquery.com/jquery-1.11.2.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$('#main').load('config.inc.php?view=\"display_messages\"&chat_id=<?php echo $active_chat_id ?>');
	setInterval(function() {
		$('#main').load('config.inc.php?view=\"display_messages\"&chat_id=<?php echo $active_chat_id ?>');
	}, 2000);

</script>

<?php

require 'view_enter_message.inc.php';

?>