<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
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
			include "../include/connect.php";
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$query = "SELECT * FROM classes WHERE class_name=:class_name OR class_number=:class_number";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(':class_name', $class_name);
			$stmt->bindParam(':class_number', $class_number);
			$stmt->execute();
			$count = $stmt->rowCount();
			$result = $stmt->fetchAll();
			foreach ($result as $result) {
				if ($count == 1 && $class_name == $result['class_name']) {
					$class_namerr = "Class name already exist.";
				}
				if ($count == 1 && $class_number == $result['class_number']) {
					$class_numberr = "Class number already exist";
				}
			}
			$data_ok = empty($class_namerr || $class_numberr);
			if ($data_ok) { // Submit the data
				$query = "INSERT INTO classes (class_name, class_number)
				VALUES (:class_name, :class_number)";
				$stmt = $conn->prepare($query);
				$stmt->bindParam(':class_name', $class_name);
				$stmt->bindParam(':class_number', $class_number);
				$stmt->execute();
				$success_notice = "New class created successfully";
			}
		} catch(PDOException $e) {
			echo "Error: " .$e->getMessage();	
		} 
	}
	unset($_SESSION['postdata']);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Create New Class</title>
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
				<h1>Add Class</h1>
			</div>
			<div id="body">
				<div id="input">
					<form method="post">
						<?php echo $success_notice; ?>
						<table>
							<tbody>
								<tr>
									<td><label for="class_name">Class: </label></td>
									<td>
										<input type="text" name="class_name">
										<span class="error">*<br><?php echo "$class_namerr"; ?></span>
									</td>
								</tr>
								<tr>
									<td><label for="class_number">Class Number: </label></td>
									<td>
										<input type="number" name="class_number">
										<span class="error">*<br><?php echo "$class_numberr"; ?></span>
									</td>
								</tr>
								<tr><td><button type="submit">Add Class</button></td></tr>
							</tbody>
						</table>
					</form>	
				</div>
			</div>
		</div>
	</div>
</body>