<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
	$subject_id = $_GET['subject_id'];
	include "../include/connect.php";
	if (strcasecmp($_SERVER['REQUEST_METHOD'], "POST") === 0) {
		$_SESSION['postdata'] = $_POST;
		header("Location: " .$_SERVER['PHP_SELF']. "?" .$_SERVER['QUERY_STRING']);
		exit;
	}

	// Define the add class variables and set to null
	$subject_name = $subject_code = "";
	$subject_namerr = $subject_coderr = "";
	$success_notice = "";

	if (isset($_SESSION['postdata'])) {
		$_POST = $_SESSION['postdata'];
		// Validate the add class form
		include "../include/val_function.php";
		if (empty($_POST['subject_name'])) {
			$subject_namerr = "Subject name is required.";
		} else {
			$subject_name = val_input($_POST['subject_name']);
		}
		if (empty($_POST['subject_code'])) {
			$subject_coderr = "Please enter a code for the subject.";
		}  else {
			$subject_code = val_input($_POST['subject_code']);
		}
		try {
			include "../include/connect.php";
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$query = "SELECT * FROM subjects WHERE subject_name=:subject_name OR subject_code=:subject_code";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(':subject_name', $subject_name);
			$stmt->bindParam(':subject_code', $subject_code);
			$stmt->execute();
			$count = $stmt->rowCount();
			$result = $stmt->fetchAll();
			foreach ($result as $result) {
				if ($count == 1 && $subject_name == $result['subject_name']) {
					$subject_namerr = "Subject already exist.";
				}
				if ($count == 1 && $subject_code == $result['subject_code']) {
					$subject_coderr = "Subject code already exist";
				}
			}
			$data_ok = empty($subject_namerr || $subject_coderr);
			if ($data_ok) { // Submit the data
				$query = "UPDATE subjects SET subject_name=:subject_name, subject_code=:subject_code WHERE subject_id=$subject_id ";
				$stmt = $conn->prepare($query);
				$stmt->bindParam(':subject_name', $subject_name);
				$stmt->bindParam(':subject_code', $subject_code);
				$stmt->execute();
				$success_notice = "Subject edited successfully.";
			}
		} catch(PDOException $e) {
			echo "Error: " .$e->getMessage();	
		} 
	}
	unset($_SESSION['postdata']);
	try {
		if ($subject_id = $_GET['subject_id']) {
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$query = "SELECT * FROM subjects WHERE subject_id=$subject_id";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$result = $stmt->fetchAll();
		}
	} catch(PDOException $e) {
		echo "Error: " .$e->getMessage();	
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Create New Subject</title>
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
				<h1>Add Subjects</h1>
			</div>
			<div id="body">
				<div id="input">
					<form method="post">
						<?php
						echo $success_notice;
						foreach ($result as $result) {
						?>
						<table>
							<tbody>
								<tr>
									<td><label for="subject_name">Subject: </label></td>
									<td>
										<input type="text" name="subject_name" value="<?php echo $result['subject_name'] ?>">
										<span class="error">*<br><?php echo "$subject_namerr"; ?></span>
									</td>
								</tr>
								<tr>
									<td><label for="subject_code">Subject Code: </label></td>
									<td>
										<input type="text" name="subject_code" value="<?php echo $result['subject_code'] ?>">
										<span class="error">*<br><?php echo "$subject_coderr"; ?></span>
									</td>
								</tr>
								<tr><td><button type="submit">Edit Subject</button></td></tr>
							</tbody>
						</table>
						<?php
						}
						?>
					</form>	
				</div>
			</div>
		</div>
	</div>
</body>