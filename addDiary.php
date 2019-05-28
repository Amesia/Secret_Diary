<?php 
	session_start();
	//add or edit database function
	if (array_key_exists("diary", $_POST)) {
		
		include 'db_connnection.php';
		
		//Check if date has diary
		$query = "SELECT `diary` FROM `Entries` WHERE `user_id` = '" . $_SESSION['id'] . "' AND `date` = '" . mysqli_real_escape_string($link, $_POST['date']) . "'"; 
		$result = mysqli_query($link, $query);
		
		if (mysqli_num_rows ($result) == 0) {
			//add to database
			$query = "INSERT INTO `Entries` (`user_id`, `date`, `diary`) VALUES ('" . $_SESSION['id'] . "','" . $_POST['date'] . "','".mysqli_real_escape_string($link, $_POST['diary']) . "')"; 
			if (mysqli_query($link, $query)) {
				$data["message"] = '<div class="alert alert-success" role="alert">Diary added!</div>';
			} else {
				$data["message"] = '<div class="alert alert-warning" role="alert">Something went wrong, please try again later</div>';
			}
		} else if (mysqli_num_rows ($result) == 1){
			//edit database
			$query = "UPDATE `Entries` SET `diary` = '".$_POST['diary']."' WHERE `user_id` = '" . $_SESSION['id'] . "' AND `date` = '" . mysqli_real_escape_string($link, $_POST['date']) . "'";
			
			if (mysqli_query($link, $query)) {
				$data["message"] = '<div class="alert alert-success" role="alert">Diary updated!</div>';
			} else { 
				$data["message"] = '<div class="alert alert-warning" role="alert">Something went wrong, please try again later</div>';
			} 
		} else {
			$data["message"] = '<div class="alert alert-warning" role="alert">Error not sure what to do</div>';
		}

	} else {
		$data["message"] = '<div class="alert alert-warning" role="alert">data failure</div>';
	}
	
	echo json_encode($data);
?>