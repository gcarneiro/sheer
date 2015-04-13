<?php
	$listaImportacaoDS = \Sh\ModuleFactory::getModuleDataSource('malaDiretaListaImportacao', 'malaDiretaListaImportacao');
?>
<section class="sh-box sh-box-laranja sh-w-600 sh-margin-x-auto ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Adicionar Vários e-mails</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=malaDiretaListaImportacao/adicionarVariosEmails" method="post" novalidate sh-form >
		
			<fieldset>
			
				<h3>Insira o arquivo com os e-mails</h3>
				
				<div class="sh-form-fs">
					<p style="margin-bottom: 1em;">O arquivo deverá ser enviado em formato ".txt". Contendo um registro por linha. Este registro poderá ter os campos: "Email", "Nome" e "Habilitado"</p>
				
					<?php
						$html = '';
						$html .= '<input type="hidden" name="idLista" id="idLista" value="'.$_GET['idLista'].'" />';
						
						//ARQUIVO
						$html .= \Sh\RendererLibrary::renderFieldBox($listaImportacaoDS->getField('arquivo', false), null, array(
							'required'=>true
						), array('div'=>array('class'=>'sh-w-1-2')));
						
						//ATIVAR
						$html .= \Sh\RendererLibrary::renderFieldBox($listaImportacaoDS->getField('ativar', false), 2, array(
							'label'=>'Ativar envio para e-mails já contido na lista com envio desabilitado?',
							'required'=>true
						), array('div'=>array('class'=>'sh-w-1-2')));
	
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