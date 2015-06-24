<?php
	$listasEmail = $content['malaDiretaLista/malaDiretaLista_lista'];
?>
<section class="sh-box">

	<header>
		<div><span data-icon="a"></span></div>
		
		<h1>Listas de E-mails</h1>
		
		<a href="renderer.php?rd=malaDiretaLista/adicionarLista" sh-component="overlayLink" data-icon="k"></a>
		
		<div><?php echo $listasEmail['total'].'/'.$listasEmail['available']; ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table class="sh-table">
				
			<thead>
				<tr>
					<th>Nome</th>
					<th class="data-center">Emails</th>
					<th class="data-center">Tipo</th>
					<th class="sh-w-100"></th>
				</tr>
			</thead>
			
			<tbody>
				<?php
				$html = '';
				if( $listasEmail['total'] > 0 ) {
						
					foreach ( $listasEmail['results'] as $id=>$lista ) {

						$pctParticipacao = 0;
						if( $lista['totalEmails'] > 0 ) {
							$pctParticipacao = round( (100*$lista['totalHabilitados'])/$lista['totalEmails'], 2 );
						}
				
						$html .= '<tr data-id="'.$lista['id'].'" data-content>';
							$html .= '<td>'.$lista['nome'].'</td>';
							$html .= '<td class="data-center"><span qtip="'.$pctParticipacao.'% do total de '.number_format($lista['totalEmails'], 0, ',', '.').'" qtip-position="right">'.number_format($lista['totalHabilitados'], 0, ',', '.').'<span></td>';
							$html .= '<td class="data-center">'.$lista['tipo_lookup'].'</td>';
							$html .= '<td class="data-right">';
								$html .= '<a title="Ver Lista" href="?p=malaDireta/listas/detalhes&idLista='.$lista['id'].'" data-icon="l"></a>';
							$html .= '</td>';
						$html .= '</tr>';
					}
						
					echo $html;
				}
				?>
			</tbody>
		
		</table>
		
		<?php 
			if( $listasEmail['total'] == 0 ) {
				echo \Sh\RendererLibrary::getEmptyHolderHtml();
			}
		?>

	</div>
	
</section>
