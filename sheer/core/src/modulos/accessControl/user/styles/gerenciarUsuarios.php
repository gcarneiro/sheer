<?php
	$usuarios = $content['user/user_lista'];
?>

<section class="sh-box">

	<header>
		<div><span data-icon="a"></span></div>

		<h1>Usuários do sistema</h1>
		
		<a href="renderer.php?rd=user/adicionarUsuario" sh-component="overlayLink" data-icon="k"></a>

		<div><?php echo $usuarios['total'].'/'.$usuarios['available']; ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table class="sh-table " >
		
			<thead>
				<tr>
					<th class="sh-w-50"></th>
					<th>Nome</th>
					<th>Email</th>
					<th>Login</th>
					<th class="sh-w-100">Habilitado</th>
					<th class="sh-w-100">Multi-Seção</th>
					<th class="sh-w-50"></th>
				</tr>
			</thead>
			
			<tbody>
			
				<?php 
					$html = '';
					if( $usuarios['results'] ) {
						
						foreach ( $usuarios['results'] as $user ) {
							$html .= '<tr data-id="'.$user['id'].'" data-content>';
								$html .= '<td class="data-right">';
									$html .= '<a href="?p=usuarios/detalhes&id='.$user['id'].'" data-icon="l" ></a>';
								$html .= '</td>';
								$html .= '<td>'.$user['nome'].'</td>';
								$html .= '<td>'.$user['email'].'</td>';
								$html .= '<td>'.$user['login'].'</td>';
								$html .= '<td class="data-center">'.$user['habilitado_lookup'].'</td>';
								$html .= '<td class="data-center">'.$user['multiSecao_lookup'].'</td>';
								$html .= '<td class="data-right">';
									$html .= '<a href="?p=usuarios/detalhes&id='.$user['id'].'" data-icon="l" ></a>';
								$html .= '</td>';
							$html .= '</tr>';
						}
						echo $html;
					}
				?>
				
			</tbody>
		
		</table>
		
		<?php 
			$html = '';
			if( $usuarios['total'] == 0 ) {
				$html = '<div class="data-center">';
					$html .= '<p data-icon="g" style="font-size: 1.3em;"></p>';
					$html .= '<p style="text-transform: uppercase">Nenhum resultado encontrado</p>';
				$html .= '</div>';
				echo $html;
			}
		?>

	</div>
	
</section>