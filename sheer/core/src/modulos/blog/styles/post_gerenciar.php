<?php

	$posts = $content['blog/post_lista'];
	
?>

<section class="sh-box">

	<header>
		<div><span data-icon="a"></span></div>

		<h1>Posts</h1>
		
		<a href="index.php?p=blog/post/adicionar" data-icon="k"></a>
		
		<div><?php echo $posts['total'].'/'.$posts['available']; ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table class="sh-table" >
		
			<thead>
				<tr>
					<th>Título</th>
					<th>Categoria</th>
					<th class="sh-w-100"></th>
				</tr>
			</thead>
			
			<tbody>
			
				<?php 
					$html = '';
					if( $posts['results'] ) {
						foreach ( $posts['results'] as $detalhes ) {
							$html .= '<tr data-id="'.$detalhes['id'].'">';
								$html .= '<td>'.$detalhes['titulo'].'</td>';
								$html .= '<td>'.$detalhes['categoria']['titulo'].'</td>';
								$html .= '<td class="data-right">';
									$html .= '<a href="?p=blog/post/visualizar&id='.$detalhes['id'].'" data-icon="l" title="Visualizar" ></a>';
									$html .= '<a href="?p=blog/post/editar&id='.$detalhes['id'].'" data-icon="s" title="Editar" ></a>';
									$html .= '<a href="action.php?ah=blog/post_delete" sh-component="action" data-icon="x" sh-comp-confirm sh-comp-confirmMessage="Deseja realmente apagar este Post? Esta operação não pode ser desfeita." ></a>';
								$html .= '</td>';
							$html .= '</tr>';
						}
						echo $html;
					}
				?>
				
			</tbody>
		
		</table>
		
		<?php 
			if( $posts['total'] == 0 ) {
				echo \Sh\RendererLibrary::getEmptyHolderHtml();
			}
		?>

	</div>
	
</section>