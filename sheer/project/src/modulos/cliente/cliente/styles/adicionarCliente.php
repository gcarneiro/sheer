<?php
	$clienteDS = \Sh\ModuleFactory::getModuleDataSource('cliente', 'cliente');
	$cliente = $content['cliente/cliente_detalhes'];
	
	$titulo = 'Adicionar Cliente';
	if(isset($_GET['id'])){
		$titulo = 'Editar Cliente';
	}
	
	if ($cliente['total'] > 0){
		$cliente = reset($cliente['results']);
	}
	else{
		//Pegando Estado padrão da variável
		$estadoPadrao = \Sh\Modules\variavel\variavel::getVariavelByAlias('estadoPadrao');
		
		$cliente = array(
			'id'=>null,
			'nome'=>null,
			'tipoPessoa'=>1,
			'razaoSocial'=>null,
			'cpf'=>null,
			'cnpj'=>null,
			'inscricaoMunicipal'=>null,
			'email'=>null,
			'email2'=>null,
			'telefone'=>null,
			'telefone2'=>null,
			'foto' => null,
			'cep'=>null,
			'estado'=> array(
				'id' => $estadoPadrao['valor'],
				'nome' => null			
			),
			'cidade'=> array(
				'id' => null,
				'nome' => null,
			),
			'bairro'=> array(
				'id' => null,
				'nome' => null,
			),
			'endereco'=>null,
			'numero'=>null,
			'complemento'=>null,
		);
	}
?>
<section class="sh-box sh-box-laranja sh-w-1000 sh-margin-x-auto">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1><?php echo $titulo ?></h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form sh-form-laranja" action="action.php?ah=cliente/alterarCliente" method="post" novalidate sh-form sh-form-rh="[sim/cliente][adicionarCliente.adicionarClienteResponse]"   >
		
		
			<fieldset>
				<h3>Informe os dados do cliente</h3>
				<div class="sh-form-fs">
				
				
					
					
					<?php
						$html = '';
						
						if ($cliente['id']!=null){
							$html .= '<input type="hidden" name="id" id="id" required="required" value="'.$cliente['id'].'" />';
						}
						
						//Radio para Trocar Nome / Razão
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('tipoPessoa', false), $cliente['tipoPessoa'], array (
								'required' => true,
								'renderType' => 'radio'
						), array('div'=>array('class'=>'sh-w-1 tipoPessoa')));
						
						
						//Nome
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('nome', false), $cliente['nome'], array (
							'placeholder' => 'Nome',
							'required' => true
						), array('div'=>array('class'=>'sh-w-1')));
						
						//Razão Social
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('razaoSocial', false), $cliente['razaoSocial'], array (
							'placeholder' => 'Razão Social',
							'required' => true
						), array('div'=>array('class'=>'sh-w-1')));
						
						$html .= '<div class="sh-w-1 linhaFisica sh-form-fs-clear" style="display:none">';
							//CPF
							$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('cpf', false), $cliente['cpf'], array(
									'id'=>'clienteCpf',
									'placeholder'=>'CPF',
// 									'validationType'=>'cpf',
									'mask'=>'cpf'
							), array('div'=>array('class'=>'sh-w-1-3 sh-form-field')));
						$html .= '</div>';
							
						
						$html .= '<div class="sh-w-1 linhaJuridica sh-form-fs-clear">';
							//CNPJ
							$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('cnpj', false), $cliente['cnpj'], array(
									'id'=>'clienteCnpj',
									'placeholder'=>'CNPJ',
									'validationType'=>'cnpj',
									'mask'=>'cnpj',
									'required' => true
							), array('div'=>array('class'=>'sh-w-1-3 sh-form-field')));
							
							//Inscrição Municipal
							$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('inscricaoMunicipal', false), $cliente['inscricaoMunicipal'], array(
									'placeholder'=>'Inscrição Municipal',
									'mask'=>'numero'
							), array('div'=>array('class'=>'sh-w-1-3  sh-form-field')));		
							
						$html .= '</div>';
						
						//Email
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('email', false), $cliente['email'], array(
								'placeholder'=>'E-mail',
								'validationType'=>'email'
						), array('div'=>array('class'=>'sh-w-1-2')));

						//Email 2
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('email2', false), $cliente['email2'], array(
								'placeholder'=>'E-mail de Cobrança',
								'validationType'=>'email'
						), array('div'=>array('class'=>'sh-w-1-2')));

						//Telefone
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('telefone', false), $cliente['telefone'], array(
								'placeholder'=>'Telefone',
								'mask'=>'telefone'
						), array('div'=>array('class'=>'sh-w-1-2')));

						//Telefone 2
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('telefone2', false), $cliente['telefone2'], array(
								'placeholder'=>'Telefone Secundário',
								'mask'=>'telefone'
						), array('div'=>array('class'=>'sh-w-1-2')));
						
						//Foto
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('foto', false), $cliente['foto'], array(
								'placeholder'=>'Foto'
						), array('div'=>array('class'=>'sh-w-1-2')));
						
						
						echo $html;
						
					?>
				</div>
			</fieldset>
			
			<fieldset>
				<h3>Endereço</h3>
				<div class="sh-form-fs">		
					<?php 

						$html = '';
						//Estado
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('idEstado', false), $cliente['estado']['id'], array(
								'blankOption'=>'Estado',
								'sh-localidade-role' => 'estado',
								'required' => true
						), array('div'=>array('class'=>'sh-w-1-3')));
						
						//Cidade
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('idCidade', false), $cliente['cidade']['id'], array(
								'blankOption'=>'Cidade',
								'dpFilters' => array( 'idUf' => $cliente['estado']['id'] ),
								'sh-localidade-role' => 'cidade',
								'required' => true
						), array('div'=>array('class'=>'sh-w-1-3')));
						
						//Bairro
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('idBairro', false), $cliente['bairro']['id'], array(
								'blankOption'=>'Bairro',
								'dpFilters' => array( 'idCidade' => $cliente['cidade']['id'] ),
								'sh-localidade-role' => 'bairro'
						), array('div'=>array('class'=>'sh-w-1-3')));
						
						
						//Endereço
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('endereco', false), $cliente['endereco'], array(
								'placeholder'=>'Endereço'
						), array('div'=>array('class'=>'sh-w-3-4')));
						
						//Número
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('numero', false), $cliente['numero'], array(
								'placeholder'=>'Número'
						), array('div'=>array('class'=>'sh-w-1-4')));
						
						//Complemento
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('complemento', false), $cliente['complemento'], array(
								'placeholder'=>'Complemento'
						), array('div'=>array('class'=>'sh-w-3-4')));
						
						//CEP
						$html .= \Sh\RendererLibrary::renderFieldBox($clienteDS->getField('cep', false), $cliente['cep'], array(
								'placeholder'=>'CEP',
								'mask'=>'cep'
						), array('div'=>array('class'=>'sh-w-1-4')));
						
						echo $html;
					
					?>
					
				</div>
			</fieldset>
			
			<div class="sh-btn-holder">
				<button type="submit">Salvar</button>
			</div>
		
		</form>
	
	</div>

</section>