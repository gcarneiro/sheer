<?php
	$pessoa = reset($content['cliente/cliente_detalhes']['results']);
	$clientePessoa = $content['cliente/pessoaPorCliente'];
	$fatura = $content['fatura/faturasAbertasPorCliente'];
	$cobranca = $content['cobranca/cobrancaPorCliente'];
	$nome = \Sh\Library::getNomeFromNomeCompleto($pessoa['nome']);
?>

<div class="sim-page-250-x dashboard">

	<div>
		<!-- Seção com o bloco da pessoa -->
		<section>
			<div>
				<!-- NOME, IDADE E PROFISSAO DA PESSOA -->
				<?php
						$html ='';
						$html .='<img alt="'.$pessoa['nome'].'" title="'.$pessoa['nome'].'" src="'.$pessoa['avatar'][90].'">';
						$html .='<p><strong>'.$nome.'</strong></p>';
						$html .='<p>'.$pessoa['tipoPessoa_lookup'].'</p>';
						
						echo $html;
					 ?>
			</div>
			<div>
				<!-- INFORMAÇÕES -->
				<section class="">
					<header>
						<p>Informações</p>
						<a href="?p=cliente/adicionar&id=<?php echo $pessoa['id'] ?>" data-icon="s"></a>
					</header>
					
					<div>
						<?php 
							$html = '';
							$html .= '<p><strong>Código:</strong> '.$pessoa['codigo'].'</p>';
							if ( $pessoa['cpf'] ){
								$html .= '<p><strong>CPF:</strong> '.$pessoa['cpf'].'</p>';	
							}
							else if ( $pessoa['cnpj'] ){
								$html .= '<p><strong>CNPJ:</strong> '.$pessoa['cnpj'].'</p>';
								
								if ( $pessoa['inscricaoMunicipal'] ){
									$html .= '<p><strong>Inscrição Mun.:</strong> '.$pessoa['inscricaoMunicipal'].'</p>';
								}
								if($pessoa['razaoSocial']){
									$html .= '<p><strong>Razão:</strong> '.$pessoa['razaoSocial'].'</p>';
								}
							}
							if( $pessoa['email'] ){
								$html .= '<p><strong>E-mail:</strong> '.$pessoa['email'].'</p>';
							}
							
							if( $pessoa['email2'] ){
								$html .= '<p><strong>E-mail de cobrança:</strong> '.$pessoa['email2'].'</p>';
							}
							
							if( $pessoa['telefone'] ){
								$html .= '<p><strong>Telefone:</strong> '.$pessoa['telefone'].'</p>';
							}
							
							if( $pessoa['telefone2'] ){
								$html .= '<p><strong>Telefone 2:</strong> '.$pessoa['telefone2'].'</p>';
							}
							
							echo $html;
						?>
					</div>
					
					<header>
						<p>Endereço</p>
						<a href="?p=cliente/adicionar&id=<?php echo $pessoa['id'] ?>" data-icon="s"></a>
					</header>
					
					<div>
						<?php 
							$html = '';
							
							if ( $pessoa['endereco'] ) {
								if( $pessoa['estado'] ){
									$html .= '<p><strong>Estado:</strong> '.$pessoa['estado']['nome'].'</p>';
								}
								if( $pessoa['cidade'] ) {
									$html.='<p><strong>Cidade: </strong>'.$pessoa['cidade']['nome'].'</p>';
								}
								if( $pessoa['bairro'] ) {
									$html.='<p><strong>Bairro:</strong> '.$pessoa['bairro']['nome'].'</p>';
								}
								
								$html.='<p><strong>Endereço: </strong>'.$pessoa['endereco'].'</p>';
								
								if( $pessoa['numero'] ){
									$html.='<p><strong>Nº: </strong>'.$pessoa['numero'].'</p>';
								}
									
								if( $pessoa['cep'] ) {
									$html.='<p><strong>CEP:</strong> '.$pessoa['cep'].'</p>';
								}
							}
							
							echo $html;
						?>
					</div>
				</section>
			</div>
		</section>
		
		<section>
			<div>
				<p>Senhas</p>
			</div>
			<div>
				<?php
					$html = '';
					if( $pessoa['senha'] ) {
						foreach ($pessoa['senha'] as $senha){
							
							$html.='<section class="dadosPessoa fechado">';
								$html.='<header>';
									$html .= '<p>'.$senha['servico'].'</p>';
									$html.='<a href="#" sh-component="navigation" sh-component-target="navegacaoCliente" data-id="'.$senha['id'].'" data-icon="s" ></a>';
								$html.='</header>';
								$html.='<div>';
									if($senha['acesso']){
										
										$html .= '<p><strong>Acesso:</strong><a href="'.$senha['acesso'].'" target="_blank"> '.$senha['acesso'].'</a></p>';
									}
									$html .= '<p><strong>Login:</strong> '.$senha['login'].'</p>';
									
									$html .= '<p class="colunaSenhas">';
										$html .= '<span class="verSenha"><strong>Senha:</strong> Ver Senha</span>';
										$html .= '<span class="senha" style="display:none"><strong>Senha:</strong> '.$senha['senha'].'</span>';
									$html .= '</p>';
										
									//se o registro tiver observações imprimo sim
									if($senha['observacao']){
										$html .= '<p class="observacoesLabel" ><strong>Observacções: </strong>Sim';
										$html .= '<input type="hidden" class="conteudo" value="'.$senha['observacao'].'" >';
										$html .= '<input type="hidden" class="titulo" value="Observações" ></p>';
									}
									else {
										$html .= '<p><strong>Observações:</strong> Não</p>';
									}
									
								$html.='</div>';
							$html.='</section>';
						}
					}
					echo $html;
				 ?>
				 <div class="dashboard-btn-mais">
					<a data-icon="k" href="renderer.php?rd=clienteSenha/adicionarSenha&id=<?php echo $_GET['id']?>" sh-component="overlayLink"></a>
				</div>
			</div>
		</section>
		
	</div>
	
	<div style="display:block;">
		
		<section class="sim-box sim-box-lista">
		
			<header>
				<div><span data-icon="a"></span></div>
		
				<h1>Faturas em Aberto</h1>
		
				<a href="#" sh-component="navigation" sh-component-target="navegacaoTabelaFatura" data-id="'.$detalhes['id'].'" data-icon="d" ></a>
					
			</header>
			
			<?php 
				$classeVazio = '';
				if( $fatura['total'] == 0 ) {
					$classeVazio = 'sim-lista-empty';
				}
			?>
			<div class="sim-box-content">
			
				<section class="sim-lista-gerenciar <?php echo $classeVazio; ?>">
				
					<table id="listaFaturas" class="sh-table sh-table-admin">
					
						<thead>
							<tr>
								<th><input type="checkbox" /></th>
								<th>Código</th>
								<th class="data-center">Competência</th>
								<th class="data-center">Vencimento</th>
								<th class="data-center" >Valor</th>
								<th class="data-center">Boleto</th>
								<th class="sim-element-action"></th>
							</tr>
						</thead>
						
						<tbody>
						
							<?php 
								$html = '';
								if( $fatura['results'] ) {
									$total = 0.0;
									//FIXME CONTIUNA DAQUI. FALTA FZER O ESTILO DE INSERIR COBRANÇA
									foreach ( $fatura['results'] as $detalhes ) {
										$html .= '<tr data-id="'.$detalhes['id'].'" data-content>';
											$html .= '<td><input type="checkbox" sh-check="" name="itemId[]" value="'.$detalhes['id'].'" /></td>';
											$html .= '<td>'.$detalhes['numero'].'</td>';	
											$html .= '<td class="data-center">'.$detalhes['competencia']['monthName'].' / '.$detalhes['competencia']['year'].'</td>';
											$html .= '<td class="data-center">'.$detalhes['vencimento']['date'].'</td>';
											$html .= '<td class="data-center">R$ '.$detalhes['valor'].'</td>';
											$total += (float)  \Sh\FieldDinheiro::formatInputDataToPrimitive($detalhes['valor']);
											//Verifica se possuo boleto, se tiver exibe função Ver
											if($detalhes['htmlBoleto']){
												$html .= '<td class="data-center"> <a href="'.$detalhes['htmlBoleto'].'" target="_blank">Ver</a> </td>';
											}
											else{
												$html .= '<td class="data-center"> -- </td>';
											}
											
											$html .= '<td><a href="#" sh-component="navigation" sh-component-target="navegacaoFatura" data-id="'.$detalhes['id'].'" data-icon="d" ></a></td>';
										$html .= '</tr>';
									}
									echo $html;
								}
							?>
							
						</tbody>
					
					</table>
					<?php 
						
						$html = '';	
					
						if( $fatura['results'] ) {
							$html .= '<p class="data-right">Total: <strong>R$ '.\Sh\FieldDinheiro::formatPrimitiveDataToSheer($total).'</strong></p>';
						}
					
						echo $html;
						
					?>
					<div class="empty-info">
						<p data-icon="g"></p>
						<p>Nenhum resultado encontrado</p>
					</div>
					
				</section>
		
			</div>
			
		</section>
		
		<!-- COBRANÇAS -->
		<section class="sim-box sim-box-lista">
			<header>
				<div><span data-icon="a"></span></div>
				
				<h1>Cobranças</h1>
				
				<a  href="renderer.php?rd=cobranca/adicionarCobranca&idCliente=<?php echo $pessoa['id']?>" sh-component="overlayLink" data-icon="k" class="adicionarItem"></a>
				<a href="#" sh-component="navigation" sh-component-target="navegacaoTabela" data-icon="d" ></a>
				
			</header>
			
			<?php 
				$classeVazio = '';
				if( $cobranca['total'] == 0) {
					$classeVazio = 'sim-lista-empty';
				}
			?>
			<div class="sim-box-content">
		
				<section class="sim-lista-gerenciar <?php echo $classeVazio; ?>">
				
					<table id="listaCobrancaAbertas" class="sh-table sh-table-admin" >
					
						<thead>
							<tr>
								<th><input type="checkbox" /></th>
								<th>Serviço</th>
								<th class="data-center">Competência</th>
								<th class="data-center" >Valor</th>
								<th class="sim-element-action"></th>
							</tr>
						</thead>
						
						<tbody>
						
							<?php 
								$html = '';
								if( $cobranca['results'] ) {
									$total = (float) 0.0;
									$contador = 0;
									//FIXME CONTIUNA DAQUI. FALTA FZER O ESTILO DE INSERIR COBRANÇA
									foreach ( $cobranca['results'] as $detalhes ) {
										if($contador == 5){
											continue;
										}
										
										$contador++;
										$html .= '<tr data-id="'.$detalhes['id'].'" data-content>';
											$html .= '<td><input type="checkbox" sh-check="" name="itemId[]" value="'.$detalhes['id'].'" /></td>';
											$html .= '<td>'.$detalhes['servico']['nome'].'</td>';
											$html .= '<td class="data-center">'.$detalhes['competencia']['monthName'].' / '.$detalhes['competencia']['year'].'</td>';
											$html .= '<td class="data-center">R$ '.$detalhes['valor'].'</td>';
											$total += (float)  \Sh\FieldDinheiro::formatInputDataToPrimitive($detalhes['valor']);
											$html .= '<td><a href="#" sh-component="navigation" sh-component-target="navegacaoCobranca" data-id="'.$detalhes['id'].'" data-icon="d" ></a></td>';
										$html .= '</tr>';
									}
									
									echo $html;
								}
							?>
							
						</tbody>
					
					</table>
					
					<div class="empty-info">
						<p data-icon="g"></p>
						<p>Nenhum resultado encontrado</p>
					</div>
					
				</section>
	
			</div>
			
		</section>
		
		<!-- COBRANÇAS RECORRENTES -->
		<section class="sim-box sim-box-lista">
		
			<header>
				<div><span data-icon="a"></span></div>
				
				<h1>Cobranças Recorrentes</h1>
				
				<a  href="renderer.php?rd=cobrancaRecorrente/adicionarRecorrente&id=<?php echo $pessoa['id']?>" sh-component="overlayLink" data-icon="k" class="adicionarItem"></a>
				
			</header>
			<?php 
				$classeVazio = '';
				if( $pessoa['cobrancaRecorrente'] == null ) {
					$classeVazio = 'sim-lista-empty';
				}
			?>
			<div class="sim-box-content">
			
				<section class="sim-lista-gerenciar <?php echo $classeVazio; ?>">
				
					<table class="sh-table sh-table-admin">
					
						<thead>
							<tr>
								<th><input type="checkbox" /></th>
								<th>Serviço</th>
								<th class="data-center">Valor</th>
								<th class="data-center" >Recorrência</th>
								<th class="data-center" >Data de Início</th>
								<th class="data-center" >Data de Término</th>
								<th class="data-center" >Descrição</th>
								<th class="sim-element-action"></th>
							</tr>
						</thead>
						
						<tbody>
						
							<?php 
								$html = '';
								if( $pessoa['cobrancaRecorrente'] ) {
									$contador = 0;
									
									foreach ( $pessoa['cobrancaRecorrente'] as $idCobrancaRecorrente=>$cobrancaRecorrente ) {
										
										if($cobrancaRecorrente['ativo']==2){									
											$html .= '<tr class="cobrancaRecorrente-cancelada" data-id="'.$cobrancaRecorrente['id'].'" data-content>';
										}
										else{
											$html .= '<tr data-id="'.$cobrancaRecorrente['id'].'" data-content>';
										}
										
										
										$html .= '<td><input type="checkbox" name="itemId[]" value="'.$cobrancaRecorrente['id'].'" /></td>';
										//SERVIÇO
										if($cobrancaRecorrente['servico']['descricao']){
											$html .= '<td class="observacoesLabel">'.$cobrancaRecorrente['servico']['nome'];
											$html .= '<input type="hidden" class="conteudo" value="'.$cobrancaRecorrente['servico']['descricao'].'" />';
											$html .= '<input type="hidden" class="titulo" value="Descrição do Serviço" /></td>';
										}
										else {
											$html .= '<td>'.$cobrancaRecorrente['servico']['nome'].'</td>';
										}
										//VALOR
										$html .= '<td class="data-center">R$ '.$cobrancaRecorrente['valor'].'</td>';
										//RECORRENCIA
										$html .= '<td class="data-center">'.$cobrancaRecorrente['recorrencia_lookup'].'</td>';
										//DATAINICIO
										$html .= '<td class="data-center">'.$cobrancaRecorrente['dataInicio']['date'].'</td>';
										//DATAFIM
										$html .= '<td class="data-center">'.$cobrancaRecorrente['dataFim']['date'].'</td>';
										
										//se o registro tiver observações imprimo sim
										if($cobrancaRecorrente['descricao']){
											$html .= '<td class="data-center observacoesLabel" >Sim';
											$html .= '<input type="hidden" class="conteudo" value="'.$cobrancaRecorrente['descricao'].'" />';
											$html .= '<input type="hidden" class="titulo" value="Descrição" /></td>';
										}
										else {
											$html .= '<td class="data-center " >Não</td>';
										}
										$html .= '<td><a href="#" sh-component="navigation" sh-component-target="navegacaoRecorrente" data-id="'.$cobrancaRecorrente['id'].'" data-icon="d" ></a></td>';
										$html .= '</tr>';
		
									}
									echo $html;
								}
							?>
							
						</tbody>
					</table>
					<div class="empty-info">
						<p data-icon="g"></p>
						<p>Nenhum resultado encontrado</p>
					</div>
					
				</section>
			
			</div>
			<?php 
				$classeVazio = '';
				if( $pessoa['cobrancaRecorrente'] == null ) {
					$classeVazio = 'sim-lista-empty';
				}
			?>
			
		</section>
		
		<section class="sim-box sim-box-lista">
	
			<header>
				<div><span data-icon="a"></span></div>
				
				<h1>Lista de pessoas</h1>
				
				<a  href="renderer.php?rd=pessoa/adicionarEditarPessoa&id=<?php echo $_GET['id']?>" sh-component="overlayLink" data-icon="k" class="adicionarItem"></a>
				
			</header>
			
			<?php 
				$classeVazio = '';
				if( $clientePessoa['total'] == 0 ) {
					$classeVazio = 'sim-lista-empty';
				}
			?>
			<div class="sim-box-content">
			
				<section class="sim-lista-gerenciar <?php echo $classeVazio; ?>">
				
					<table class="sh-table sh-table-admin">
					
						<thead>
							<tr>
								<th><input type="checkbox" /></th>
								<th>Nome</th>
								<th>Telefone</th>
								<th>E-mail</th>
								<th class="sim-element-action"></th>
							</tr>
						</thead>
						
						<tbody>
						
							<?php 
								$html = '';
								if( $clientePessoa['results'] ) {
									
									foreach ( $clientePessoa['results'] as $id=>$clientePessoa ) {
										$html .= '<tr data-id="'.$clientePessoa['id'].'" data-content>';
											$html .= '<td><input type="checkbox" name="itemId[]" value="'.$clientePessoa['id'].'" /></td>';
											$html .= '<td>'.$clientePessoa['pessoa']['nome'].'</td>';
											$html .= '<td>'.$clientePessoa['pessoa']['telefone'].'</td>';
											$html .= '<td>'.$clientePessoa['pessoa']['email'].'</td>';
											
											$html .= '<td><a href="#" sh-component="navigation" sh-component-target="navegacaoPessoa" data-id="'.$clientePessoa['id'].'" data-icon="d" ></a></td>';
										$html .= '</tr>';
		
									}
									echo $html;
								}
							?>
							
						</tbody>
					
					</table>
					
					<div class="empty-info">
						<p data-icon="g"></p>
						<p>Nenhum resultado encontrado</p>
					</div>
				
				</section>
		
		</div>
		
	</section>
	
	</div>
</div>


<!-- COBRANÇA RECORRENTE -->
<template id="navegacaoRecorrente">
	<nav class="sim-navegacaoItem" data-id="{id}">
		<header></header>
		<div>
			<ul>
				<li><a href="renderer.php?rd=cobrancaRecorrente/adicionarRecorrente&id={id}" sh-component="overlayLink">Editar</a></li>
				<li><a href="renderer.php?rd=cobrancaRecorrente/cancelarRecorrente&id={id}" sh-component="overlayLink">Cancelar</a></li>
<!-- 				<li><a href="action.php?ah=cobrancaRecorrente/cobrancaRecorrente_delete&id={id}" sh-component="action">Excluir</a></li> -->
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>

<!-- FATURA -->
<template id="navegacaoFatura">
	<nav class="sim-navegacaoItem" data-id="{id}">
		<header></header>
		<div>
			<ul>
				<li><a href="renderer.php?rd=fatura/pagar&id={id}" sh-component="overlayLink">Marcar Pagamento</a></li>
				<li><a href="renderer.php?rd=fatura/notaFiscal&id={id}" sh-component="overlayLink">Cadastrar Nota Fiscal</a></li>
				<li><a href="renderer.php?rd=fatura/verCobrancas&id={id}" sh-component="overlayLink">Ver Cobranças</a></li>
				<li><a href="renderer.php?rd=fatura/alterar&id={id}" sh-component="overlayLink">Alterar Vencimento</a></li>
				<li><a href="renderer.php?rd=fatura/cancelar&id={id}" sh-component="overlayLink">Cancelar</a></li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>

<template id="navegacaoTabelaFatura">
	<nav class="sim-navegacaoItem" data-id="{id}">
		<header></header>
		<div>
			<ul>
				<li><a href="action.php?ah=fatura/gerarBoleto" class="fatura" sh-component="actionMultiplo" sh-comp-content="n" sh-comp-contentNode="listaFaturas"
				sh-component-responseHandler="[sim/faturamento][fatura.verificarCliente.gerarBoletoResponse]">Gerar Boleto</a></li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>

<!-- COBRANÇA -->
<template id="navegacaoCobranca">
	<nav class="sim-navegacaoItem" data-id="{id}">
		<header></header>
		<div>
			<ul>
				<li><a href="renderer.php?rd=cobranca/adicionarCobranca&id={id}" sh-component="overlayLink">Editar</a></li>
				<li><a href="renderer.php?rd=cobranca/cancelarCobranca&id={id}" sh-component="overlayLink">Cancelar</a></li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>

<template id="navegacaoTabela">
	<nav class="sim-navegacaoItem" data-id="{id}">
		<header></header>
		<div>
			<ul>
				<li><a href="renderer.php?rd=fatura/adicionar" class="fatura" sh-component="overlayLink" sh-comp-content="n" sh-comp-contentNode="listaCobrancaAbertas">Gerar Fatura</a></li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>

<!-- LISTA DE SENHA -->
<template id="navegacaoCliente">
	<nav class="sim-navegacaoItem" data-id="{id}">
		<header></header>
		<div>
			<ul>
				<li><a href="renderer.php?rd=clienteSenha/adicionarSenha&id={id}" sh-component="overlayLink">Editar</a></li>
				<li><a href="action.php?ah=clienteSenha/clienteSenha_delete&id={id}" sh-component="action">Apagar</a></li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>

<!-- PESSOA -->
<template id="navegacaoPessoa">
	<nav class="sim-navegacaoItem" data-id="{id}">
		<header></header>
		<div>
			<ul>
				<li><a href="renderer.php?rd=pessoa/verDetalhes&id={id}" sh-component="overlayLink">Ver Detalhes</a></li>
				<li><a href="renderer.php?rd=pessoa/adicionarEditarPessoa&id={id}" sh-component="overlayLink">Editar</a></li>
				<li><a href="action.php?ah=cliente/clientePessoa_delete&id={id}" sh-component="action">Remover</a></li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>

<script>
	require(['sim/cliente'], function (cliente) {
		cliente.dashboard.init();
	});
</script>