<?php
	$agendamentoDS = \Sh\ModuleFactory::getModuleDataSource('malaDiretaAgendamento', 'malaDiretaAgendamento');
	$campanha = reset($content['malaDiretaCampanha/malaDiretaCampanha_detalhes']['results']);
?>
<section class="sh-box sh-box-laranja sh-w-600 sh-margin-x-auto ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Adicionar Agendamento</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=malaDiretaAgendamento/adicionarAgendamento" method="post" novalidate sh-form >
		
			<fieldset class="sh-grid-box">
			
				<h3>Informe os dados do agendamento</h3>
				<div class="sh-form-fs">
				<?php
					
						$html = '';
						$html = '<input type="hidden" name="idCampanha" id="campanha" value="'.$campanha['id'].'" />';
						
						//DATA
						$html .= \Sh\RendererLibrary::renderFieldBox($agendamentoDS->getField('data', false), null, array(
								'placeholder'			=> 'Data para Envio',
								'datePicker'			=> 'datePicker',
								'datePicker-startDate' 	=> date('d/m/Y'),
								'mask'					=> 'date',
								'validationType'		=> 'date',
								'required'				=> true
						), array('div'=>array('class'=>'sh-w-1-2')));

						//HORA
						$html .= \Sh\RendererLibrary::renderFieldBox($agendamentoDS->getField('hora', false), null, array(
								'placeholder'		=> 'Hora para Envio',
								'required'			=> true,
								'mask'				=> 'time',
								'validationType' 	=> 'time'
						), array('div'=>array('class'=>'sh-w-1-2')));
						
						//LISTA DE EMAILS
						$html .= \Sh\RendererLibrary::renderFieldBox($agendamentoDS->getField('idLista', false), $campanha['remetente']['listaEmail'], array(
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
