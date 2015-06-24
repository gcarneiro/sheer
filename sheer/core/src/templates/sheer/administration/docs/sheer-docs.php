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
		<script src="./resources/addons/sheer/sheer.validation.js"></script>
		<script src="./resources/addons/sheer/sheer.adm.js"></script>
		
		<!-- 
			Estilo
				ADDONS
		 -->
		<link rel="stylesheet" type="text/css" href="./resources/addons/jquery.colorbox/colorbox.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/jquery.select2/select2.min.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/bootstrap/less/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/datepicker/datepicker.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/qtip/jquery.qtip.min.css" />
		
		{{page.scripts}}
		{{page.styles}}
		
	</head>
	
	<body>
	
		<h1>Sheer - Documentação</h1>
		
		<ul>
			<li><a href="docs/server-side">Server Side</a></li>
			<li><a href="docs/client-side">Client Side</a></li>
		</ul>
		
	</body>
	
</html>