<?php
	session_start();
	$error="";
	if (array_key_exists("logout", $_GET)) {
		unset($_SESSION['id']);
		setcookie("id", "", time()-3600);
		$_COOKIE["id"] = "";
	} else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {

		header("Location: secretDiary.php");
	}
	if (array_key_exists("signup", $_POST) OR array_key_exists("signin", $_POST)) {
		include 'db_connnection.php';
		if (!$_POST["email"]){
			$error .= "Email adress field is empty<br>";
		}
		if (!$_POST["password"]) {
			$error .= "Password field is empty<br>";
		}
		if ($error != ""){
			$error = "<div class='alert alert-danger' role='alert'><p><strong>There are error(s) in your form:</strong><br>".$error."</p></div>";
		} else if (array_key_exists("signup", $_POST)){ //sign up new user
			$query = mysqli_query($link, "SELECT email FROM users");
			$known = false;
			while ($reslt = mysqli_fetch_array($query)){
				if($reslt["email"] == mysqli_real_escape_string($link, $_POST['email'])){
					$known=true;
				}
			}
			if ($known) {
				$error = "<div class='alert alert-danger' role='alert'><p><strong>There are error(s) in your form:</strong><br> The emailadress ".$_POST["email"]." is already in use</p></div>";
			} else {
				// add: add user to database and login!!
				$query = "INSERT INTO `users` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."',
				'".mysqli_real_escape_string($link, $_POST['password'])."')";
				if (!mysqli_query($link, $query)){
					$error = "<div class='alert alert-danger' role='alert'><p>Could not sign you up, please try again later</p></div>"; 
				} else {
					$query = "UPDATE `users` SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1";
					mysqli_query($link, $query);
					$_SESSION['id'] = mysqli_insert_id($link);
					if (isset ($_POST['stay_logged_in'])) {
						setcookie("id", mysqli_insert_id($link), time() + 60*60*24*365);
					}
					header("Location: secretDiary.php");
				}
			}
		} else if (array_key_exists("signin", $_POST)) { 
		//sign in excisting user
			$query = mysqli_query($link, "SELECT * FROM users");
			$isUser = "";
			while ($reslt = mysqli_fetch_array($query)) {
				if ($reslt['email'] == mysqli_real_escape_string($link, $_POST['email'])) {
					$isUser = $reslt;
				}
			}
			if ($isUser != "") {
				if ($isUser['password'] == md5(md5($isUser['id']).mysqli_real_escape_string($link, $_POST['password']))) {
					$_SESSION['id'] = $isUser['id'];
						if (isset($_POST['stay_logged_in'])) {
							setcookie("id", $isUser['id'], time() + 60*60*24*365);
						}
					header("Location: secretDiary.php");
				} else {
					$error = "<div class='alert alert-danger' role='alert'><p>That password is not correct<p></div>";
				}
			} else {
					$error = "<div class='alert alert-danger' role='alert'><p>Your not in our database<p></div>";
			}
		} else {
			$error = "<div class='alert alert-danger' role='alert'><p>Something went wrong, please try again later<p></div>";
		}
	};
	
?>
<!DOCTYPE html> 
<html>
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<link rel="stylesheet" href="main.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
		<title>My Secret Diary | Login</title>
	</head>
	<body>
		<div class="container">
		<h1>My Secret Diary</h1>
			<form id="signUpForm" method="post"> <!-- Sign up form-->
				<div class="form-group">
					<label for="email1" class="sr-only">Email address</label>
					<input type="email" name="email" placeholder="Your email" id="email1" class="form-control email" aria-describedby="emailHelp">
					<small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
				</div>
				<div class="form-group">
					<label for="password2" class="sr-only">Password</label>
					<input type="password" name="password" class="form-control password" id="password1" placeholder="Password">
				</div>
				<div class="form-group form-check">
					<label class="form-check-label" for="Check1">Keep me logged in</label>
					<input type="checkbox" name="stay_logged_in" value="1" class="form-check-input check" id="Check1">
				</div>
				<input type="submit" name="signup" value="Sign Up" class="btn btn-primary">
				<button type="button" class="btn btn-link switch">Log in</button>
			</form>
			<form id="signInForm" method="post" class="hidden"> <!-- Sign in form-->
				<div class="form-group">
					<label for="email2" class="sr-only">Email address</label>
					<input type="email" name="email" placeholder="Your email" id="email2" class="form-control email" aria-describedby="emailHelp">
					<small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
				</div>
				<div class="form-group">
					<label for="password2" class="sr-only">Password</label>
					<input type="password" name="password" class="form-control password" id="password2" placeholder="Password">
				</div>
				<div class="form-group form-check">
					<label class="form-check-label" for="Check2">Keep me logged in</label>
					<input type="checkbox" name="stay_logged_in" value="1" class="form-check-input check" id="Check2">
				</div>
				<input type="submit" name="signin" value="Log In" class="btn btn-primary">
				<button type="button" class="btn btn-link switch">Sign Up</button>
			</form>
			<div id="error"><?php echo $error; ?></div>
			
		</div>
		
		<script type="text/javascript">
			// Switch function
			$(".switch").click(function(){
				$("#signInForm").toggle();
				$("#signUpForm").toggle();
			});
		</script>
		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	</body>
	
</html>	