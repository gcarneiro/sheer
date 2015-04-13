<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>{{page.title}}</title>
		{{page.baseUrl}}
		
		<!-- 
			Inicializando o requirejs
			Dentro do script setup do sheer iremos inicializar todas as dependecias do Sheer.
		
		 -->
		<script src="./resources/addons/sheer/sheer.adapters.js"></script>
		<script src="./resources/addons/sheer/sheer.js"></script>
		<script src="./resources/addons/sheer/sheer.adm.js"></script>
		
		<!-- 
			Estilo
				ADDONS
		 -->
		<link rel="stylesheet" type="text/css" href="./resources/addons/jquery.colorbox/colorbox.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/jquery.select2/select2.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/bootstrap/less/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/datepicker/datepicker.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/fullcalendar/fullcalendar.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/qtip/jquery.qtip.min.css" />
		
		<!-- 
			Sheer
		 -->
		 <link rel="stylesheet" type="text/css" href="./resources/css/sheer/sheer.base.css" />
		 <link rel="stylesheet" type="text/css" href="./resources/css/sheer/sheer.interface.css" />
		
		{{page.scripts}}
		{{page.styles}}
		
	</head>
	
	<body>
	
		<section class="sh-box sh-w-400 sh-margin-x-auto sh-box-verde">
			
			<header>
				<h1 class="data-center">Efetue o login no sistema</h1>
			</header>
		
			<div class="sh-box-content">
				<form class="sh-form" action="action.php?ah=user/efetuarLogin" method="post" novalidate sh-form sh-form-rh="[sheer/adm][rh.login]" >
					<fieldset>
					
						<div class="sh-w-1 data-center">
							<div class="sh-form-field sh-w-1 data-left">
								<label for="login">Login</label>
								<input type="text" id="login" name="login" placeholder="Login" required />
							</div>
						</div>
						
						<div class="sh-w-1 data-center">
							<div class="sh-form-field sh-w-1 data-left">
								<label for="password">Senha</label>
								<input type="password" id="password" name="password" placeholder="Senha" required />
							</div>
						</div>
						
						
						<div class="sh-btn-holder">
							<button type="submit" class="sh-btn-verde">Logar</button>
						</div>
						
					</fieldset>
				</form>
			</div>
		
		</section>
		
	</body>
</html>