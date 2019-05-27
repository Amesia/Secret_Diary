<?php 
	session_start();
	//load diary from database function
	if (array_key_exists("date", $_POST)) {
		include 'db_connnection.php';
		
		if (isset($_POST['action'])) {
			if ($_POST['action'] == 'bydate') {
				
				$query = "SELECT * FROM `Entries` WHERE `user_id` = '" . $_SESSION['id'] . "' AND `date` = '" . mysqli_real_escape_string($link, $_POST['date']) . "'"; 
				$result = mysqli_query($link, $query);
				
				
				if (mysqli_num_rows ($result) == 1) {
					
					while ($row = mysqli_fetch_array($result)){
						$data["diary"] = $row['diary'];
						$data["date"] = $row['date'];
						$data["message"] = '<div class="alert alert-success" role="alert">Successfully loaded data from database '.$_POST['action'].'</div>';
					}
					
				} else if (mysqli_num_rows ($result) == 0){
					$data["diary"] = null;
					$data["date"] = mysqli_real_escape_string($link, $_POST['date']);
					if ($data["date"] == date('Y-m-d')) {
						$data["message"] = '<div class="alert alert-light" role="alert">Ready to add a new diary? '. $_POST['action'].'</div>';
					} else {
						$data["message"] = '<div class="alert alert-secondary" role="alert">No diaries found on that date '. $_POST['action'].'</div>';
					}
				} else {
					$data["message"] = '<div class="alert alert-warning" role="alert">Error in database: To many diaries found</div>';
				}

			} else if ($_POST['action'] == 'prev') {
				$query = "SELECT * FROM `Entries` WHERE `user_id` = '" . $_SESSION['id'] . "' AND `date` < '" . mysqli_real_escape_string($link, $_POST['date']) . "' ORDER BY date DESC LIMIT 1"; 
				$result = mysqli_query($link, $query);
				
				if (mysqli_num_rows ($result) == 1) {
					
					while ($row = mysqli_fetch_array($result)){
						$data["diary"] = $row['diary'];
						$data["date"] = $row['date'];
						$data["message"] = '<div class="alert alert-success" role="alert">Successfully loaded data from database '.$_POST['action'].'</div>';
					}
					
				} else if (mysqli_num_rows ($result) == 0){
					$data["diary"] = null;
					$data["date"] = mysqli_real_escape_string($link, $_POST['date']);
					$data["message"] = '<div class="alert alert-secondary" role="alert">No older diaries found'. $_POST['action']. '</div>';
				}
				
			} else if ($_POST['action'] == 'next') {
				$query = "SELECT * FROM `Entries` WHERE `user_id` = '" . $_SESSION['id'] . "' AND `date` > '" . mysqli_real_escape_string($link, $_POST['date']) . "' ORDER BY date ASC LIMIT 1"; 
				$result = mysqli_query($link, $query);
				
				if (mysqli_num_rows ($result) == 1) {
					
					while ($row = mysqli_fetch_array($result)){
						$data["diary"] = $row['diary'];
						$data["date"] = $row['date'];
						$data["message"] = '<div class="alert alert-success" role="alert">Successfully loaded data from database '.$_POST['action'].'</div>';
					}
					
				} else if (mysqli_num_rows ($result) == 0){
					$data["diary"] = null;
					$data["date"] = date('Y-m-d');
					$data["message"] = '<div class="alert alert-secondary" role="alert">No newer diaries found'. $_POST['action'].'</div>';
				}
			
			} else {
				$data["message"] = '<div class="alert alert-warning" role="alert">Error: Unexpected action</div>';
				
			}
		//isNext?
		$query = "SELECT * FROM `Entries` WHERE `user_id` = '" . $_SESSION['id'] . "' AND `date` > '" . $data["date"] . "' ORDER BY date ASC LIMIT 1"; 
		$result = mysqli_query($link, $query);
		if (mysqli_num_rows ($result) == 1 OR $data["date"] < date('Y-m-d')) {
			$data["isNext"] = true;
		} else {
			$data["isNext"] = false;
		}
		
		// isPrevious?
		$query = "SELECT * FROM `Entries` WHERE `user_id` = '" . $_SESSION['id'] . "' AND `date` < '" . $data["date"] . "' ORDER BY date DESC LIMIT 1"; 
		$result = mysqli_query($link, $query);
		if (mysqli_num_rows ($result) == 1) {
			$data["isPrevious"] = true;
		} else {
			$data["isPrevious"] = false;
		}
		
		} else {
				$data["message"] = '<div class="alert alert-warning" role="alert">Error: Action is not set</div>';
		}
		
		echo json_encode($data);
	}
?>