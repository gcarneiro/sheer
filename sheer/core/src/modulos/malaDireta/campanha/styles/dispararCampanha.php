<?php
	$disparoDS = \Sh\ModuleFactory::getModuleDataSource('malaDiretaDisparo', 'malaDiretaDisparo');
	
	$campanha = reset($content['malaDiretaCampanha/malaDiretaCampanha_detalhes']['results']);
?>
<section class="sh-box sh-box-laranja sh-w-600 sh-margin-x-auto ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Disparar E-mails</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=malaDiretaDisparo/dispararMalaDireta" method="post" novalidate sh-form >
		
			<fieldset class="sh-grid-box">
				<h3>Selecione a lista de e-mails</h3>
				
				<div class="sh-form-fs">
					<input type="hidden" name="idCampanha" id="idCampanha" value="<?php echo $campanha['id']; ?>" />
					
					<?php
						$html = '';
						//LISTA DE EMAILS
						$html .= \Sh\RendererLibrary::renderFieldBox($disparoDS->getField('idLista', false), $campanha['remetente']['listaEmail'], array(
								'placeholder'=>'Lista de Emails',
								'required'=>'true',
						), array('div'=>array('class'=>'sh-w-1')));
						
						echo $html;
					 ?>
				</div>
			</fieldset>
				
			
			<div class="sh-btn-holder">
				<button type="submit" class="sh-btn-laranja">Enviar</button>
			</div>
		
		</form>
	
	</div>

</section>
					