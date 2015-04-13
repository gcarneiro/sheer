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
	
		<h1>Ocorreu um erro inesperado</h1>
		<?php 
			if( isset($_GET['m']) ) {
				echo '<p>'.$_GET['m'].'</p>';
			}
			else {
				echo '<p>Não há descrição do erro.</p>';
			}
		
		?>
	</body>
</html>