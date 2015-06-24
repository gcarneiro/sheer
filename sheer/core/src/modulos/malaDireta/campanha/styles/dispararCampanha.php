<?php
	$disparoDS = \Sh\ModuleFactory::getModuleDataSource('malaDiretaDisparo', 'malaDiretaDisparo');
	$listaEmail = \Sh\ContentProviderManager::loadContent('malaDiretaLista/malaDiretaLista_listaSimples');
	$campanha = reset($content['malaDiretaCampanha/malaDiretaCampanha_detalhes']['results']);
?>
<section class="sh-box sh-box-azul sh-w-600 sh-margin-x-auto ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Disparar E-mails</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form sh-form-azul" action="action.php?ah=malaDiretaDisparo/dispararMalaDireta" method="post" novalidate sh-form >
		
			<fieldset class="sh-grid-box">
				<h3>Selecione a lista de e-mails</h3>
				
				<div class="sh-form-fs">
					<input type="hidden" name="idCampanha" id="idCampanha" value="<?php echo $campanha['id']; ?>" />
					
					<?php
						$html = '';
							
							//LISTA DE EMAILS
							$html .= '<div class="sh-w-1">';
								$html .= '<label for="idLista">Lista de Emails</label>';
								$html .= '<select id="idLista" name="idLista[]" multiple="multiple" required placeholder="Selecione">';
									
									foreach ($listaEmail['results'] as $detalhe) {
										$html .= '<option id="'.$detalhe['id'].'" value="'.$detalhe['id'].'">'.$detalhe['nome'].'</option>';	
									}
									
								$html .= '</select>';
							$html .= '</div>';
							
						echo $html;
					 ?>
				</div>
			</fieldset>
				
			
			<div class="sh-btn-holder">
				<button type="submit" class="sh-btn-azul">Enviar</button>
			</div>
		
		</form>
	
	</div>

</section>
					