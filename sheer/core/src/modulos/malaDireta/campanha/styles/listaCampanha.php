<?php
	$listaCampanha = $content['malaDiretaCampanha/malaDiretaCampanha_lista'];
?>
<section class="sh-box">

	<header>
		<div><span data-icon="a"></span></div>
		
		<h1>Lista de campanhas</h1>
		
		<a href="renderer.php?rd=malaDiretaCampanha/adicionarCampanha" sh-component="overlayLink" data-icon="k"></a>
		
		<div><?php echo $listaCampanha['total'].'/'.$listaCampanha['available']; ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table class="sh-table">
		
			<thead>
				<tr>
					<th>Assunto</th>
					<th class="data-center">Remetente</th>
					<th class="data-center">Data</th>
					<th class="data-center">Conteúdo da campanha</th>
					<th class="sh-w-100"></th>
				</tr>
			</thead>
			
			<tbody>
			
				<?php 
					$html = '';
					if( $listaCampanha['total'] > 0 ) {
						
						foreach ( $listaCampanha['results'] as $id=>$campanha ) {

							$html .= '<tr data-id="'.$campanha['id'].'" data-content>';
								$html .= '<td>'.$campanha['assunto'].'</td>';
								$html .= '<td class="data-center"><span title="'.$campanha['remetente']['emailEnvio'].'">'.$campanha['remetente']['nomeEnvio'].'<span></td>';
								$html .= '<td class="data-center">'.$campanha['criadoEm']['date'].'</td>';
								$html .= '<td class="data-center"><a target="_blank" href="renderer.php?rd=malaDiretaCampanha/ml&htmlResponse=1&idCampanha='.$campanha['id'].'">ver</td>';
								$html .= '<td class="data-right">';
									$html .= '<a title="Ver Campanha" style="margin-right:5px;" href="?p=malaDireta/campanha/dashboard&id='.$campanha['id'].'" data-icon="l"></a>';
									$html .= '<a title="Remover Campanha" href="action.php?ah=malaDiretaCampanha/removerCampanha&id='.$campanha['id'].'" sh-component="action" sh-comp-confirm sh-comp-confirmMessage="Deseja realmente remover essa campanha?" data-icon="x"></a>';
								$html .= '</td>';
							$html .= '</tr>';
						}
						
						echo $html;

					}
				?>
				
			</tbody>
		
		</table>
		<?php
			if($listaCampanha['total']== 0 ){
				echo \Sh\RendererLibrary::getEmptyHolderHtml();
			} 
		?>

	</div>
	
</section>
