<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
	$get_student_id = $_GET['student_id'];
	include "../include/connect.php";
	try {
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$query = "SELECT DISTINCT sessions.session_code,classes.class_name,students.student_id,students.student_name,subjects.subject_name,results.marks FROM results JOIN classes ON classes.class_id=results.class_id JOIN sessions ON sessions.session_id=results.session_id JOIN students ON students.student_id=results.student_id JOIN subjects ON subjects.subject_id=results.subject_id WHERE results.student_id=$get_student_id";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$result = $stmt->fetchAll();
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
								<th>Subject</th>
								<?php
								foreach ($result as $result_session) {
								?>
								<th><?php echo $result_session['session_code']; ?></th>
								<?php
								}
								?>
								<th>Average</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result as $result_subj) { // List the subjects and scores using foreach loop
							?>
							<tr>
								<td><?php echo $result_subj['subject_name']; ?></td>
								<td><?php echo $result_subj['marks']; ?></td>
							<?php
								try {
									$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
									$avg_query = "SELECT AVG(marks) FROM results WHERE student_id=$get_student_id";
									$avg_stmt = $conn->prepare($avg_query);
									$avg_stmt->execute();
									$average = $avg_stmt->fetch(PDO::FETCH_COLUMN);
								}catch(PDOException $e) {
									echo "Error: " .$e->getMessage();
							    }
							?>
							    <td></td>
							<?php
							} // Close the Subjects foreach loop
							?>
						    </tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</body>