<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="assets/css/blackvatican.css">
		<link rel="icon" href="assets/img/1.png" type="image/png">
		<audio id="bvsfx" src="assets/sfx/click.mp3"></audio>
		<title>PLEASE ENTER PASSWORD</title>
	</head>
	<body>
		<center>
			<div class="d-flex flex-column min-vh-100 justify-content-center align-items-center">
				<div class="col-md-3 justify-content-center">
					<div class="card bvborder">
						<div class="card-header">
							PLEASE ENTER PASSWORD
						</div>
						<div class="card-header">
							<img src="Logo.png" class="bvlogo">
						</div>
						<div class="card-body">
							<form onsubmit="return false;">
							<input type="password" id="password" name="password" class="form-control" rows="1" placeholder="Password">
							<button type="submit" class="btn btn-outline-light btn-block" id="login">LOGIN</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</center>
		<script>
		document.getElementById('login').addEventListener('click', function() {
			const password = document.getElementById('password').value;
			if (password === 'swabe') {
				localStorage.setItem('loggedIn', 'true');
				window.location.href = 'index.html';
			} else {
				alert('Incorrect password');
			}
		});
		</script>
		<script src="assets/js/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
		<script src="assets/js/jquery.redirect.js" type="text/javascript"></script>
		<script src="assets/js/login.js" type="text/javascript"></script>
		<script src="assets/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	</body>
</html>
