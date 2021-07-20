<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
	try {
		include "../include/connect.php";
		$query = "SELECT * FROM subjects ORDER BY subject_name";
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
	<title>Subjects</title>
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
				<h1>Subjects</h1>
			</div>
			<div id="body">
				<div id="output">
					<table>
						<thead>
							<tr>
								<th>#</th>
								<th>Subject</th>
								<th>Subject Code</th>
								<th>Date Created</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result as $result) {
							?>
							<tr>
								<td><?php echo $numbering; ?></td>
								<td><?php echo $result['subject_name']; ?></td>
								<td><?php echo $result['subject_code']; ?></td>
								<td><?php echo $result['date_created'] ?></td>
								<td><a href="edit_subject.php?subject_id=<?php echo $result['subject_id']; ?>">Edit</a></td>
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