<?php
	$usuario = reset($content['user/user_detalhes']['results']);
	$profiles = $content['user/userProfile_listaSimples'];
	
	//GERO UM ARRAY COM OS PROFILES QUE USUÁRIO POSSUI
	$profilesExistentes = array();
	if($usuario['profiles']) {
		foreach ( $usuario['profiles'] as $profileUser ) {
			$profilesExistentes[$profileUser['profile']['id']] = $profileUser['profile']['nome'];
		}
	}
?>

<div class="sh-box-avatar sh-box-avatar-90">
	
	<div>
		<section class="sh-box sh-box-azul">
			<header>
				<!-- A primeira div deve ser do avatar -->
				<div>
					<img src="resources/images/avatar/pessoa_m_90.png" />
				</div>
				
				<h1><?php echo $usuario['nome']; ?></h1>
			</header>
			
			<div class="sh-box-content">
			
				<p class="data-center">
					<a href="renderer.php?rd=user/atualizarUsuario&id=<?php echo $usuario['id']; ?>" sh-component="overlayLink" data-icon="s" title="Editar Usuário"></a>
				</p>
				<hr />
				
				<h3>Email</h3>
				<p><?php echo $usuario['email']; ?></p>
	
				<h3>Login</h3>
				<p><?php echo $usuario['login']; ?></p>
	
				<h3>Senha <a href="renderer.php?rd=user/trocarSenhaUsuarioSemAntiga&id=<?php echo $usuario['id']; ?>" style="float: right; font-size: 0.8em;" title="Alterar Senha" data-icon="s" sh-component="overlayLink"></a></h3>
				<p>**********</p>
				
				<h3>Multi-Seção</h3>
				<p><?php echo $usuario['multiSecao_lookup']; ?></p>
	
				<h3>Habilitado</h3>
				<p><?php echo $usuario['habilitado_lookup']; ?></p>
				
			</div>
		</section>
	</div>
	
	<div>
		<div class="sh-w-1-2 sh-w-min-500">
			<section class="sh-box sh-box-azul sh-margin-w">
				<header>
					<h1>Perfis de Acesso</h1>
				</header>
				
				<div class="sh-box-content">
					<div>
						<p>Determine aqui os perfis de acesso que este usuário deve possuir</p>
					</div>
					
					<form class="sh-form" sh-form action="action.php?ah=user/atualizarPerfisAcesso" novalidate>
					
						<fieldset>
							
							<?php 
								$html = '';
								$html .= '<input type="hidden" name="id" value="'.$usuario['id'].'" />';
								$html .= '<div class="sh-form-field">';
								
								//iterando pelos profiles para exibir todos
								foreach ( $profiles['results'] as $idProfile=>$profile ) {
									$c = '';
									if( isset($profilesExistentes[$idProfile]) ) {
										$c='checked="checked"';
									}

									$html .= '<label class="sh-radio">';
										$html .= '<input type="checkbox" name="profiles['.$profile['id'].']" value="'.$profile['id'].'" '.$c.' />'.$profile['nome'];
									$html .= '</label><br />';
									$html .= '<p style="margin: 0;"><small>'.$profile['descricao'].'</small></p>';
									
								}
								$html .= '</div>';
								
								echo $html;
							
							?>
							
							<div class="sh-btn-holder">
								<button class="sh-btn-azul">Salvar</button>
							</div>
						
						</fieldset>
					
					</form>
					
				</div>
			</section>
			
			<section class="sh-box sh-box-azul">
				<header>
					<h1>Acesso Padrão</h1>
				</header>
				
				<div class="sh-box-content">
					<div>
						<p>Escolha o perfil de acesso padrão do usuário</p>
					</div>
					
					<form class="sh-form" sh-form action="action.php?ah=user/user_update" novalidate>
					
						<fieldset>
						
							<input type="hidden" name="id" value="<?php echo $usuario['id']; ?>" required />
						
							<div class="sh-form-field sh-w-1">
								<select id="defaultProfile" name="defaultProfile">
									<?php 
										$html = '';
										
										foreach ($profilesExistentes as $idProfile=>$nomeProfile) {
											$s = '';
											if( $usuario['defaultProfile'] == $idProfile ) {
												$s = 'selected="selected"';
											}
											$html .= '<option value="'.$idProfile.'" '.$s.'>'.$nomeProfile.'</option>';
										}
										echo $html;
									?>
								</select>
							</div>
							
							<div class="sh-btn-holder">
								<button class="sh-btn-azul">Salvar</button>
							</div>
						
						</fieldset>
					
					</form>
					
				</div>
			</section>
		</div>
		
	</div>
	
</div>