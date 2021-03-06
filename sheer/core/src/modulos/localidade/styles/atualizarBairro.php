<?php
	$bairroDS = \Sh\ModuleFactory::getModuleDataSource('localidade', 'bairro');
	$bairro = reset($content['localidade/bairro_detalhes']['results']);
?>

<section class="sh-box sh-box-laranja sh-margin-x-auto sh-w-500 ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Atualizar Bairro</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=localidade/bairro_update" method="post" novalidate sh-form sh-form-responseHandler="[sheer/modules/localidade][bairros.gerenciar.adicionarResponseHandler]" >
		
			<fieldset class="sh-grid-box">
			
				<h3>Informe os dados do bairro</h3>
				
				<div class="sh-form-fs">
					<?php
						$html = '';
						$html .= '<input type="hidden" name="id" required value="'.$bairro['id'].'" />';
						//Estado
						$html .= \Sh\RendererLibrary::renderFieldBox($bairroDS->getField('idUf', false), $bairro['idUf'], array(
							'blankOption' => 'Selecione',
							'sh-localidade-role' => 'estado'
						), array(
							'div' => array('class'=>'sh-w-1')
						));
	
						//Cidade
						$html .= \Sh\RendererLibrary::renderFieldBox($bairroDS->getField('idCidade', false), $bairro['idCidade'], array(
							'blankOption' => 'Selecione',
							'sh-localidade-role' => 'cidade',
							'dpFilters' => array('idUf'=>$bairro['idUf'])
						), array(
								'div' => array('class'=>'sh-w-1')
						));
						
						//Bairro
						$html .= \Sh\RendererLibrary::renderFieldBox($bairroDS->getField('nome', false), $bairro['nome'], array(
							'placeholder'=>'Valor',
							'uppercase'=>true,
							'required'=>'true'
						), array('div'=>array('class'=>'sh-w-1')));
						
						echo $html;
					
					?>
					
					<div class="sh-btn-holder">
						<button type="submit" class="sh-btn-laranja">Atualizar</button>
					</div>
				</div>
				
			</fieldset>
		
		</form>
	
	</div>

</section>