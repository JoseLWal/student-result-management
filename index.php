<?php
session_start();
if (isset($_SESSION['admin_id'])) {
	header("location:./admin/dashboard.php");
} else {
	if (strcasecmp($_SERVER['REQUEST_METHOD'], "POST") === 0) {
		$_SESSION['postdata'] = $_POST;
		header("Location: " .$_SERVER['PHP_SELF']. "?" .$_SERVER['QUERY_STRING']);
		exit;
	}
	// Define the login variables and set to null
	$login = $password = "";
	$error = "";

	if (isset($_SESSION['postdata'])) {
		$_POST = $_SESSION['postdata'];
		// Validate the login form
		include "include/val_function.php";
		if (empty($_POST['login']) || empty($_POST['password'])) {
			$error = "Please enter your username and password.";
		} else {
			$login = val_input($_POST['login']);
			$password = val_input($_POST['password']);
			// Authentication
			try {
				include "include/connect.php";
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$query = "SELECT * FROM admin WHERE username=:username";
				$stmt = $conn->prepare($query);
				$stmt->bindParam(':username', $login);
				$stmt->execute();
				$count = $stmt->rowCount();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($count == 1 && password_verify($password, $result['password'])) {
					$_SESSION = $result;
					header("location:./admin/dashboard.php");
				} else {
					$error = "Invalid username or password";
				}
			} catch(PDOException $e) {
				echo "Error: " .$e->getMessage();
			}
		}
		unset($_SESSION['postdata']);
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Admin Login</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<style type="text/css">
		body { font-family: arial; }
		#frontpage {
			padding: 20px 40px;
			background-color: #f2f2f2;
		}
		#frontpage h1 {
			text-align: center;
			margin-bottom: 100px;
		}
		#frontbody {
			display: grid;
			grid-template-columns: 50% 50%;
			margin-top: 100px;
			font-size: 14px;
		}
		#frontleft {
			background-color: #ffffff;
			margin: 0 50px;
			padding: 20px 10%;
			max-height: 120px;
		}
		#frontright {
			background-color: #ffffff;
			margin: 0 50px;
			padding: 20px 10%;
		}
		#frontright label { margin-right: 10px; }
		.h3title { text-align: center; }
		tr { height: 40px; }
		input[type=text], input[type=password] {
			width: 300px;
		}
		input[type=submit] {
			padding: 6px 20px;
			background-color: #5cb85c;
			font-size: 15px;
			border: 1px solid #4cae4c;
		}
		.error { color: red; }
	</style>
</head>
<body id="frontpage">
	<h1>Student Result Management System</h1>
	<div id="frontbody">
		<div id="frontleft">
			<h3 class="h3title">For Students</h3>
			<p>Student Result Management System</p>
			<p>
				<span>Search your result</span>
				<span><a href="#">Click here</a></span>
			</p>
		</div>
		<div id="frontright">
			<h3 class="h3title">Admin Login</h3>
			<p>Student Result Management System</p>
			<form method="post">
				<table>
					<tbody>
						<span class="error"><?php echo $error; ?></span>
						<tr>
							<td><label for="login"><b>Login: </b></label></td>
							<td><input type="text" name="login" value="<?php echo $login; ?>"></td>
						</tr>
						<tr>
							<td><label for="password"><b>Password: </b></label></td>
							<td><input type="password" name="password"><br><br></td>
						</tr>
						<tr><td><input type="submit"></td></tr>
						<tr><td><a href='logout.php'>Logout</a></td></tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>	
</body>
</html>