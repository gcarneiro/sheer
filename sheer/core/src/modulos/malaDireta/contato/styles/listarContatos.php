<?php
	$contatos = $content['malaDiretaContato/malaDiretaContato_lista'];
?>
<section class="sh-box sh-box-azul">

	<header>
		<h1>Contatos cadastrados</h1>
		
		<div><?php echo $contatos['total'].'/'.$contatos['available']; ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table class="sh-table">
				
			<thead>
				<tr>
					<th>Email</th>
					<th>Nome</th>
				</tr>
			</thead>
			
			<tbody>
				<?php
				$html = '';
				if( $contatos['total'] > 0 ) {
						
					foreach ( $contatos['results'] as $id=>&$contato ) {
						
						//Determinado string do nome do contato
						$strNome = '--';
						if( isset($contato['nome']) && $contato['nome'] ) {
							$strNome = $contato['nome'];
						}

						$html .= '<tr data-id="'.$contato['id'].'" data-content>';
							$html .= '<td>'.$contato['email'].'</td>';
							$html .= '<td>'.$strNome.'</td>';
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
