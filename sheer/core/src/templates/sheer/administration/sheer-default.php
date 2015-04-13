<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>{{page.title}}</title>
		{{page.baseUrl}}
		
		{{template.includes/inclusaoScripts.html}}
		
		{{page.scripts}}
		{{page.styles}}
		
	</head>
	
	<body class="sheer-main-bar">
	
		{{template.includes/barraPrincipal.html}}
		
		<div class="sh-margin-x-auto sh-w-min-800">
		
			{{holder.central}}
			
		</div>
		
	</body>
	
</html>