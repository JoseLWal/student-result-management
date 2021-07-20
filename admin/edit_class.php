<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
	$class_id = $_GET['class_id'];
	include "../include/connect.php";
	if (strcasecmp($_SERVER['REQUEST_METHOD'], "POST") === 0) {
		$_SESSION['postdata'] = $_POST;
		header("Location: " .$_SERVER['PHP_SELF']. "?" .$_SERVER['QUERY_STRING']);
		exit;
	}

	// Define the add class variables and set to null
	$class_name = $class_number = "";
	$class_namerr = $class_numberr = "";
	$success_notice = "";

	if (isset($_SESSION['postdata'])) {
		$_POST = $_SESSION['postdata'];
		// Validate the add class form
		include "../include/val_function.php";
		if (empty($_POST['class_name'])) {
			$class_namerr = "Class name is required.";
		} else {
			$class_name = val_input($_POST['class_name']);
		}
		if (empty($_POST['class_number'])) {
			$class_numberr = "Please enter a numeric figure for the class.";
		}  else {
			$class_number = val_input($_POST['class_number']);
		}
		try {
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$query = "SELECT * FROM classes WHERE class_name=:class_name OR class_number=:class_number AND class_id<>$class_id";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(':class_name', $class_name);
			$stmt->bindParam(':class_number', $class_number);
			$stmt->execute();
			$count = $stmt->rowCount();
			$result = $stmt->fetchAll();
			foreach ($result as $result) {
				if ($count == 2 && $class_name == $result['class_name']) {
					$class_namerr = "Class name already exist.";
				}
				if ($count == 2 && $class_number == $result['class_number']) {
					$class_numberr = "Class number already exist";
				}
			}
			$data_ok = empty($class_namerr || $class_numberr);
			if ($data_ok) { // Submit the data
				$query = "UPDATE classes SET class_name=:class_name, class_number=:class_number WHERE class_id=$class_id ";
				$stmt = $conn->prepare($query);
				$stmt->bindParam(':class_name', $class_name);
				$stmt->bindParam(':class_number', $class_number);
				$stmt->execute();
				$success_notice = "Class successfully updated.";
			}
		} catch(PDOException $e) {
			echo "Error: " .$e->getMessage();	
		} 
	}
	unset($_SESSION['postdata']);
	try {
		if ($class_id = $_GET['class_id']) {
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$query = "SELECT * FROM classes WHERE class_id=$class_id";
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
	<title>Edit Class</title>
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
				<h1>Edit Class</h1>
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
									<td><label for="class_name">Class: </label></td>
									<td>
										<input type="text" name="class_name" value="<?php echo $result['class_name'] ?>">
										<span class="error">*<br><?php echo "$class_namerr"; ?></span>
									</td>
								</tr>
								<tr>
									<td><label for="class_number">Class Number: </label></td>
									<td>
										<input type="number" name="class_number" value="<?php echo $result['class_number'] ?>">
										<span class="error">*<br><?php echo "$class_numberr"; ?></span>
									</td>
								</tr>
								<tr><td><button type="submit">Add Class</button></td></tr>
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