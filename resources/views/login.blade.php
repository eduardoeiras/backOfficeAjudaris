<?php
	$utilizador = session()->get("utilizador");
	if(isset($utilizador)) {
		if($utilizador->tipoUtilizador == 0) {
			header("location:admin/dashboardAdmin");
			exit();
		}
		else {
			header("location:colaborador/dashboardColaborador");
			exit();
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Página Inicial</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="{{ asset('images/icons/favicon.ico') }}"/>
	<link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('vendor/animate/animate.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('vendor/css-hamburgers/hamburgers.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('vendor/select2/select2.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/util.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="{{ asset('images/logo_ajudaris.png') }}" alt="IMG">
				</div>

				<form class="login100-form validate-form" action="login" method="POST">
                    @csrf
					<span class="login100-form-title">
						Realizar Login
					</span>

					<div class="wrap-input100 validate-input">
						<input class="input100" type="text" name="nome" placeholder="Nome de utilizador">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "É necessário introduzir a password!">
						<input class="input100" type="password" name="password" placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
                    </div>
                    <?php 
                        if(isset($msg)) {
                            $html = "<p style='color: red'>";
                            $html = $html.$msg.'</p>';
                            echo $html;
                        }
                    ?>
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Login
						</button>
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							Esqueceu-se do seu
						</span>
						<a class="txt2" href="#">
							Nome de utilizador / Password?
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script src="{{ asset('vendor/jquery/jquery-3.2.1.min.js') }}"></script>
	<script src="{{ asset('vendor/bootstrap/js/popper.js') }}"></script>
	<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
	<script src="{{ asset('vendor/tilt/tilt.jquery.min.js') }}"></script>
	<script>
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
	<script src="{{ asset('js/main.js') }}"></script>

</body>
</html>