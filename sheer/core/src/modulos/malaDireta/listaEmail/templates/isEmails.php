<?php
	$listaEmailDS = \Sh\ModuleFactory::getModuleDataSource('malaDiretaListaEmail', 'malaDiretaListaEmail');
?>
<section class="sh-box sh-box-agua">
		
	<header>
		<div><span data-icon="e"></span></div>
		
		<h1>Faça sua busca</h1>
	</header>

	<div class="sh-box-content">
		<form class="sh-form" action="renderer.php?rd=malaDiretaListaEmail/listaEmails" method="post" 
				sh-is sh-is-holder="#isEmails" autocomplete="off"
		>
			<fieldset>
			
				<?php
					$html = '';
					$html .= '<input type="hidden" name="idLista" value="'.$_GET['idLista'].'" />';
					
					//NOME
					$html .= \Sh\RendererLibrary::renderFieldBox($listaEmailDS->getField('nome', false), null, array(
						'placeholder'=>'Nome',
						'id'=>'isNome',
						'required'=>false,
					), array('div'=>array('class'=>'sh-form-field')));
					
					//E-MAIL
					$html .= \Sh\RendererLibrary::renderFieldBox($listaEmailDS->getField('email', false), null, array(
						'placeholder'=>'E-mail',
						'id'=>'isEmail',
						'required'=>false,
					), array('div'=>array('class'=>'sh-form-field')));

					//ENVIAR
					$html .= \Sh\RendererLibrary::renderFieldBox($listaEmailDS->getField('enviar', false), null, array(
						'id'=>'isEnviar',
						'required'=>false,
						'blankOption' => 'Todos'
					), array('div'=>array('class'=>'sh-form-field')));

					echo $html;
				 ?>
				
			</fieldset>
			
		</form>
	</div>
	
</section>