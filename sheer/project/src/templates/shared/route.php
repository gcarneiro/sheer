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
		<link rel="stylesheet" type="text/css" href="./resources/addons/qtip/jquery.qtip.min.css" />
		
		<!-- 
			Sheer
		 -->
		 <link rel="stylesheet" type="text/css" href="./resources/css/sheer/sheer.base.css" />
		 <link rel="stylesheet" type="text/css" href="./resources/css/sheer/sheer.interface.css" />
		
		<script src="scripts/teste-route.js"></script>
		
		{{page.scripts}}
		{{page.styles}}
		
	</head>
	
	<body class="sheer-main-bar">
	
		<ul>
			<li><a href="" sh-route>Home</a></li>
			<li><a href="noticias" sh-route>Notícias</a></li>
			<li><a href="quem-sou" sh-route>Quem Sou</a></li>
			<li><a href="historia" sh-route>História</a></li>
			<li><a href="contato" sh-route>Contato</a></li>
		</ul>
		
		<div id="brasil">
		</div>
		
		<div id="argentina">
		</div>
		
		<button onclick="fazer();">Fazer</button>
		
		<script>
			function fazer() {
				var html = '';
				html += '<label>';
					html += '<strong>Selecione um pais</strong>';
					html += '<select data-placeholder="Selecione">';
						html += '<option>Brasil</option>';
						html += '<option>Argentina</option>';
						html += '<option>França</option>';
						html += '<option>Portugal</option>';
						html += '<option>Espanha</option>';
					html += '</select>';
				html += '</label>';
				$('#brasil').html(html);
			}

		</script>
		
		
		
		<div id="holder">
			{{holder.central}}
		</div>
		
		<div id="loader" style="display: none;">
			<p>Carregando</p>
		</div>
		
	</body>
	
</html>