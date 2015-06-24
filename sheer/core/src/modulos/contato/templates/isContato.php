<?php 
	$contatoDS = \Sh\ModuleFactory::getModuleDataSource('contato', 'contato');
?>

<section class="sh-box sh-box-agua">
		
	<header>
		<div><span data-icon="e"></span></div>
		
		<h1>Filtre seus resultados instantaneamente</h1>
	</header>

	<div class="sh-box-content">
		
		<form class="sh-form" action="renderer.php?rd=contato/contato_gerenciar" method="post" sh-is sh-is-holder="#listaContatos" autocomplete="off">
			<fieldset>
			
				<div class="sh-form-field sh-w-250">
					<label for="isNome">Nome</label>
					<input type="text" id="isNome" name="nome" value="" placeholder="Nome" />  
				</div>
				
				<div class="sh-form-field sh-w-300">
					<label for="isEmail">Email</label>
					<input type="text" id="isEmail" name="email" value="" placeholder="Email" />  
				</div>
				
				<?php 
					$html = '';
					$html .= \Sh\RendererLibrary::renderFieldBox($contatoDS->getField('idDestino', false), null, array(
						'id'=>'isDestino',
						'blankOption' => 'Todos',
						'required' => false
					), array('div'=>array('class'=>'sh-form-field')));

					$html .= \Sh\RendererLibrary::renderFieldBox($contatoDS->getField('arquivado', false), 2, array(
						'id'=>'isArquivado',
						'blankOption' => 'Todos',
						'required' => false
					), array('div'=>array('class'=>'sh-form-field')));

					echo $html;
				?>

			</fieldset>
			
		</form>
		
	</div>
	
</section>