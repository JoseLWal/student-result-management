<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Account</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/style.css">
	<link rel="stylesheet" type="text/css" href="../css/menubar.css">
</head>
<body>
	<?php include "include/topbar.php"; ?>
	<div id="bottom">
		<?php include "include/sidebar.php"; ?>
		<div>
			<div id="head">
				<h1>Account</h1>
			</div>
			<div id="body"></div>
		</div>
	</div>
</body>