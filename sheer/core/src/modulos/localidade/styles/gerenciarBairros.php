<?php
	$bairros = $content['localidade/listaBairrosPorCidade'];
?>
<section class="sh-box">

	<header>
		<div><span data-icon="a"></span></div>

		<h1>Bairros</h1>
		
		<?php 
			if( isset($requestParameters['idUf']) && $requestParameters['idUf'] && isset($requestParameters['idCidade']) && $requestParameters['idCidade'] ) {
				echo '<a href="renderer.php?rd=localidade/adicionarBairro&idUf='.$requestParameters['idUf'].'&idCidade='.$requestParameters['idCidade'].'" sh-component="overlayLink" sh-comp-contentFunction="[sheer/modules/localidade][bairros.gerenciar.adicionarContentFunction]" data-icon="k" class="adicionarItem"></a>';
			}
		?>

		<div><?php echo $bairros['total'].'/'.$bairros['available']; ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table id="listaBairros" class="sh-table sh-table-admin" >
		
			<thead>
				<tr>
					<th><input type="checkbox" /></th>
					<th>Nome</th>
					<th class="sh-w-100"></th>
				</tr>
			</thead>
			
			<tbody>
			
				<?php 
					$html = '';
					if( $bairros['results'] ) {
						foreach ( $bairros['results'] as $detalhes ) {
							$html .= '<tr data-id="'.$detalhes['id'].'" data-content>';
								$html .= '<td><input type="checkbox" sh-check="" name="itemId[]" value="'.$detalhes['id'].'" /></td>';
								$html .= '<td>'.$detalhes['nome'].'</td>';
								$html .= '<td class="data-right">';
									$html .= '<a href="renderer.php?rd=localidade/atualizarBairro&id='.$detalhes['id'].'" sh-component="overlayLink" data-id="'.$detalhes['id'].'" data-icon="s" ></a>';
									$html .= '<a href="action.php?ah=localidade/bairro_delete&id='.$detalhes['id'].'" sh-component="action" sh-comp-rh="[sheer/modules/localidade][bairros.gerenciar.deleteResponseHandler]" data-id="'.$detalhes['id'].'" data-icon="x" ></a>';
								$html .= '</td>';
							$html .= '</tr>';
						}
						echo $html;
					}
				?>
				
			</tbody>
		
		</table>
		
		<?php 
			if( $bairros['total'] == 0 ) {
				echo \Sh\RendererLibrary::getEmptyHolderHtml();
			}
		?>

	</div>
	
</section>