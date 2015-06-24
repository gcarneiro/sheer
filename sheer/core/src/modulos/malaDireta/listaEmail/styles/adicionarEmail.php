<?php
	$contatoDS = \Sh\ModuleFactory::getModuleDataSource('malaDiretaContato', 'malaDiretaContato');
?>
<section class="sh-box sh-box-azul ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Adicionar E-mail</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form sh-form-azul" action="action.php?ah=malaDiretaListaEmail/adicionarEmail" method="post" novalidate sh-form >
		
			<fieldset>
			
				<h3>Informe os dados do e-mail</h3>
				
				<div class="sh-form-fs">
				
					<?php
						$html = '';
						
						$html .= '<input type="hidden" name="idLista" id="idLista" value="'.$requestParameters['idLista'].'" />';
						
						//NOME
						$html .= \Sh\RendererLibrary::renderFieldBox($contatoDS->getField('nome', false), null, array(
							'placeholder'=>'Nome'
						), array('div'=>array('class'=>'sh-w-1-1')));

						//E-MAIL
						$html .= \Sh\RendererLibrary::renderFieldBox($contatoDS->getField('email', false), null, array(
							'validationType'=>'email',
							'placeholder' => 'E-mail',
							'required'=>'true'
						), array('div'=>array('class'=>'sh-w-1-1')));


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