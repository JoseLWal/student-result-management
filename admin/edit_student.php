<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
	include "../include/connect.php";
	if (strcasecmp($_SERVER['REQUEST_METHOD'], "POST") === 0) {
		$_SESSION['postdata'] = $_POST;
		header("Location: " .$_SERVER['PHP_SELF']. "?" .$_SERVER['QUERY_STRING']);
		exit;
	}

	// Define the add student variables and set to null
	$student_name = $roll_id = $email = $gender = $dob = $class = "";
	$student_namerr = $roll_iderr = $emailerr = $genderr = $doberr = $classerr = "";
	$status = "active"; // Set the status for newly added student to active.
	$success_notice = "";

	if (isset($_SESSION['postdata'])) {
		$_POST = $_SESSION['postdata'];
		// Validate the add student form
		include "../include/val_function.php";
		if (empty($_POST['student_name'])) {
			$student_namerr = "Student name is required.";
		} else {
			$student_name = val_input($_POST['student_name']);
			if (!preg_match("/^[a-zA-Z-' ].*$/", $student_name)) {
				$student_namerr = "Student name can only contain letters, white spaces and period mark.";
			}
		}
		if (empty($_POST['roll_id'])) {
			$roll_iderr = "Please enter a Roll Identification code for the student.";
		}  else {
			$roll_id = val_input($_POST['roll_id']);
			if (!preg_match("/^[a-zA-Z-0-9-' ]*$/", $roll_id)) {
				$roll_iderr = "Roll Identification code can only contain letters, numbers and hyphen.";
			}
		}
		if (!empty($email)) {
			$email = val_input($_POST['email']);
			if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
		   		$emailerr = "Invalid email format";
			}
		}
		if (empty($_POST['gender'])) {
			$genderr = "Please select a gender.";
		} else {
			$gender = val_input($_POST['gender']);
		}
		if (empty($_POST['dob'])) {
			$doberr = "Student date of birth is required";
		} else {
			$dob = val_input($_POST['dob']);
		}
		if (empty($_POST['class'])) {
			$classerr = "Please select a class for the student.";
		} else {
			$class = val_input($_POST['class']);
		}
		try {
			include "../include/connect.php";
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$query = "SELECT * FROM students WHERE student_name=:student_name OR roll_id=:roll_id OR student_email=:email";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(':student_name', $student_name);
			$stmt->bindParam(':roll_id', $roll_id);
			$stmt->bindParam(':email', $email);
			$stmt->execute();
			$count = $stmt->rowCount();
			$result = $stmt->fetchAll();
			foreach ($result as $result) {
				if ($count == 1 && $student_name == $result['student_name']) {
					$student_namerr = "Student already exist.";
				}
				if ($count == 1 && $roll_id == $result['roll_id']) {
					$roll_iderr = "Roll Identification code already exist";
				}
				if (!empty($email) && $count == 1 && $email == $result['student_email']) {
					$emailerr = "Email already exist";
				}
			}
			$data_ok = empty($student_namerr || $roll_iderr || $emailerr || $genderr || $doberr || $classerr);
			if ($data_ok) { // Submit the data
				$query = "INSERT INTO students (student_name, roll_id, student_email, gender, dob, class_id, status)
				VALUES (:student_name, :roll_id, :email, :gender, :dob, :class, :status)";
				$stmt = $conn->prepare($query);
				$stmt->bindParam(':student_name', $student_name);
				$stmt->bindParam(':roll_id', $roll_id);
				$stmt->bindParam(':email', $email);
				$stmt->bindParam(':gender', $gender);
				$stmt->bindParam(':dob', $dob);
				$stmt->bindParam(':class', $class);
				$stmt->bindParam(':status', $status);
				$stmt->execute();
				$success_notice = "New student added successfully";
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
	<title>Add New Student</title>
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
				<h1>Add Students</h1>
			</div>
			<div id="body">
				<div id="input">
					<form method="post">
						<?php echo $success_notice; ?>
						<table>
							<tbody>
								<tr>
									<td><label for="student_name">Student Name: </label></td>
									<td>
										<input type="text" name="student_name">
										<span class="error">* <br><?php echo $student_namerr; ?></span>
									</td>
								</tr>
								<tr>
									<td><label for="roll_id">Roll Id: </label></td>
									<td>
										<input type="text" name="roll_id">
										<span class="error">*<br><?php echo $roll_iderr; ?></span>
									</td>
								</tr>
								<tr>
									<td><label for="email">Email: </label></td>
									<td>
										<input type="email" name="email">
										<span class="error"><br><?php echo $emailerr; ?></span>
									</td>
								</tr>
								<tr>
									<td><label for="gender">Gender: </label></td>
									<td>
										<input type="radio" name="gender" value="male"> Male
										<input type="radio" name="gender" value="female"> Female
										<span class="error">*<br><?php echo $genderr; ?></span>
									</td>
								</tr>
								<tr>
									<td><label for="dob">Date of Birth: </label></td>
									<td>
										<input type="date" name="dob">
										<span class="error">*<br><?php echo $doberr; ?></span>
									</td>
								</tr>
								<tr>
									<td><label for="class">Class: </label></td>
									<td>
										<select name="class">
											<option value="">Select class</option>
											<?php
											try {
												$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
												$query = "SELECT * FROM classes";
												$stmt = $conn->prepare($query);
												$stmt->execute();
												$count = $stmt->rowCount();
												$result = $stmt->fetchAll();
												if ($count > 0) {
													foreach ($result as $result) {
											?>
											<option value="<?php echo $result['class_id']; ?>"><?php echo $result['class_name'] ?></option>
											<?php
													}
												}
											} catch(PDOException $e) {
												echo "Error: " .$e->getMessage();
											}
											?>
										</select>
										<span class="error">*<br><?php echo $classerr; ?></span>
									</td>
								</tr>
								<tr><td><button type="submit">Add Student</button></td></tr>
							</tbody>
						</table>
					</form>	
				</div>
			</div>
		</div>
	</div>
</body>