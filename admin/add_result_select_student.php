<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
	$session_id = $_GET['session_id'];
	$class_id = $_GET['class_id'];
	try {
		include "../include/connect.php";
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$query = "SELECT * FROM students WHERE class_id=$class_id";
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
	<title>Create Result</title>
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
				<h1>Add Results</h1>
			</div>
			<div id="body">
				<div id="output">
					<table>
						<thead>
							<tr>
								<th>#</th>
								<th>Students</th>
								<th>Roll Id</th>
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
								<td><?php echo $result['roll_id'] ?></td>
								<td><a href="add_result.php?session_id=<?php echo $session_id; ?>&class_id=<?php echo $class_id; ?>&student_id=<?php echo $result['student_id']; ?>">Create Result</a></td>
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