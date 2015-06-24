<?php
	$destinos = $content['contato/destino_lista'];
?>
<section class="sh-box">

	<header>
		<div><span data-icon="a"></span></div>
		
		<h1>Destino para os emails de contato</h1>
		
		<a href="renderer.php?rd=contato/destino_adicionar" sh-component="overlayLink" data-icon="k"></a>
		
		<div><?php echo $destinos['total'].'/'.$destinos['available']; ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table class="sh-table">
		
			<thead>
				<tr>
					<th class="">Nome</th>
					<th class="data-center sh-w-300">Email</th>
					<th class="data-center sh-w-300">Alias</th>
					<th class="sh-w-100"></th>
				</tr>
			</thead>
			
			<tbody>
			
				<?php 
					$html = '';
					if( $destinos['total'] > 0 ) {
						
						foreach ( $destinos['results'] as $id=>&$destino ) {

							$html .= '<tr data-id="'.$destino['id'].'" data-content>';
								$html .= '<td>'.$destino['nome'].'</td>';
								$html .= '<td class="data-center">'.$destino['email'].'</td>';
								$html .= '<td class="data-center">'.$destino['alias'].'</td>';
								$html .= '<td class="data-right">';
									$html .= '<a href="renderer.php?rd=contato/destino_editar&id='.$destino['id'].'" sh-component="overlayLink" data-icon="s" ></a>';
									$html .= '<a href="action.php?ah=contato/destino_delete" sh-component="action" sh-comp-confirm data-id="'.$destino['id'].'" data-icon="x" ></a>';
								$html .= '</td>';
							$html .= '</tr>';
							
						}
						
						echo $html;

					}
				?>
				
			</tbody>
		
		</table>
		
		<?php 
			if( $destinos['total'] == 0 ) {
				echo \Sh\RendererLibrary::getEmptyHolderHtml();
			}
		?>
			
	</div>
	
</section>