<?php
	$userDS = \Sh\ModuleFactory::getModuleDataSource('user', 'user');
	$usuario = reset($content['user/user_detalhes']['results']);
?>

<section class="sh-box sh-box-laranja sh-margin-x-auto sh-w-700 ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Trocar a senha do usuário</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=user/atualizarSenhaSemConfirmacao" method="post" novalidate sh-form >
		
			<fieldset class="sh-grid-box">
			
				<h3>Informe a nova senha</h3>
				
				<div class="sh-form-fs">
				
					<input type="hidden" name="id" required value="<?php echo $usuario['id']; ?>" />
					
					<div class="sh-w-1-2">
						<label for="password">Senha</label>
						<input type="password" id="password" name="password" placeholder="Senha" value="" required />
					</div>
					
					<div class="sh-w-1-2">
						<label for="passwordConfirmar">Confirmar Senha</label>
						<input type="password" id="passwordConfirmar" name="passwordConfirmar" placeholder="Confirmar Senha" value="" required />
					</div>
					
					
					<div class="sh-btn-holder">
						<button type="submit" class="sh-btn-laranja">Enviar</button>
					</div>
				</div>
				
			</fieldset>
		
		</form>
	
	</div>

</section>