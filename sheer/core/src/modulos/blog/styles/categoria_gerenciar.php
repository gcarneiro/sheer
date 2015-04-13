<?php

	$categorias = $content['blog/categoria_lista'];
	
?>

<section class="sh-box">

	<header>
		<div><span data-icon="a"></span></div>

		<h1>Categorias</h1>
		
		<a href="renderer.php?rd=blog/categoria_add" sh-component="overlayLink" data-icon="k"></a>
		
		<div><?php echo $categorias['total'].'/'.$categorias['available']; ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table class="sh-table" >
		
			<thead>
				<tr>
					<th>Título</th>
					<th class="sh-w-100 data-center">Postagens</th>
					<th class="sh-w-100 data-center">Posição</th>
					<th class="sh-w-100"></th>
				</tr>
			</thead>
			
			<tbody>
			
				<?php 
					$html = '';
					if( $categorias['results'] ) {
						foreach ( $categorias['results'] as $detalhes ) {
							$html .= '<tr data-id="'.$detalhes['id'].'">';
								$html .= '<td>'.$detalhes['titulo'].'</td>';
								$html .= '<td class="data-center">'.count($detalhes['posts']).'</td>';
								$html .= '<td class="data-center">'.$detalhes['posicao'].'</td>';
								$html .= '<td class="data-right">';
									$html .= '<a href="renderer.php?rd=blog/categoria_update&id='.$detalhes['id'].'" sh-component="overlayLink" data-icon="s" ></a>';
// 									$html .= '<a href="action.php?ah=localidade/bairro_delete&id='.$detalhes['id'].'" sh-component="action" sh-comp-rh="[sheer/modules/localidade][bairros.gerenciar.deleteResponseHandler]" data-id="'.$detalhes['id'].'" data-icon="x" ></a>';
								$html .= '</td>';
							$html .= '</tr>';
						}
						echo $html;
					}
				?>
				
			</tbody>
		
		</table>
		
		<?php 
			if( $categorias['total'] == 0 ) {
				echo \Sh\RendererLibrary::getEmptyHolderHtml();
			}
		?>

	</div>
	
</section>