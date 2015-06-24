<?php
	$contatos = $content['contato/contato_lista'];
?>
<section class="sh-box">

	<header>
		<div><span data-icon="a"></span></div>
		
		<h1>Contatos recebidos pelo site</h1>
		
<!-- 		<a href="renderer.php?rd=contato/contato_adicionar" sh-component="overlayLink" data-icon="k"></a> -->
		
		<div><?php echo $contatos['total'].'/'.$contatos['available']; ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table class="sh-table">
		
			<thead>
				<tr>
					<th class="">Destino</th>
					<th class="">Nome</th>
					<th class="data-center">Email</th>
					<th class="data-center sh-w-150">Telefone</th>
					<th class="data-center">Assunto</th>
					<th class="data-center">Enviado Em</th>
					<th class="sh-w-100"></th>
				</tr>
			</thead>
			
			<tbody>
			
				<?php 
					$html = '';
					if( $contatos['total'] > 0 ) {
						
						foreach ( $contatos['results'] as $id=>&$contato ) {

							$html .= '<tr data-id="'.$contato['id'].'" data-content>';
								$html .= '<td>'.$contato['destino']['nome'].'</td>';
								$html .= '<td>'.$contato['nome'].'</td>';
								$html .= '<td class="data-center">'.$contato['email'].'</td>';
								$html .= '<td class="data-center">'.$contato['telefone'].'</td>';
								$html .= '<td class="data-center">'.$contato['assunto'].'</td>';
								$html .= '<td class="data-center">'.$contato['enviadoEm']['date'].'</td>';
								$html .= '<td class="data-right">';
									$html .= '<a href="action.php?ah=contato/contato_arquivar&id={id}" sh-component="action" sh-comp-confirm data-id="'.$contato['id'].'" data-icon="x" ></a>';
								$html .= '</td>';
							$html .= '</tr>';
							
						}
						
						echo $html;

					}
				?>
				
			</tbody>
		
		</table>
		
		<?php 
			if( $contatos['total'] == 0 ) {
				echo \Sh\RendererLibrary::getEmptyHolderHtml();
			}
		?>
			
	</div>
	
</section>