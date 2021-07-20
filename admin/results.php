<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
	try {
		include "../include/connect.php";
		$query = "SELECT DISTINCT students.student_id,students.student_name,students.roll_id,students.reg_date,students.status,classes.class_name FROM results JOIN students ON students.student_id=results.student_id JOIN classes ON classes.class_id=results.class_id";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$result = $stmt->fetchAll();
		$numbering = 1;
	} catch(PDOException $e) {
		echo "Error: " .$e->getMessage();
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Results</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/style.css">
	<link rel="stylesheet" type="text/css" href="../css/menubar.css">
	<link rel="stylesheet" type="text/css" href="../css/outputable.css">
</head>
<body>
	<?php include "include/topbar.php"; ?>
	<div id="bottom">
		<?php include "include/sidebar.php"; ?>
		<div>
			<div id="head">
				<h1>Results</h1>
			</div>
			<div id="body">
				<div id="output">
					<table>
						<thead>
							<tr>
								<th>#</th>
								<th>Student Name</th>
								<th>Roll Id</th>
								<th>Class</th>
								<th>Date Registered</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result as $result) {
							?>
							<tr>
								<td><?php echo $numbering; ?></td>
								<td><?php echo $result['student_name']; ?></td>
								<td><?php echo $result['roll_id']; ?></td>
								<td><?php echo $result['class_name']; ?></td>
								<td><?php echo $result['reg_date']; ?></td>
								<td><?php if($result['status'] == 1) { echo "Active"; } else { echo "Inactive"; } ?></td>
								<td><a href="student_results.php?student_id=<?php echo $result['student_id'] ?>">View Results</a></td>
							</tr>
							<?php
							$numbering = $numbering + 1;
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</body>