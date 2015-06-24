<?php 
	$usuario = reset($content['user/user_detalhes']['results']);
	$authentication = \Sh\AuthenticationControl::getAuthenticatedUserInfo();
?>

<div class="sheer-barra-item" sh-component="navigation" sh-component-target="menuPerfisUsuario">
	<a href="#" >
		<img src="./resources/images/avatar/pessoa_m_30.png" />
	</a>

	<p><?php echo preg_replace('/(\s+)|(-)/', ' ', $authentication['profile']['nome']); ?></p>
	<div><span data-icon="t"></span></div>
</div>

<template id="menuPerfisUsuario" style="display: none;">

	<nav class="sheer-nav-inline" style="z-index: 100000" data-id="<?php echo $usuario['id']; ?>">
		<header><?php echo $usuario['nome']; ?></header>
		
		<div>
			<ul>
				<?php 
					$html = '';
					if( $usuario['profiles'] ) {
						foreach ($usuario['profiles'] as $idProfile=>$profile) {
							$html .= '<li>';
								$html .= '<a href="action.php?ah=user/alteraPerfilAcesso" sh-component="action" sh-comp-rh="[sheer/adm][rh.trocarPerfil]" data-id="'.$profile['idProfile'].'">'.$profile['profile']['nome'].'</a>';
							$html .= '</li>';
						}
					}
					echo $html;
				?>
			</ul>
		</div>
		
		<footer>
			<a href="action.php?ah=user/efetuarLogout" sh-component="action" sh-comp-rh="[sheer/adm][rh.logout]">logout x</a>	
		</footer>
	</nav>

</template>