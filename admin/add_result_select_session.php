<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
	try {
		include "../include/connect.php";
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$query = "SELECT * FROM sessions";
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
								<th>Sessions</th>
								<th>Code</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result as $result) {
							?>
							<tr>
								<td><?php echo $numbering; ?></td>
								<td><?php echo $result['session_name']; ?></td>
								<td><?php echo $result['session_code'] ?></td>
								<td><a href="add_result_select_class.php?session_id=<?php echo $result['session_id']; ?>">Create Result</a></td>
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