<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
	$session_id = $_GET['session_id'];
	try {
		include "../include/connect.php";
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$query = "SELECT * FROM classes";
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
								<th>Class</th>
								<th>Number</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result as $result) {
							?>
							<tr>
								<td><?php echo $numbering; ?></td>
								<td><?php echo $result['class_name']; ?></td>
								<td><?php echo $result['class_number'] ?></td>
								<td><a href="add_result_select_student.php?session_id=<?php echo $session_id; ?>&class_id=<?php echo $result['class_id']; ?>">Create Result</a></td>
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