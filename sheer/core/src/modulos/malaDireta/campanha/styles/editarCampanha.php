<?php
	$campanhaDs = \Sh\ModuleFactory::getModuleDataSource('malaDiretaCampanha', 'malaDiretaCampanha');
	$campanha = reset($content['malaDiretaCampanha/malaDiretaCampanha_detalhes']['results']);
	$agendamentos = $content['malaDiretaAgendamento/malaDiretaAgendamento_lista'];
	$disparos = $content['malaDiretaDisparo/malaDiretaDisparo_lista'];
	
	//FIXME REFAZER
// 	$agendamentos = \Sh\ContentProviderManager::loadContent('malaDiretaAgendamento/malaDiretaAgendamento_lista', array('idCampanha'=>$campanha['id']));
// 	$disparos = \Sh\ContentProviderManager::loadContent('malaDiretaDisparo/malaDiretaDisparo_lista', array('idCampanha'=> $campanha['id']));
?>
<section class="sh-box sh-box-laranja sh-margin-x-auto ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Editar Campanha</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form sh-form-laranja" action="action.php?ah=malaDiretaCampanha/editarCampanha" method="post" novalidate sh-form >
		
			<fieldset>
			
				<h3>Informe os dados da campanha</h3>
				<div class="sh-form-fs">
					<?php
						$idUser = \Sh\AuthenticationControl::getAuthenticatedUserInfo('id');
						
						$html = '';
						$html .= '<input type="hidden" name="id" id="id" value="'.$campanha['id'].'" />';
						
						//ASSUNTO
						$html .= \Sh\RendererLibrary::renderFieldBox($campanhaDs->getField('assunto', false), $campanha['assunto'], array(
								'placeholder'=>'Assunto',
								'required'=>'true',
						), array('div'=>array('class'=>'sh-w-1')));

						//REMETENTE
						$html .= \Sh\RendererLibrary::renderFieldBox($campanhaDs->getField('idRemetente', false), $campanha['idRemetente'], array(
								'placeholder'=>'Assunto',
								'required'=>'true',
						), array('div'=>array('class'=>'sh-w-1')));

						//HTML
						//verifico se tenho agendamentos ou disparos. tendo eu não exibo o bloco de html
						if( $agendamentos['total'] == 0 && $disparos['total'] == 0 ){
							$html .= \Sh\RendererLibrary::renderFieldBox($campanhaDs->getField('html', false), $campanha['html'], array(
									'required'=>'true'
							), array('div'=>array('class'=>'sh-w-1')));
						}

						echo $html;
					?>
				
				</div>
			</fieldset>
			
			<div class="sh-btn-holder">
				<button class="sh-btn-laranja">Salvar</button>
			</div>
		
		</form>
	
	</div>

</section>
