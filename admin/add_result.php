<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
	$session_id = $_GET['session_id'];
	$class_id = $_GET['class_id'];
	$student_id= $_GET['student_id'];
	$grade = $graderr = "";
	$success_notice = "";
	try {
		include "../include/connect.php";
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
	<title>Create Result</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/style.css">
	<link rel="stylesheet" type="text/css" href="../css/menubar.css">
	<link rel="stylesheet" type="text/css" href="../css/input.css">
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
				<div id="input">
					<form method="post">
						<?php echo $success_notice; ?>
						<table>
							<thead>
								<tr>
									<th>#</th>
									<th>Subjects</th>
									<th>Grade Input</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($result as $result) {
								?>
								<tr>
									<td><?php echo $numbering; ?></td>
									<td><label for="grade"><?php echo $result['subject_name']; ?><label></td>
									<td>
										<input type="number" name="grade" max="100">
										<span class="error"><br><?php echo $graderr; ?></span>
										<?php $subject_id = $result['subject_id']; ?>
									</td>
								</tr>
								<?php
								$numbering = $numbering + 1;

									if (strcasecmp($_SERVER['REQUEST_METHOD'], "POST") === 0) {
										$_SESSION['postdata'] = $_POST;
										header("Location: " .$_SERVER['PHP_SELF']. "?" .$_SERVER['QUERY_STRING']);
										exit;
									}
									if (isset($_SESSION['postdata'])) {
										$_POST = $_SESSION['postdata'];
										// Validate the add grade input
										include "../include/val_function.php";
										if (!empty($_POST['grade'])) {
											$grade = val_input($_POST['grade']);
											if ($grade > 100) {
												$graderr = "Student grade can not exceed 100%";
											}
										}
										$data_ok = empty($graderr);
										if ($data_ok) {
											try {
												$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
												$query = "INSERT INTO results (session_id, student_id, class_id, subject_id, marks)
												VALUES (:session_id, :student_id, :class_id, :subject_id, :mark)";
												$stmt = $conn->prepare($query);
												$stmt->bindParam(':session_id', $session_id);
												$stmt->bindParam(':student_id', $student_id);
												$stmt->bindParam(':class_id', $class_id);
												$stmt->bindParam(':subject_id', $subject_id);
												$stmt->bindParam(':mark', $grade);
												$stmt->execute();
												$success_notice = "New class created successfully";
											} catch(PDOException $e) {
												echo "Error " .$e->getMessage();
											}
										}
									}
									unset($_SESSION['postdata']);
								}
								?>
								<tr><td><button type="submit">Create Result</button></td></tr>
							</tbody>
						</table>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>