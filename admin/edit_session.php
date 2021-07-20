<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
	header("location:.././");
} else {
	$session_id = $_GET['session_id'];
	include "../include/connect.php";
	if (strcasecmp($_SERVER['REQUEST_METHOD'], "POST") === 0) {
		$_SESSION['postdata'] = $_POST;
		header("Location: " .$_SERVER['PHP_SELF']. "?" .$_SERVER['QUERY_STRING']);
		exit;
	}

	// Define the add class variables and set to null
	$session_name = $session_code = $date_begin = $date_end = "";
	$session_namerr = $session_coderr = "";
	$success_notice = "";

	if (isset($_SESSION['postdata'])) {
		$_POST = $_SESSION['postdata'];
		// Validate the add class form
		include "../include/val_function.php";
		if (empty($_POST['session_name'])) {
			$session_namerr = "Session name is required.";
		} else {
			$session_name = val_input($_POST['session_name']);
		}
		if (empty($_POST['session_code'])) {
			$session_coderr = "Please enter a code for the session.";
		}  else {
			$session_code = val_input($_POST['session_code']);
		}
		$date_begin = val_input($_POST['date_begin']);
		$date_end = val_input($_POST['date_end']);
		try {
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$query = "SELECT * FROM sessions WHERE session_name=:session_name OR session_code=:session_code";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(':session_name', $session_name);
			$stmt->bindParam(':session_code', $session_code);
			$stmt->execute();
			$count = $stmt->rowCount();
			$result = $stmt->fetchAll();
			foreach ($result as $result) {
				if ($count == 1 && $session_name == $result['session_name']) {
					$session_namerr = "Session already exist.";
				}
				if ($count == 1 && $session_code == $result['session_code']) {
					$session_coderr = "Session code already exist";
				}
			}
			$data_ok = empty($session_namerr || $session_coderr);
			if ($data_ok) { // Submit the data
				$query = "INSERT INTO sessions (session_name, session_code, date_begin, date_end)
				VALUES (:session_name, :session_code, :date_begin, :date_end)";
				$stmt = $conn->prepare($query);
				$stmt->bindParam(':session_name', $session_name);
				$stmt->bindParam(':session_code', $session_code);
				$stmt->bindParam(':date_begin', $date_begin);
				$stmt->bindParam(':date_end', $date_end);
				$stmt->execute();
				$success_notice = "Session updated successfully.";
			}
		} catch(PDOException $e) {
			echo "Error: " .$e->getMessage();	
		} 
	}
	unset($_SESSION['postdata']);
	try {
		if ($section_id = $_GET['session_id']) {
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$query = "SELECT * FROM sessions WHERE session_id=$session_id";
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
	<title>Create New Session</title>
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
				<h1>Add Sessions</h1>
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
									<td><label for="session_name">Session: </label></td>
									<td>
										<input type="text" name="session_name" value="<?php echo $result['session_name'] ?>">
										<span class="error">*<br><?php echo "$session_namerr"; ?></span>
									</td>
								</tr>
								<tr>
									<td><label for="session_code">Session Code: </label></td>
									<td>
										<input type="text" name="session_code" value="<?php echo $result['session_code'] ?>">
										<span class="error">*<br><?php echo "$session_coderr"; ?></span>
									</td>
								</tr>
								<tr>
									<td><label for="date_begin">Start Date: </label></td>
									<td>
										<input type="date" name="date_begin" value="<?php echo $result['date_begin'] ?>">
									</td>
								</tr>
								<tr>
									<td><label for="date_end">End Date: </label></td>
									<td>
										<input type="date" name="date_end" value="<?php echo $result['date_end'] ?>">
									</td>
								</tr>
								<tr><td><button type="submit">Edit Session</button></td></tr>
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