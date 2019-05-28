<?php 
	
	session_start(); 
		
	if(array_key_exists("id", $_COOKIE)){
		$_SESSION['id'] = $_COOKIE['id'];
	} 
	
	if (array_key_exists("id", $_SESSION)) {
		$login = "<span>Logged in!!! Session: ".$_SESSION['id']."</span>";
		$logout = "<span><a href='index.php?logout=1'>Log out</a></span>";
		include 'db_connnection.php';
	} else {
		header("Location: index.php");
	}
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
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

	<title>My Secret Diary</title>
	</head>
	<body>
		<div id="topbar" class="container-fluid">
				<div class="float-left d-inline" id="login">This Version of Secret Diary only works correctly in Internet Explorer for now.... </div>
				<div class="float-right d-inline" id="logout"><?php echo $logout; ?></div>
		</div>
		<div class="container2">
		<h1>My Secret Diary</h1>
		
			<form method="post">
				<div class="form-group">
					<div id="form-header">
						<div id="date-div" class="form-inline"> 
							<input type="button" class="today btn btn-info" name="today" value="Today">
							<input type="text" id="date" placeholder="YYYY-MM-DD" class="form-control date-field" name="date">
							<input type="button" class="go btn btn-info" name="go" value="Go">
						</div>
						<div id="old-new-div">
							<button type="button" class="next old-new btn btn-info" name="next"><i class="fas fa-angle-double-left"></i></button>
							<button type="button" class="previous old-new btn btn-info" name="previous"><i class="fas fa-angle-double-right"></i></button>
						</div>
					</div>
					<label for="textarea" class="sr-only">Space to type your diary</label>
					<textarea name="input" class="form-control" id="textarea" rows="12"></textarea>
					<div id="message"></div>
				</div>
				
			</form> 
		
		<!-- Optional JavaScript -->
		<script type="text/javascript">
			
			$(document).ready(function() {
					$("#date").val(currentDate());
					loadDiary('bydate');
					
					function currentDate() {
						var now = new Date();
						var y = now.getFullYear();
						var m = now.getMonth() + 1;
						var d = now.getDate();
						var mm = m < 10 ? '0' + m : m;
						var dd = d < 10 ? '0' + d : d;
						var today = y + "-" + mm + "-" + dd
						return today;
					}
					
					function loadDiary(action) { 
						var id = <?php echo $_SESSION['id'];?>;
						var date = $("#date").val();
						$('#textarea').html('');
						$.ajax({
							url:'loadDiary.php',
							method:'POST',
							data:{
								action: action,
								id: id,
								date: date
							},
							dataType: "JSON",

						}).done(function(data){
								$('#message').html(data.message);
								$('#date').val(data.date);
								$('#textarea').html(data.diary);
								alert("onload finished" + data.diary); 
								if (data.isNext) {
									$(".next").removeClass('disabled');
									$(".next").attr("disabled", false);
									//alert ("removed class disabled to next");
								} else {
									$(".next").addClass('disabled');
									$(".next").attr("disabled", true);
									//alert ("added class disabled to next");
								}
								if (data.isPrevious) {
									$(".previous").removeClass('disabled');
									$(".previous").attr("disabled", false);
									//alert ("removed class disabled to previous");
								} else {
									$(".previous").addClass('disabled');
									$(".previous").attr("disabled", true);
									//alert ("added class disabled to previous");
								}
						});
					}

				$( "#textarea" ).bind('input propertychange', function() {
					var diary = $("#textarea").val();
					var id="<?php echo $_SESSION['id'];?>";   
					var date =$("#date").val();
					
					$.ajax({
						url:'addDiary.php',
						method:'POST',
						data:{
							diary: diary,
							id: id,
							date: date
						},
						dataType: "JSON",

					}).done(function(data){
						$('#message').html(data.message);
					});
				});
				
				$(".today").click(function(){
					$("#date").val(currentDate());
					loadDiary('bydate');
				});

				
				$(".go").click(function(){
					var action = "bydate";
					loadDiary(action);
				});
				
				
				$(".previous").click(function(){
					var action = "prev";
					loadDiary(action);
				});
				
				$(".next").click(function(){
					var action = "next";
					loadDiary(action);
				});

			});
		</script>
	</body>
</html>