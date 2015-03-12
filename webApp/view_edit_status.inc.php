<?php

if(!empty($_POST['status'])) {
	$query = "	UPDATE status
				SET `text` = :text,
					`datetime` = :datetime
				WHERE user_id = :user_id";

	$query_params = array(
		':text' => $_POST['status'],
		':datetime' => get_datetime(),
		':user_id' => $active_user_id
	);

	$stmt = exec_query($db, $query, $query_params);
	header('Location: index.php?view=edit_status&edited=1');
}

$edited = false;

if(isset($_GET['edited'])) {
	$edited = (intval($_GET['edited']) == 1) ? true : false;
}

$status = "";

$query = "SELECT * FROM status WHERE user_id = :user_id";
$query_params = array(
	':user_id' => $active_user_id
);

$stmt = exec_query($db, $query, $query_params);

$row = $stmt->fetch();
if($row) {
	$status = $row['text'];
}

?>

	<h2>Edit Status</h2>

<?php if(!$edited): ?>
	<form action="index.php?view=edit_status" method="post">
		Enter or change your current status:
		<br><br>
		<textarea type="text", name="status" row="50" cols="50"><?php echo $status; ?></textarea>
		<br><br>
		<input type="submit" value="Submit status">
	</form>
<?php else: ?>
	Your status has been updated!
	<br><br>
	<a href="index.php?view=home" style="display: block;">Home</a>
<?php endif; ?>