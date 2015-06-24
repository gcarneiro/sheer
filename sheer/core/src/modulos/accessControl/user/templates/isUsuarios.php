<section class="sh-box sh-box-agua">
		
	<header>
		<div><span data-icon="e"></span></div>
		
		<h1>Faça sua busca</h1>
	</header>

	<div class="sh-box-content">
		
		<form class="sh-form" action="renderer.php?rd=user/gerenciarUsuarios" method="post" sh-is sh-is-holder="#listaUsuarios" sh-is-autosend="off" autocomplete="off">
			<fieldset>
			
				<div class="sh-form-field sh-w-250">
					<label for="nome">Nome</label>
					<input type="text" id="nome" name="nome" value="" placeholder="Nome" />  
				</div>
				
				<div class="sh-form-field sh-w-300">
					<label for="email">Email</label>
					<input type="text" id="email" name="email" value="" placeholder="Email" />  
				</div>

				<div class="sh-form-field sh-w-250">
					<label for="login">Login</label>
					<input type="text" id="login" name="login" value="" placeholder="Login" />  
				</div>

				<div class="sh-form-field sh-w-150">
					<label for="habilitado">Habilitado</label>
					<select id="habilitado" name="habilitado">
						<option value="">Selecione</option>
						<option value="1">Sim</option>
						<option value="2">Não</option>
					</select> 
				</div>
				
			
			</fieldset>
			
		</form>
		
	</div>
	
</section>