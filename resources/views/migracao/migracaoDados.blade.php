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
			<div style="background-color: white;border-radius: 15px;padding: 2%">
				<div>
					<h2>Migração de Dados</h2>
					<br>
					<span>Para realizar a migração de dados, selecione o ficheiro <b>Excel</b> a utilizar. Seguem-se 
						algumas indicações referentes ao processo:</span>
					<ol>
						<br>
						<li style="font-size: 14px;margin-left: 15px">Não desligue o computador durante o processo.</li>
						<br>
						<li style="font-size: 14px;margin-left: 15px">As moradas devem seguir o formato, separado por vírgula:<i> Rua, Nº Porta, Código Postal,
						Localidade, Distrito</i>.<br> As que não o seguirem não poderão ser importadas.</li>
						<br>
						<li style="font-size: 14px;margin-left: 15px">Registos no Excel de Colaboradores sem indicação de <b>nome</b>,<br>
						ou com nomes <b>maiores que 150 caracteres</b> não poderão ser inseridos.</li>
						<br>
						<li style="font-size: 14px;margin-left: 15px">Registos de colaboradores repetidos ou endereços de email<br>
						repetidos, serão adicionados nos campos de Observações para esses casos.</li>
						<br>
						<li style="font-size: 14px;margin-left: 15px">A execução da migração, não dispensa a verificação ou inserção<br>
						manual de certos registos, que não possam ser importados.</li>
						<br>
					</ol>  
					<form id="formMigracao" method="POST" enctype="multipart/form-data" action="iniciarMigracao"> 
						@csrf
						<label>Selecione um ficheiro:</label><br>
						<input type="file" id="excel" name="excel" required>
						<br><br>
						<button class="btn btn-success">Iniciar Migração</button>
					</form>
				</div>
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
			scale: 1.0
		})
	</script>
	<script src="{{ asset('js/main.js') }}"></script>

</body>
</html>