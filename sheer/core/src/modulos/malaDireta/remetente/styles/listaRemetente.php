<?php
	$listaRemetente = $content['malaDiretaRemetente/malaDiretaRemetente_lista'];
?>
<section class="sh-box">

	<header>
		<div><span data-icon="c"></span></div>
		
		<h1>Remetentes para mala direta</h1>
		
		<a href="renderer.php?rd=malaDiretaRemetente/adicionarRemetente" sh-component="overlayLink" data-icon="k"></a>
		
		<div><?php echo $listaRemetente['total'].'/'.$listaRemetente['available']; ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table class="sh-table">
		
			<thead>
				<tr>
					<th class="">Nome de envio</th>
					<th class="data-center sh-w-300">Email de Envio</th>
					<th class="data-center sh-w-300">Lista de E-mail Padrão</th>
					<th class="sh-w-100"></th>
				</tr>
			</thead>
			
			<tbody>
			
				<?php 
					$html = '';
					if( $listaRemetente['total'] > 0 ) {
						
						foreach ( $listaRemetente['results'] as $id=>$remetente ) {

							$html .= '<tr data-id="'.$remetente['id'].'" data-content>';
								$html .= '<td>'.$remetente['nomeEnvio'].'</td>';
								$html .= '<td class="data-center">'.$remetente['emailEnvio'].'</td>';
								if($remetente['listaEmail_lookup']){
									$html .= '<td class="data-center">'.$remetente['listaEmail_lookup'].'</td>';
								}
								else{
									$html .= '<td class="data-center">--</td>';
								}
								$html .= '<td class="data-right">';
									$html .= '<a href="renderer.php?rd=malaDiretaRemetente/editarRemetente&id='.$remetente['id'].'" sh-component="overlayLink" data-icon="s" ></a>';
									$html .= '<a href="action.php?ah=malaDiretaRemetente/removerRemetente&id={id}" sh-component="action" sh-comp-confirm sh-comp-confirmMessage="Deseja realmente remover este remetente?" data-id="'.$remetente['id'].'" data-icon="x" ></a>';
								$html .= '</td>';
							$html .= '</tr>';
						}
						
						echo $html;

					}
				?>
				
			</tbody>
		
		</table>
		
		<?php 
			if( $listaRemetente['total'] == 0 ) {
				echo \Sh\RendererLibrary::getEmptyHolderHtml();
			}
		?>
			
	</div>
	
</section>