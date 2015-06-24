<?php
	$lista = reset($content['malaDiretaLista/malaDiretaLista_detalhes']['results']);
?>
<section class="sh-box sh-box-amarelo">

	<div class="sh-box-content data-center">
	
		<?php
			$html = '';
			
			$pctParticipacao = 0;
			if( $lista['totalEmails'] > 0 ) {
				$pctParticipacao = round( (100*$lista['totalHabilitados'])/$lista['totalEmails'], 2 );
			}
			
			$html .= '<h2 class="sh-w-1 data-center">';
				$html .= $lista['nome'];
				//Se a lista for complexa preciso adicionar o botão de sincronia
				if( $lista['tipo']==2 ) {
					$html .= '<div style="float: right; font-size: small;" class="data-right sh-color-cinza">';
						$html .= '<a href="action.php?ah=malaDiretaLista/sincronizarLista" data-id="'.$lista['id'].'" sh-component="action" sh-comp-rh="[sheer/modules/maladireta][rh.sincronizarLista]" title="Sincronizar Lista">';
							$html .= '<span style="display: none;"><img src="./resources/images/loaders/azul_bg_transparent_16.gif" /> Sincronizando... Aguarde...</span>';
							$html .= '<span data-icon="j"></span>';
						$html .= '</a>';
					$html .= '</div>';
				}
			$html .= '</h2>';
			
			$html.='<p class="sh-w-200 sh-w-ib data-center">';
				$html.='<strong>Criado Por</strong> <br />';
				$html.= $lista['user']['nome'];
			$html.= '</p>';
			
			$html.='<p class="sh-w-200 sh-w-ib data-center">';
				$html.='<strong>Data</strong> <br />';
				$html.= $lista['criadoEm']['date'];
			$html.= '</p>';
			
			$html.='<p class="sh-w-200 sh-w-ib data-center">';
				$html.='<strong>Emails</strong> <br />';
				$html.= '<span qtip="'.$pctParticipacao.'% do total de '.number_format($lista['totalEmails'], 0, ',', '.').'">'.number_format($lista['totalHabilitados'], 0, ',', '.').'</span>';
			$html.= '</p>';
			
			$html.='<p class="sh-w-200 sh-w-ib data-center">';
				$html.='<strong>Tipo</strong> <br />';
				$html.= $lista['tipo_lookup'];
			$html.= '</p>';

			$html.='<p class="sh-w-200 sh-w-ib data-center">';
				$html.='<strong>Sincronizado Em</strong> <br />';
				if( !$lista['sincronia'] ) {
					$html.= 'Nunca realizada';
				}
				else {
					$html.= $lista['sincronia']['sincronizadoEm']['date'].'<br />';
					$html.= $lista['sincronia']['sincronizadoEm']['hour'].':'.$lista['sincronia']['sincronizadoEm']['minute'];
				}
			$html.= '</p>';
			
			echo $html; 
		?>
		
	</div>
</section>