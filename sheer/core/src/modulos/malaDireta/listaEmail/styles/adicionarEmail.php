<?php
	$listaEmailDS = \Sh\ModuleFactory::getModuleDataSource('malaDiretaListaEmail', 'malaDiretaListaEmail');
?>
<section class="sh-box sh-box-laranja sh-margin-x-auto ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Adicionar E-mail</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=malaDiretaListaEmail/adicionarEmail" method="post" novalidate sh-form >
		
			<fieldset>
			
				<h3>Informe os dados do e-mail</h3>
				
				<div class="sh-form-fs">
				
					<?php
						$html = '';
						
						$html .= '<input type="hidden" name="idLista" id="idLista" value="'.$_GET['idLista'].'" />';
						
						//NOME
						$html .= \Sh\RendererLibrary::renderFieldBox($listaEmailDS->getField('nome', false), null, array(
								'placeholder'=>'Nome'
						), array('div'=>array('class'=>'sh-w-1-1')));

						//E-MAIL
						$html .= \Sh\RendererLibrary::renderFieldBox($listaEmailDS->getField('email', false), null, array(
								'validationType'=>'email',
								'placeholder' => 'E-mail',
								'required'=>'true'
						), array('div'=>array('class'=>'sh-w-1-1')));

						//ENVIAR
// 						$html .= \Sh\RendererLibrary::renderFieldBox($listaEmailDS->getField('enviar', false), null, array(
// 								'required'=>'true'
// 						), array('div'=>array('class'=>'sh-w-1-1')));

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