<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Secret Diary - Login</title>

</head>
<body>
<?php
	
	session_start();
	$error = "";
	if (array_key_exists("logout", $_GET)) {
        unset($_SESSION);
        setcookie("id", "", time() - 60*60);
        $_COOKIE["id"] = "";  
	} else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {
		header("Location: remember.php");
	}
	if (array_key_exists('submit', $_POST)) {
		$link = mysqli_connect("localhost", "jamshid_diary", "jty123", "jamshid_bidemy2");
		if (mysqli_connect_error()) {
			die("Database connection error.");
		}
		if (!$_POST['email']) {
			$error .= "An email address is required.<br>";
		}
		if (!$_POST['password']) {
			$error .= "A password is required.<br>";
		}
		if ($error != "") {
			$error = "There were error(s) in your form:" . $error;
		} else {

			if ($_POST['signup'] == '1') {

				$query = "SELECT id FROM `users2` WHERE email = '".mysqli_real_escape_string($link, $_POST['emial'])."' LIMIT 1";
				$result = mysqli_query($link, $query);
				print_r($result);
				if (mysqli_num_rows($result) > 0) {
					$error = "That email address is taken.";
				} else {
					$query = "INSERT INTO `users2` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."')";
					if (!mysqli_query($link, $query)) {
					 	$error = "Could not sign you up. Please try again later.";
					 } else {
					 	$query = "UPDATE `users2` SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1";
					 	mysqli_query($link, $query);
					 	$_SESSION['id'] = mysqli_insert_id($link);
					 	if ($_POST['remember'] == '1') {
					 		setcookie("id", mysqli_insert_id($link), time() + 60 * 60 * 24 *365);
					 	}
					 	header("Location: remember.php");
					 }
				}
			} else {
				$query = "SELECT * FROM `users2` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";
				$result = mysqli_query($link, $query);
				$row = mysqli_fetch_array($result);
				if (isset($row)) {
					$hashedPassword = md5(md5($row['id']) . $_POST['password']);
					if ($hashedPassword == $row['password']) {
						$_SESSION['id'] = $row['id'];
					 	if ($_POST['remember'] == '1') {
					 		setcookie("id", $row['id'], time() + 60 * 60 * 365);
					 	}
					 	header("Location: remember.php");
					} else {
						$error = "That email/password combination could not be found.";
					}
				} else {
					$error = "That email/password combination could not be found.";
				}
			}
		}

	}

?>

<div id="error"><?php echo $error; ?></div>
<form method="post">
	<input type="email" name="email" placeholder="Enter your email">
	<input type="password" name="password" placeholder="Enter your password">
	<input type="checkbox" name="remember" value="1">
	<input type="hidden" name="signup" value="1">
	<button type="submit" name="submit">Sign Up</button>
</form>
<form method="post">
	<input type="email" name="email" placeholder="Enter your email">
	<input type="password" name="password" placeholder="Enter your password">
	<input type="checkbox" name="remember" value="1">
	<input type="hidden" name="signup" value="0">
	<button type="submit" name="submit">Log In</button>
</form>
</body>
</html>
