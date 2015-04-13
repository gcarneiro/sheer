<?php
	$user = reset($content['user/user_detalhes']['results']);
?>

<div class="sh-margin-x-auto sh-w-max-1000 sh-w-min-800">

	<section class="sh-box sh-box-iagua">
		<div class="sh-quadro-dash">
			<p>Olá <strong><?php echo $user['nome'];?></strong>, foi requisitado que em seu próximo login você efetua-se a troca da sua senha. Esta alteração se dá por motivos de segurança.</p>
		</div>
	</section>
	
	<section class="sh-box sh-box-laranja">
	
		<header>
			<h1 class="data-center"><strong>Troque sua senha</strong></h1>
		</header>
		
		<div class="sh-box-content">
			<form action="action.php?ah=user/trocarSenhaRequisitadaLogin" method="post" class="sh-form" sh-form novalidate autocomplete="off">
			
				<fieldset>
				
					<p>Você deverá inserir uma nova senha e depois será redirecionado para a página de login novamente para confirmação</p>
					
					<div class="sh-w-1-2 sh-form-field">
						<label for="password">Nova Senha</label>
						<input type="password" id="password" name="password" placeholder="Nova Senha" required />
					</div>
					
					<div class="sh-w-1-2 sh-form-field">
						<label for="confirmPassword">Confirmar Senha</label>
						<input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmar Senha" required />
					</div>
					
				</fieldset>
				
				<div class="sh-btn-holder">
					<button class="sh-btn-laranja" type="submit">Alterar</button>
				</div>
			
			</form>
		</div>
	
	</section>
	
</div>