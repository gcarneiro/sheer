<?php
	$userDS = \Sh\ModuleFactory::getModuleDataSource('user', 'user');
?>

<section class="sh-box sh-box-laranja sh-margin-x-auto sh-w-700 ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Cadastrar novo usuário</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=user/user_add" method="post" novalidate sh-form >
		
			<fieldset class="sh-grid-box">
			
				<h3>Informe os dados do usuário</h3>
				
				<div class="sh-form-fs">
					<?php
						$html = '';
						
						$html .= \Sh\RendererLibrary::renderFieldBox($userDS->getField('nome', false), null, array('placeholder' => 'Nome'), array(
							'div' => array('class'=>'sh-w-1')
						));

						$html .= \Sh\RendererLibrary::renderFieldBox($userDS->getField('email', false), null, array(
								'validationType' => 'email',
								'required' => true,
								'placeholder' => 'Email'
						), array(
								'div' => array('class'=>'sh-w-1')
						));

						echo $html;
					
					?>
					
					<div class="sh-w-1-2">
						<label for="login">Login</label>
						<input type="text" id="login" name="login" placeholder="Login" />
						<small>Deixe vazio para utilizar o email como login</small>
					</div>
					
					<div class="sh-w-1-2">
						<label for="password">Senha</label>
						<input type="password" id="password" name="password" required placeholder="Senha" />
					</div>
					
					<?php
						$html = '';
						
						$html .= \Sh\RendererLibrary::renderFieldBox($userDS->getField('habilitado', false), null, array(
							'required' => true,
						), array(
							'div' => array('class'=>'sh-w-1-3')
						));
						$html .= \Sh\RendererLibrary::renderFieldBox($userDS->getField('multiSecao', false), null, array(
							'required' => true,
						), array(
							'div' => array('class'=>'sh-w-1-3')
						));

						echo $html;
					
					?>
					
					<div class="sh-btn-holder">
						<button class="sh-btn-laranja">Enviar</button>
					</div>
				</div>
				
			</fieldset>
		
		</form>
	
	</div>

</section>