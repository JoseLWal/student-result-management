<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
	try {
		include "../include/connect.php";
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stu_query = "SELECT student_id FROM students";
		$sub_query = "SELECT subject_id FROM subjects";
		$cls_query = "SELECT class_id FROM classes";
		$res_query = "SELECT result_id FROM results";
		$stu_stmt = $conn->prepare($stu_query);
		$sub_stmt = $conn->prepare($sub_query);
		$cls_stmt = $conn->prepare($cls_query);
		$res_stmt = $conn->prepare($res_query);
		$stu_stmt->execute();
		$sub_stmt->execute();
		$cls_stmt->execute();
		$res_stmt->execute();
	} catch(PDOException $e) {
		echo "Error: " .$e->getMessage();
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Dashboard</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/style.css">
	<link rel="stylesheet" type="text/css" href="../css/menubar.css">
	<style type="text/css">
		#body {
			display: grid;
			grid-template-columns: auto auto auto auto;
			grid-column-gap: 20px;
		}
	</style>
</head>
<body>
	<?php include "include/topbar.php"; ?>
	<div id="bottom">
		<?php include "include/sidebar.php"; ?>
		<div>
			<div id="head">
				<h1>Dashboard</h1>
			</div>
			<div id="body">
				<div id="totstudents" class="total">
					<h3><?php echo $stu_stmt->rowCount(); ?></h3>
					<p>Students</p>
				</div>
				<div id="totsubjects" class="total">
					<h3><?php echo $sub_stmt->rowCount(); ?></h3>
					<p>Subjects</p>
				</div>
				<div id="totclasses" class="total">
					<h3><?php echo $cls_stmt->rowCount(); ?></h3>
					<p>Classes</p>
				</div>
				<div id="totresults" class="total">
					<h3><?php echo $res_stmt->rowCount(); ?></h3>
					<p>Results</p>
				</div>
			</div>
		</div>
	</div>
	

</body>
</html>