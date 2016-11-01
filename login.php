<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Secret Diary - Login</title>

</head>
<body>
<?php
	
	$error = "";
	if (array_key_exists("submit", $_POST)) {

		$link = mysqli_connect("localhost", "jamshid_diary", "jty123", "jamshid_bidemy2");
		if (mysqli_connect_errno()) {
			die("Database connection error");
		}

		if (!$_POST['email']) {
			$error .= "Email is required.<br>";
		}
		if (!$_POST['password']) {
			$error .= "Password is required.<br>";
		}
		if ($error != "") {
			$error = "There were errors in your form:<br>" . $error;
		} else {
			$query = "SELECT id FROM `users2` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
			$result = mysqli_query($link, $query);
			if (mysqli_num_rows($result) > 0) {
				$error = "That emial address is taken.";
			} else {
				$query = "INSERT INTO `users2` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."')";
				if (!mysqli_query($link, $query)) {
				 	$error = "Could not sign you up. Please try again later.";
				 } else {
				 	$query = "UPDATE `users2` SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1";
				 	mysqli_query($link, $query);
				 	echo "Sign up successful.= ".md5(md5(mysqli_insert_id($link)).$_POST['password']);
				 }
			}
		}
	}


?>

<div id="error"><?php echo $error; ?></div>
	<form action="" method="post">
		<input type="email" name="email" id="email" placeholder="Enter your email">
		<input type="password" name="password" id="password" placeholder="Enter your password">
		<input type="checkbox" name="remember" id="remember">
		<button type="submit" name="submit">Submit</button>
	</form>
</body>
</html>