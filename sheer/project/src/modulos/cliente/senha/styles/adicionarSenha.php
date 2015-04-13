<?php
	$clienteSenhaDS = \Sh\ModuleFactory::getModuleDataSource('clienteSenha', 'clienteSenha');
	$clienteSenha = reset($content['clienteSenha/clienteSenhaPorId']['results']);
	$acessoCliente = $clienteSenha['acesso'];
	if(!$acessoCliente){
		$acessoCliente = $clienteSenhaDS->getField('acesso', false)->getDefaultValue();
	}
?>
<section class="sim-box sim-box-action sh-margin-x-auto ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Adicionar Senha</h1>
	</header>
	
	<div class="sim-box-content">
	
		<form class="pure-form sim-form" action="action.php?ah=clienteSenha/adicionarAtualizarSenha" method="post" novalidate sh-form >
		
			<div class="sh-w-600 sim-form-fieldset sh-margin-x-auto">
				<fieldset class="sh-grid-box">
				
					<h3>Informe os dados da senha</h3>
					
					<?php
						$html = '';
						//SE EU ESTIVER NO PROCESSO DE EDITAR A SENHA DEVO PASSAR O ID DO REGISTRO QUE VOU EDITAR
						$observacoesCheck = 'style="display:block"';
						$observacaoDiv = 'display:none;';
						if($clienteSenha){
							if($clienteSenha['observacao']){
								$observacoesCheck = 'style="display:none;"';
								$observacaoDiv = null;
							}
							$html .= '<input type="hidden" name="id" id="id" required="required" value="'.$clienteSenha['id'].'" />';
						}
						//SE EU FOR ADICIONAR PASSO O ID DO CLIENTE
						else{
							$html .= '<input type="hidden" name="idCliente" id="idCliente" required="required" value="'.$_GET['id'].'" />';
						}
						
						//Serviço
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteSenhaDS->getField('servico', false), $clienteSenha['servico'], array(
									'placeholder'=>'Serviço'
						), array('div'=>array('class'=>'sh-w-1')));
						
						//ACESSO
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteSenhaDS->getField('acesso', false), $acessoCliente, array(
									'placeholder'=>'Acesso'
						), array('div'=>array('class'=>'sh-w-1')));
						
						//Login
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteSenhaDS->getField('login', false), $clienteSenha['login'], array (
							'placeholder' => 'Login',
							'required' => true
						), array('div'=>array('class'=>'sh-w-1-2')));
						
						//Senha
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteSenhaDS->getField('senha', false), $clienteSenha['senha'], array (
							'placeholder' => 'Senha',
							'required' => true
						), array('div'=>array('class'=>'sh-w-1-2')));
						
						//Checkbox para exibir bloco de observações
						$html.='<div class="sh-w-1-2" '.$observacoesCheck.'>';
							$html.='<label for="checkObservacoes" class="sh-checkbox" id="showObservacoes"><input id="checkObservacoes" type="checkbox" /> Adicionar Observações</label>';
						$html.='</div>';
						
						//Observação
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteSenhaDS->getField('observacao', false), $clienteSenha['observacao'], null,
						array('div'=>array('class'=>'sh-w-1','style'=>$observacaoDiv, 'id'=>'observacoesDiv')));

						echo $html;
					
					?>
					
				</fieldset>
				
			</div>
			
			<div class="sim-btn-1 sim-btn-margin-05">
				<button type="submit">Enviar</button>
			</div>
		
		</form>
	
	</div>

</section>
