<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>{{page.title}}</title>
		{{page.baseUrl}}
		
		{{template.includes/inclusaoScripts.html}}
		
		{{page.scripts}}
		{{page.styles}}
		
	</head>
	
	<body class="sheer-main-bar">
	
		<section class="sheer-main-bar">
		
			<div class="left data-left">
				<a href="#" class="sheer-main-navigation-trigger"><span data-icon="B"></span></a>
			</div>
			
			<div class="sheer-logotipo"><a><img src="resources/images/sheer_logo_h40.png" /></a></div>
			
			<div class="right data-right">
			</div>
			
		</section>
		
		<div id="navegadorSubPages" class="sh-btn-holder" style="margin-top: 1em;">
			<a href="#" data-subpage="formularios" class="sh-btn-verde">Formulários</a>	
			<a href="#" data-subpage="boxes" class="sh-btn-verde">Boxes e Botões</a>	
			<a href="#" data-subpage="tabelas" class="sh-btn-verde">Tabelas</a>	
			<a href="#" data-subpage="listaAvatar" class="sh-btn-verde">Lista Avatar</a>	
			<a href="#" data-subpage="avatares" class="sh-btn-verde">Avatares e Tabs</a>	
			<a href="#" data-subpage="grid" class="sh-btn-verde">Posicionamento [GRIDS]</a>	
			<a href="#" data-subpage="notify" class="sh-btn-verde">Notificações</a>	
			<a href="#" data-subpage="cores" class="sh-btn-verde">Cores disponíveis</a>	
			<a href="#" data-subpage="icones" class="sh-btn-verde">Ícones</a>	
		</div>
		
		<div id="formularios" class="subpage">
		
			<section class="sh-box">
				<div class="sh-box-content">
					<h2>Formulários</h2>
				
					<p>Este bloco irá mostrar como funcionam os formulários na interface do Sheer.</p>
					<p>Os itens contemplados aqui são:</p>
					<ul>
						<li>Elementos simples</li>
						<li>Máscarando campos</li>
						<li>DatePickers e InputCases</li>
						<li>Validação</li>
						<li>Resposta padrão de validação</li>
					</ul>
					
					<p>Exemplos de inputs fora de fieldset padrão</p>
					<form class="sh-form">
					
						<fieldset>
							
							<div class="sh-w-1 data-center">
								<div class="sh-form-field sh-w-1 data-left">
									<label for="login">Login</label>
									<input type="text" id="login" name="login" placeholder="Login" required />
								</div>
							</div>
							
							<div class="sh-w-1 data-center">
								<div class="sh-form-field sh-w-1 data-left">
									<label for="password">Senha</label>
									<input type="password" id="password" name="password" placeholder="Senha" required />
								</div>
							</div>
						
						</fieldset>
						
					</form>
					
				</div>
			</section>
		
			<section class="sh-box sh-box-laranja">
		
				<header>
					<div><span data-icon="k"></span></div>
					<h1>Formulários</h1>
				</header>
				
				<div class="sh-box-content">
					
					<form class="sh-form sh-form-laranja">
						
						<fieldset>
							<h3>Campos <small> - inputs, radios, checkbox, textarea e richtext</small></h3>
							
							<div class="sh-form-fs">
							
								<div class="sh-w-1-2">
									<label>Nome</label>
									<input type="text" placeholder="Nome"  />
								</div>
								<div class="sh-w-1-2">
									<label>Endereço</label>
									<input type="text" placeholder="Endereço"  />
								</div>
								
								<div class="sh-w-1-3">
									<label>Escolaridade</label>
									<select>
										<option>Selecione</option>
										<option>Analfabeto</option>
										<option>Fundamental</option>
										<option>Médio</option>
										<option>Graduado</option>
										<option>Pós Graduado</option>
										<option>Mestre</option>
									</select>
								</div>
								<div class="sh-w-1-3">
									<label>Sexo</label>
									<label class="sh-radio">
										<input type="checkbox" /> Masculino
									</label>
									<label class="sh-radio">
										<input type="checkbox" /> Feminino
									</label>
								</div>
								<div class="sh-w-1-3">
									<label>Sexo</label>
									<label class="sh-radio">
										<input type="radio" /> Masculino
									</label>
									<label class="sh-radio">
										<input type="radio" /> Feminino
									</label>
									<label class="sh-radio">
										<input type="radio" /> Feminino
									</label>
									<label class="sh-radio">
										<input type="radio" /> Feminino
									</label>
								</div>
								
								<div class="sh-w-1-2 sh-input-file">
									<label for="arquivo" class=" show-labels">Arquivo</label>
									<div>
										<span data-input-trigger="" data-icon="f"></span>
										<div>
											<a href="dfd.php?i=9BE53BDB-F512-4A53-98FE-F4F615290844">14.07.14</a>
											<label>
												<input type="checkbox" data-op="remove" value="1" name="arquivo_remove" id="arquivo_remove"> 
												Remover arquivo
											</label>
										</div>
										<input type="file" name="arquivo" id="arquivo">
										<input type="hidden" id="arquivo_current">
									</div>
								</div>
								
								<div class="sh-w-1">
									<label>Mensagem</label>
									<textarea></textarea>
								</div>
								<div class="sh-w-1">
									<label>Mensagem</label>
									<textarea richtext="simple"></textarea>
								</div>
								
							</div>
							
							<div class="sh-btn-holder">
								<button class="sh-btn-laranja">Enviar</button>
							</div>
						
						</fieldset>
						
					</form>
				
				</div>
					
			</section>
				
			<section class="sh-box sh-box-amarelo">
		
				<header>
					<h1>Máscaras de campos</h1>
				</header>
				
				<div class="sh-box-content">
					
					<form class="sh-form sh-form-amarelo">
						
						<fieldset>
							<h3>Máscaras<small> - exibindo todas as máscaras disponíveis pelo Sheer</small></h3>
							
							<div class="sh-form-fs">
							
								<div class="sh-w-1-5">
									<label>Número</label>
									<input type="text" placeholder="Número" mask="numero" value="15000"  />
									<small>mask="numero"/ mask="number"</small>
								</div>
								<div class="sh-w-1-5">
									<label>Número com separação</label>
									<input type="text" placeholder="Número com separação" mask="numero-separado" value="15/000-455.541"  />
									<small>mask="numero-separado"</small>
								</div>
								<div class="sh-w-1-5">
									<label>Inteiro</label>
									<input type="text" placeholder="Inteiro" mask="inteiro" value="15000"  />
									<small>mask="inteiro"/ mask="integer"</small>
								</div>
								<div class="sh-w-1-5">
									<label>Decimal/Dinheiro</label>
									<input type="text" placeholder="Decimal/Dinheiro" mask="decimal" value="15000,25000"  />
									<small>mask="decimal" / mask="dinheiro" / mask="money"</small>
								</div>
								<div class="sh-w-1-5">
									<label>Float</label>
									<input type="text" placeholder="Float" mask="float" value="15000,25000"  />
									<small>mask="float"</small>
								</div>
	
								<div class="sh-w-1-4">
									<label>CPF</label>
									<input type="text" placeholder="CPF" mask="cpf"  />
								</div>
								<div class="sh-w-1-4">
									<label>CNPJ</label>
									<input type="text" placeholder="CNPJ" mask="cnpj"  />
								</div>
								<div class="sh-w-1-4">
									<label>CEP</label>
									<input type="text" placeholder="CEP" mask="cep"  />
								</div>
								<div class="sh-w-1-4">
									<label>Telefone</label>
									<input type="text" placeholder="Telefone" mask="telefone"  />
								</div>
								
								<div class="sh-w-1-4">
									<label>Data</label>
									<input type="text" placeholder="Data" mask="date"  />
								</div>
								<div class="sh-w-1-4">
									<label>Data e hora</label>
									<input type="text" placeholder="Data e hora" mask="datetime"  />
								</div>
								<div class="sh-w-1-4">
									<label>Hora</label>
									<input type="text" placeholder="Hora" mask="time"  />
								</div>
								
							</div>
							
							<div class="sh-btn-holder">
								<button class="sh-btn-laranja">Enviar</button>
							</div>
						
						</fieldset>
						
					</form>
				</div>
					
			</section>
				
			<section class="sh-box sh-box-agua">
		
				<header>
					<h1>DatePickers e InputCases</h1>
				</header>
				
				<div class="sh-box-content">
					
					<form class="sh-form sh-form-agua">
						
						<fieldset>
							<h3>DatePicker<small> - exibindo modelos de startDate, endDate, firstDay, lastDay, year</small></h3>
							
							<div class="sh-form-fs">
							
								<div class="sh-w-1-4">
									<label>startDate</label>
									<input type="text" placeholder="startDate" mask="date" datePicker datePicker-startDate="01/07/2014"  />
									<small>[datePicker] / datePicker-startDate="01/07/2014"</small>
								</div>
								<div class="sh-w-1-4">
									<label>endDate</label>
									<input type="text" placeholder="endDate" mask="date" datePicker datePicker-endDate="01/09/2014" />
									<small>[datePicker] / datePicker-endDate="01/09/2014"</small>
								</div>
								<div class="sh-w-1-4">
									<label>startDate e endDate</label>
									<input type="text" placeholder="startDate e endDate" mask="date" datePicker datePicker-startDate="04/07/2014" datePicker-endDate="25/07/2014"  />
									<small>[datePicker] / datePicker-startDate="04/07/2014" / datePicker-endDate="25/07/2014"</small>
								</div>
								
								<div class="sh-form-fs-group sh-w-1">
									<div class="sh-w-1-4">
										<label>Primeiro dia</label>
										<input type="text" placeholder="Primeiro dia" mask="date" datePicker="firstDay"  />
										<small>[datePicker=firstDay]</small>
									</div>
									<div class="sh-w-1-4">
										<label>Último dia</label>
										<input type="text" placeholder="Último dia" mask="date" datePicker="lastDay" />
										<small>[datePicker=lastDay]</small>
									</div>
									<div class="sh-w-1-4">
										<label>Ano</label>
										<input type="text" placeholder="Ano" datePicker="year"  />
										<small>[datePicker=year]</small>
									</div>
								</div>
								
							</div>
						</fieldset>
						
						<fieldset>
							
							<h3>InputCase<small> - exibindo modelos de UpperCase, LowerCase</small></h3>
							
							<div class="sh-form-fs">
							
								<div class="sh-form-fs-group sh-w-1">
									<div class="sh-w-1-4">
										<label>Caixa Alta</label>
										<input type="text" placeholder="Caixa Alta" data-uppercase  />
										<small>[data-uppercase]</small>
									</div>
									<div class="sh-w-1-4">
										<label>Caixa Baixa</label>
										<input type="text" placeholder="Caixa Baixa" data-lowercase  />
										<small>[data-lowercase]</small>
									</div>
								</div>
								
								
							</div>
							
							<div class="sh-btn-holder">
								<button class="sh-btn-laranja">Enviar</button>
							</div>
						
						</fieldset>
					
					
					</form>
					
				</div>
			
			</section>
			
			
			<section class="sh-box sh-box-azul">
			
				<header>
					<h1>Exemplos de respostas de validação de formulários</h1>
				</header>
				
				<div class="sh-box-content">
					
					<form class="sh-form sh-form-azul">
						
						<fieldset>
							<h3>Resposta de validação <small> - estilo para campos inválidos</small></h3>
							
							<div class="sh-form-fs">
							
								<div class="sh-w-1-2 sh-val-failed">
									<label>Nome</label>
									<input type="text" placeholder="Nome"  />
								</div>
								<div class="sh-w-1-2 sh-val-failed">
									<label>Endereço</label>
									<input type="text" placeholder="Endereço"  />
								</div>
								
								<div class="sh-w-1-3 sh-val-failed">
									<label>Escolaridade</label>
									<select>
										<option>Selecione</option>
										<option>Analfabeto</option>
										<option>Fundamental</option>
										<option>Médio</option>
										<option>Graduado</option>
										<option>Pós Graduado</option>
										<option>Mestre</option>
									</select>
								</div>
								<div class="sh-w-1-3 sh-val-failed">
									<label>Sexo</label>
									<label class="sh-radio">
										<input type="checkbox" /> Masculino
									</label>
									<label class="sh-radio">
										<input type="checkbox" /> Feminino
									</label>
								</div>
								<div class="sh-w-1-3 sh-val-failed">
									<label>Sexo</label>
									<label class="sh-radio">
										<input type="radio" /> Masculino
									</label>
									<label class="sh-radio">
										<input type="radio" /> Feminino
									</label>
									<label class="sh-radio">
										<input type="radio" /> Feminino
									</label>
									<label class="sh-radio">
										<input type="radio" /> Feminino
									</label>
								</div>
								
								<div class="sh-w-1-2 sh-input-file sh-val-failed">
									<label for="arquivo" class=" show-labels">Arquivo</label>
									<div>
										<span data-input-trigger="" data-icon="f"></span>
										<div>
											<a href="dfd.php?i=9BE53BDB-F512-4A53-98FE-F4F615290844">14.07.14</a>
											<label>
												<input type="checkbox" data-op="remove" value="1" name="arquivo_remove" id="arquivo_remove"> 
												Remover arquivo
											</label>
										</div>
										<input type="file" name="arquivo" id="arquivo">
										<input type="hidden" id="arquivo_current">
									</div>
								</div>
								
								<div class="sh-w-1 sh-val-failed">
									<label>Mensagem</label>
									<textarea></textarea>
								</div>
								<div class="sh-w-1 sh-val-failed">
									<label>Mensagem</label>
									<textarea richtext="simple"></textarea>
								</div>
								
							</div>
							
							<div class="sh-btn-holder">
								<button class="sh-btn-laranja">Enviar</button>
							</div>
						
						</fieldset>
						
					</form>
					
				</div>
			
			</section>
			
			
			<section class="sh-box sh-box-verde">
			
				<header>
					<div><span data-icon="k"></span></div>
					<h1>Validando formularios</h1>
					<div>100/200</div>
				</header>
				
				<div class="sh-box-content">
					
					<form id="formValidacaoApenas" class="sh-form sh-form-verde" method="post" action="" novalidate autocomplete="off">
						
						<fieldset>
							<h3>Validação de formulários <small> - envie o formulário para que o mesmo seja validado</small></h3>
							
							<div class="sh-form-fs">
							
								<p>Para o formulário ser validado os seus campos devem receber [required] e também devem possuir name</p>
							
								<div class="sh-w-1-2">
									<label>Nome</label>
									<input type="text" placeholder="Nome" name="name" required  />
								</div>
								<div class="sh-w-1-2">
									<label>Endereço</label>
									<input type="text" placeholder="Endereço" name="name" required  />
								</div>
								
								<!-- Strings -->
								<div class="sh-w-1-3">
									<label>String com tamanho mínimo</label>
									<input type="text" placeholder="Min: 3" name="name" required data-text-min="3"  />
									<small>[data-text-min="3"]</small>
								</div>
								<div class="sh-w-1-3">
									<label>String com tamanho máximo</label>
									<input type="text" placeholder="Máx: 5" name="name" required data-text-max="5"  />
									<small>[data-text-max="5"]</small>
								</div>
								<div class="sh-w-1-3">
									<label>String com tamanho mínimo e máximo</label>
									<input type="text" placeholder="Min: 3 Máx: 5" name="nome" required data-text-min="3" data-text-max="5"  />
									<small>[data-text-min="3"][data-text-max="5"]</small>
								</div>
								
								<!-- Numeros -->
								<div class="sh-w-1-3">
									<label>Número Mínimo</label>
									<input type="text" placeholder="Mínimo : 10" name="name" mask="number" required validationType="number" data-number-min="10"  />
									<small>[data-number-min="10"]</small>
								</div>
								<div class="sh-w-1-3">
									<label>Número Máximo</label>
									<input type="text" placeholder="Máximo : 50" name="name" mask="number" required validationType="number" data-number-max="50"  />
									<small>[data-number-max="50"]</small>
								</div>
								<div class="sh-w-1-3">
									<label>Número Mínimo e Máximo</label>
									<input type="text" placeholder="Mínimo: 10 | Máximo : 50" name="name" mask="number" required validationType="number" data-number-min="10" data-number-max="50"  />
									<small>[data-number-min="10"][data-number-max="50"]</small>
								</div>
								<br />
								
								<div class="sh-w-1-3">
									<label>Escolaridade</label>
									<select name="name" required>
										<option value="" >Selecione</option>
										<option>Analfabeto</option>
										<option>Fundamental</option>
										<option>Médio</option>
										<option>Graduado</option>
										<option>Pós Graduado</option>
										<option>Mestre</option>
									</select>
								</div>
								
								<div class="sh-w-1-3">
									<label>Sexo</label>
									<label class="sh-radio">
										<input type="checkbox" name="sexo" required /> Masculino
									</label>
									<label class="sh-radio">
										<input type="checkbox" name="sexo" required /> Feminino
									</label>
								</div>
								
								<div class="sh-w-1-3">
									<label>Sexo</label>
									<label class="sh-radio">
										<input type="radio" name="checkSexo" required /> Masculino
									</label>
									<label class="sh-radio">
										<input type="radio" name="checkSexo" required /> Feminino
									</label>
									<label class="sh-radio">
										<input type="radio" name="checkSexo" required /> Feminino
									</label>
									<label class="sh-radio">
										<input type="radio" name="checkSexo" required /> Feminino
									</label>
								</div>
								
								<div class="sh-w-1-2 sh-input-file">
									<label for="arquivo" class=" show-labels">Arquivo</label>
									<div>
										<span data-input-trigger="" data-icon="f"></span>
										<div>
											<a href="dfd.php?i=9BE53BDB-F512-4A53-98FE-F4F615290844">14.07.14</a>
											<label>
												<input type="checkbox" data-op="remove" value="1" name="arquivo_remove" id="arquivo_remove"> 
												Remover arquivo
											</label>
										</div>
										<input type="file" name="arquivo" id="arquivo" required>
										<input type="hidden" id="arquivo_current">
									</div>
								</div>
								
								<div class="sh-w-1">
									<label>Mensagem</label>
									<textarea name="text" required></textarea>
								</div>
								<div class="sh-w-1">
									<label>Mensagem</label>
									<textarea richtext="simple" name="html" required></textarea>
								</div>
								
							</div>
							
							<div class="sh-btn-holder">
								<button type="submit" class="sh-btn-verde">Enviar</button>
							</div>
						
						</fieldset>
						
					</form>
					
					<script>
						require(['jquery', 'sheer'], function ($, sheer) {
	
							$('#formValidacaoApenas').on('submit', function (evt) {
								evt.stopPropagation();
								evt.preventDefault();
	
								sheer.form.validation.validar(this);
							});
							
						})
					</script>
					
				</div>
			
			</section>
		
		</div>
	
		<div id="boxes" class="subpage" style="display: none;">
		
			<section class="sh-box" data-id="lucasimas">
			
				<header>
					<div><span data-icon="k"></span></div>
					<h1>Box de exibição padrão</h1>
					<div>100/200</div>
					<a href="navigationExemplo" sh-component="navigation" data-icon="j" data-brasil="dasdas" data-idBrasil="lucassim"></a>
				</header>
				
				<div class="sh-box-content">
				
					<h1 class="sh-color-cinza">Exemplo de Título H1</h1>
					
					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
					
					<hr />
					
					<h2 class="sh-color-verde">Exemplo de Título H2</h2>
					
					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
					
					<hr />
					
					<h3 class="sh-color-agua">Exemplo de Título H3</h3>
					
					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
					
					<hr />
					
					<h4 class="sh-color-azul">Exemplo de Título H4</h4>
					
					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
					
					<hr />
					
					<h5 class="sh-color-amarelo">Exemplo de Título H5</h5>
					
					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
					
					<hr />
					
					<h6 class="sh-color-laranja">Exemplo de Título H6</h6>
					
					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
					
					<p class="data-center">Centralizado</p>
					<div class="sh-btn-holder">
						<button>Botão</button>
						<a class="sh-btn-azul">Botão</a>
						<a class="sh-btn-agua">Botão</a>
						<a class="sh-btn-verde">Botão</a>
						<a class="sh-btn-amarelo">Botão</a>
						<a class="sh-btn-laranja">Botão</a>
					</div>
					
					<p class="data-left">Alinhados a esquerda</p>
					<div class="sh-btn-holder sh-btn-holder-l">
						<button>Botão</button>
						<a class="sh-btn-azul">Botão</a>
						<a class="sh-btn-agua">Botão</a>
						<a class="sh-btn-verde">Botão</a>
						<a class="sh-btn-amarelo">Botão</a>
						<a class="sh-btn-laranja">Botão</a>
					</div>
					
					<p class="data-right">Alinhados a direita</p>
					<div class="sh-btn-holder sh-btn-holder-r">
						<button>Botão</button>
						<a class="sh-btn-azul">Botão</a>
						<a class="sh-btn-agua">Botão</a>
						<a class="sh-btn-verde">Botão</a>
						<a class="sh-btn-amarelo">Botão</a>
						<a class="sh-btn-laranja">Botão</a>
					</div>
					
				</div>
			
			</section>
			
			<template id="navigationExemplo">
				<div class="sheer-nav-inline">
					<header>
						<h1>Menu Primeiro</h1>
						<p>Meu teste</p>
					</header>
					<ul>
						<li><a href="#brasil-{id}">Brasil</a></li>
						<li><a href="#argentina-{id}">Argentina</a></li>
						<li><a href="#chile-{id}">Chile</a></li>
					</ul>
					<footer>
						<h1>Menu Primeiro</h1>
						<p>Meu teste</p>
					</footer>
				</div>
			</template>
			
			<section class="sh-box sh-box-cinza">
		
				<header>
					<div><span data-icon="k"></span></div>
					<h1>Box de exibição padrão / Cinza</h1>
				</header>
				
				<div class="sh-box-content">
					
					<h4>Exemplo de Título H4</h4>
					
					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
					
					<div class="sh-btn-holder">
						<button class="sh-btn-cinza">Botão</button>
						<button class="sh-btn-cinza-i">Botão</button>
					</div>
					
				</div>
			
			</section>
			
			<div class="sh-w-1-3">
				
				<section class="sh-box sh-box-amarelo">
			
					<header>
						<div><span data-icon="k"></span></div>
						<h1>Box de exibição Amarelo</h1>
					</header>
					
					<div class="sh-box-content">
						
						<h4>Exemplo de Título H4</h4>
						
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
						
						<div class="sh-btn-holder">
							<button class="sh-btn-amarelo">Botão</button>
							<button class="sh-btn-amarelo-i">Botão</button>
						</div>
						
					</div>
				
				</section>
				
			</div>
			
			<div class="sh-w-2-3">
				<section class="sh-box sh-box-laranja">
				
					<header>
						<div><span data-icon="k"></span></div>
						<h1>Box de exibição Laranja</h1>
					</header>
					
					<div class="sh-box-content">
						
						<h4>Exemplo de Título H4</h4>
						
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
						
						<div class="sh-btn-holder">
							<button class="sh-btn-laranja">Botão</button>
							<button class="sh-btn-laranja-i">Botão</button>
						</div>
						
					</div>
				
				</section>
			</div>
			
			
			
			<div class="sh-w-1-2">
				<section class="sh-box sh-box-verde">
				
					<header>
						<div><span data-icon="k"></span></div>
						<h1>Box de exibição Verde</h1>
					</header>
					
					<div class="sh-box-content">
						
						<h4>Exemplo de Título H4</h4>
						
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
						
						<div class="sh-btn-holder">
							<button class="sh-btn-verde">Botão</button>
							<button class="sh-btn-verde-i">Botão</button>
						</div>
						
					</div>
				
				</section>
			</div>
			
			<div class="sh-w-1-2">
				<section class="sh-box sh-box-agua">
				
					<header>
						<div><span data-icon="k"></span></div>
						<h1>Box de exibição Verde</h1>
					</header>
					
					<div class="sh-box-content">
						
						<h4>Exemplo de Título H4</h4>
						
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
						
						<div class="sh-btn-holder">
							<button class="sh-btn-agua">Botão</button>
							<button class="sh-btn-agua-i">Botão</button>
						</div>
						
					</div>
				
				</section>
			</div>
			
			<section class="sh-box sh-box-azul sh-w-500 sh-margin-x-auto">
			
				<header>
					<div><span data-icon="k"></span></div>
					<h1>Box de exibição Azul / Alinhamento central </h1>
				</header>
				
				<div class="sh-box-content">
					
					<h4>Exemplo de Título H4</h4>
					
					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
					
					<div class="sh-btn-holder">
						<button class="sh-btn-azul">Botão</button>
						<button class="sh-btn-azul-i">Botão</button>
					</div>
					
				</div>
			
			</section>
			
		</div>
		
		<div id="tabelas" class="subpage" style="display: none;">
		
			<section class="sh-box sh-box-azul sh-box-table">
		
				<header>
					<div><span data-icon="k"></span></div>
					<h1>Tabela de dados padrão</h1>
				</header>
				
				<div class="sh-box-content">
				
					<h3>Tabela normal de conteudo</h3>
					
					<table class="sh-table">
						<thead>
							<tr>
								<th>#</th>
								<th>Nome</th>
								<th>Telefone</th>
								<th>Email</th>
								<th class="sh-w-50"></th>
							</tr>
						</thead>
						<tbody>
							<tr data-id="dasdjjdkhadkjhakjhdahkjas">
								<td>1</td>
								<td>Guilherme Carneiro</td>
								<td>(21) 974.017.167</td>
								<td>falecomigo@guilherme.ga</td>
								<td>
									<a href="action.php" data-icon="d" sh-component="action" sh-comp-content="1" sh-comp-confirm></a>
								</td>
							</tr>
							<tr>
								<td>2</td>
								<td>Guilherme Carneiro</td>
								<td>(21) 974.017.167</td>
								<td>falecomigo@guilherme.ga</td>
								<td>
									<a href="action.php" data-icon="d" sh-component="action" sh-comp-content="n" sh-comp-contentNode="contentNodeAction"></a>
								</td>
							</tr>
							<tr>
								<td>3</td>
								<td>Guilherme Carneiro</td>
								<td>(21) 974.017.167</td>
								<td>falecomigo@guilherme.ga</td>
								<td>
									<a href="action.php" data-icon="d" sh-component="action" sh-comp-content="1"></a>
								</td>
							</tr>
							<tr>
								<td>4</td>
								<td>Guilherme Carneiro</td>
								<td>(21) 974.017.167</td>
								<td>falecomigo@guilherme.ga</td>
								<td>
									<a href="action.php" data-icon="d" sh-component="action" sh-comp-content="1"></a>
								</td>
							</tr>
						</tbody>
					
					</table>
					
					<h3>Tabela descritiva</h3>
					
					<p>Esse se utiliza de linhas da tabela com o atributo [data-descriptor] sendo seu valor o id contido no [data-id] de outra linha. Quando clicado na linha do [data-id] a [data-descriptor] será exibida.</p>
					
					<table class="sh-table sh-table-descriptor">
						<thead>
							<tr>
								<th>#</th>
								<th>Nome</th>
								<th>Telefone</th>
								<th>Email</th>
							</tr>
						</thead>
						<tbody>
							<tr data-id="dado1">
								<td>1</td>
								<td>Guilherme Carneiro</td>
								<td>(21) 974.017.167</td>
								<td>falecomigo@guilherme.ga</td>
							</tr>
							<tr data-descriptor="dado1">
								<td colspan="4">
								
									<section class="sh-box">
										<header>
											<h1>Box interno tabela</h1>
										</header>
										<div class="sh-box-content">
											<h2>Novo título</h2>
											<p>brasil é um país de todos</p>
										</div>
									</section>
									
								</td>
							</tr>
							<tr data-id="dado2">
								<td>2</td>
								<td>Guilherme Carneiro</td>
								<td>(21) 974.017.167</td>
								<td>falecomigo@guilherme.ga</td>
							</tr>
							<tr data-descriptor="dado2">
								<td colspan="4">
									<section class="sh-box">
										<header>
											<h1>Box interno tabela</h1>
										</header>
										<div class="sh-box-content">
											<h2>Novo título</h2>
											<p>brasil é um país de todos</p>
										</div>
									</section>
								</td>
							</tr>
							<tr data-id="dado3">
								<td>3</td>
								<td>Guilherme Carneiro</td>
								<td>(21) 974.017.167</td>
								<td>falecomigo@guilherme.ga</td>
							</tr>
							<tr data-descriptor="dado3">
								<td colspan="4">
									<section class="sh-box">
										<header>
											<h1>Box interno tabela</h1>
										</header>
										<div class="sh-box-content">
											<h2>Novo título</h2>
											<p>brasil é um país de todos</p>
										</div>
									</section>
								</td>
							</tr>
							<tr data-id="dado4">
								<td>4</td>
								<td>Guilherme Carneiro</td>
								<td>(21) 974.017.167</td>
								<td>falecomigo@guilherme.ga</td>
							</tr>
							<tr data-descriptor="dado4">
								<td colspan="4">
									<section class="sh-box">
										<header>
											<h1>Box interno tabela</h1>
										</header>
										<div class="sh-box-content">
											<h2>Novo título</h2>
											<p>brasil é um país de todos</p>
										</div>
									</section>
								</td>
							</tr>
						</tbody>
					
					</table>
					
					<h3>Tabela de conteudo administrativo</h3>
					
					<table id="contentNodeAction" class="sh-table sh-table-admin">
						<thead>
							<tr>
								<th class="sh-ta-check"><input type="checkbox" /></th>
								<th>Nome</th>
								<th>Telefone</th>
								<th>Email</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><input type="checkbox" sh-check /></td>
								<td>Guilherme Carneiro</td>
								<td>(21) 974.017.167</td>
								<td>falecomigo@guilherme.ga</td>
							</tr>
							<tr>
								<td><input type="checkbox" sh-check /></td>
								<td>Guilherme Carneiro</td>
								<td>(21) 974.017.167</td>
								<td>falecomigo@guilherme.ga</td>
							</tr>
							<tr>
								<td><input type="checkbox" sh-check /></td>
								<td>Guilherme Carneiro</td>
								<td>(21) 974.017.167</td>
								<td>falecomigo@guilherme.ga</td>
							</tr>
							<tr>
								<td><input type="checkbox" sh-check /></td>
								<td>Guilherme Carneiro</td>
								<td>(21) 974.017.167</td>
								<td>falecomigo@guilherme.ga</td>
							</tr>
						</tbody>
					
					</table>
					
				</div>
			
			</section>
		</div>
		
		<div id="avatares" class="subpage" style="display: none;">
		
			<section class="sh-box">
				<div class="sh-box-content">
					<h2>Modelo de Bloco de Avatar ou Dashboard</h2>
				
					<p>
						Para se formar o bloco do dashboard ou avatar precisamos de uma estrutura da forma "div.sh-box-avatar" &gt; "section", &gt; "div".<br /> 
						Onde a seção será considerada como a do avatar e a segunda div será um holder que completa a página onde poderemos inserir qualquer informação. 
					</p>
				</div>
			</section>
			
			<div class="sh-box-avatar sh-box-avatar-60">
			
				<div>
					<section class="sh-box ">
						<header>
							<!-- A primeira div deve ser do avatar -->
							<div>
								<img src="resources/images/avatar/pessoa_m_60.png" />
							</div>
							
							<h1>Avatar 60</h1>
						</header>
						
						<div class="sh-box-content">
						</div>
					</section>
				</div>
				
				<div>
					<section class="sh-box sh-margin-w">
						<header>
							<h1>Conteúdo esquerdo</h1>
						</header>
						
						<div class="sh-box-content">
						</div>
					</section>
				</div>
				
			</div>
			
			<div class="sh-box-avatar sh-box-avatar-90">
			
				<div>
					<section class="sh-box sh-box-azul">
						<header>
							<!-- A primeira div deve ser do avatar -->
							<div>
								<img src="resources/images/avatar/pessoa_f_90.png" />
							</div>
							
							<h1>Avatar 90</h1>
						</header>
						
						<div class="sh-box-content">
						
							<p class="data-center">
								<a href="#" data-icon="s" title="Editar Usuário"></a>
							</p>
							<hr />
							
							<div class="sh-tab">
								<h3>Tab fechada</h3>
								<div>
									<p>Este é um exemplo.</p>
								</div>
							</div>
							
							<div class="sh-tab sh-tab-open">
								<h3>Tab aberta</h3>
								<div>
									<p>Este é um exemplo.</p>
								</div>
							</div>
							
						
						</div>
					</section>
					
					<section class="sh-box sh-box-azul">
						<header>
							<h1>Auxiliar</h1>
						</header>
						
						<div class="sh-box-content">
						
							<div class="sh-tab">
								<h3>Tab fechada</h3>
								<div>
									<p>Este é um exemplo.</p>
								</div>
							</div>
							
							<div class="sh-tab sh-tab-open">
								<h3>Tab aberta</h3>
								<div>
									<p>Este é um exemplo.</p>
								</div>
							</div>
							
						
						</div>
					</section>
				</div>
			
				<div>
					<div class="sh-w-1-2">
						<section class="sh-box sh-box-azul sh-margin-w ">
							<header>
								<h1>Conteúdo esquerdo</h1>
							</header>
							
							<div class="sh-box-content">
								<h3>Tab fechada - Como usar</h3>
								<div>
									<p>A utilização das tabs de conteúdos são de forma bem simples.</p>
									<p>Tem sua estrutura <br /><strong>"div.sh-tab" > "h*" </strong><br /> <strong>"div.sh-tab" > "div"</strong></p>
									<p>Onde o título é a informação sempre aparente e a "div" tem o toggle.</p>
									<p>As tabs sempre são tratadas como fechadas inicialmente. Para uma tab iniciar aberta ela deve ser <strong>".sh-tab.sh-tab-open"</strong></p>
								</div>
								
								<h3>Tab aberta - Como usar</h3>
								<div>
									<p>Esta tab já vem aberta. Ela contém a classe <strong>"sh-tab-open"</strong></p>
								</div>
								
							</div>
						</section>
					</div>
					
					<div class="sh-w-1-2">
						<section class="sh-box sh-box-azul sh-margin-w">
							<header>
								<h1>Conteúdo Direito</h1>
							</header>
							
							<div class="sh-box-content">
								<h3>Alinhar mais de um box dentro do avatar é fácil</h3>
								<div>
									<p>Basta colocar uma div em volta limitando o tamanho das seções. Neste estou usando div.sh-w-1-2</p>
								</div>
							</div>
						</section>
					</div>
				</div>
				
			</div>
			
		</div>
		
		<div id="grid" class="subpage" style="display: none;" >
		
			<section class="sh-box">
				<header>
					<h1>Posicionamento de elementos</h1>
				</header>
				
				<div class="sh-box-content">
				
					<p>O posicionamento em blocos irá tratar os blocos como "inline-block" e determinará o seu tamanho considerando o seu "width" + "border" + "padding".</p>
					<p><strong>Tome cuidado:</strong> As "margin" aplicadas irão estrapolar o tamanho do elemento e resultarão em problemas.</p>
				
					<h2>Posicionamento 2 Blocos</h2>
				
					<p class="sh-w-1-2" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					<p class="sh-w-1-2" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
	
					<h2>Alinhamento 3 Blocos</h2>
				
					<p class="sh-w-1-3" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					<p class="sh-w-1-3" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					<p class="sh-w-1-3" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					<p class="sh-w-2-3" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					<p class="sh-w-1-3" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
	
					<h2>Alinhamento 4 Blocos</h2>
				
					<p class="sh-w-1-4" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					<p class="sh-w-1-4" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					<p class="sh-w-1-4" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					<p class="sh-w-1-4" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					
					<p class="sh-w-1-2" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					<p class="sh-w-1-4" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					<p class="sh-w-1-4" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					
					<p class="sh-w-3-4" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					<p class="sh-w-1-4" style="padding: 0.2em;">
						Este é um exemplo de alinhamento em X blocos de itens. O Sheer conta com diversas formas de posicionamento que funcionam de forma muito parecida com o <a href="http://purecss.io">PURE</a>.
					</p>
					
					
				</div>
			</section>
		
		</div>
		
		<div id="cores" class="subpage" style="display: none;">
		
			<section class="sh-box">
				
				<header>
					<h1>Cores disponíveis no sistema</h1>
				</header>
				
				<div class="sh-box-content">
					
					<p>Lista de cores disponíveis</p>
					
					<h3>Escalas de cinza</h3>
					
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@cinzaClaro]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #d9d9d7"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@cinzaClaro2]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #f7f7f7"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@cinzaClaro3]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #f3f6e5"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@cinzaClaro4]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #f1f1f1"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@cinza]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #6d6071"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@cinzaEscuro]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #544959"></span>
					</div>
					
					<h3>Escalas de Amarelo</h3>
					
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@amarelo]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #ffbf43"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@amareloEscuro]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #edad32"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@amareloAtencao]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #fbfed3"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@amareloAtencao2]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #fcf99d"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@amareloAtencao3]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #e2e5b6"></span>
					</div>
					
					<h3>Escalas de Verde</h3>
					
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@verdeBarra]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #494e41"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@verdeBarraClara]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #6b7062"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@verde]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #7da141"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@verdeEscuro]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #5c782c"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@verdeSheer]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #00a859"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@verdeSheerEscuro]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #008748"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@verdeSucessoEscuro]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #6f824b"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@verdeSucesso]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #e2f3c6"></span>
					</div>
					
					<h3>Escalas de Vermelho</h3>
					
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@vermelhoInvalidoClaro]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #fcd5d5"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@vermelhoInvalido]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #ed2c2d"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@vermelhoErroEscuro]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #c82119"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@vermelhoErro]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #ffdcda"></span>
					</div>
					
					<h3>Escalas de azul</h3>
					
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@azul]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #243B63"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@azulEscuro]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #20365c"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@azulFundo1]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #2d446d"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@azulDestaqueInput]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #129FEA"></span>
					</div>
					
					<h3>Escalas de Laranja</h3>
					
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@laranja]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #eb862c"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@laranjaEscuro]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #ae6a2d"></span>
					</div>

					<h3>Escalas de Água</h3>
					
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@agua]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #72bebe"></span>
					</div>
					<div class="sh-w-ib sh-w-200 data-center">
						<p>[@aguaEscuro]</p>
						<span style="display: inline-block; width: 40px; height: 30px; background-color: #63a0a1"></span>
					</div>

				</div>
				
			</section>
		
		</div>
		
		<div id="notify" class="subpage" style="display: none;">
		
			
		</div>

		<div id="icones" class="subpage" style="display: none;">
			<section class="sh-box">
				
				<header>
					<h1>Cores disponíveis no sistema</h1>
				</header>
				
				<div class="sh-box-content">
					
					<img src="resources/images/sh-icons.jpg" />
				
				</div>
				
			</section>
			
		</div>
		
		<div id="listaAvatar" class="subpage" style="padding: 0 0.5em; display: none;">
		
			<div class="sh-w-1-2">

				<ul class="sh-lista-avatar no-border">
		
					<li data-id="8D68746F-92CE-4376-8860-F862AA2F60C6">
					
						<div>
							<div>
								<img alt="GUILHERME CARNEIRO ANTONIO" title="GUILHERME CARNEIRO ANTONIO" src="./resources/images/avatar/pessoa_m_90.png">
							</div>
							<p><strong>GUILHERME ANTONIO</strong></p>
							<p>23 anos</p>
							<p>AA-0001</p>
						</div>
						
						<div>
							<div>
								<p class="highlight">GUILHERME CARNEIRO ANTONIO</p>
								<p><strong>Mãe: </strong>CORINA DE PAULA</p>
								
								<p class="sh-w-1"><strong>CPF: </strong>135.537.947-47</p>
								
								<p class="sh-w-1-2"><strong>CPF: </strong>135.537.947-47</p>
								<p class="sh-w-1-2"><strong>CPF: </strong>135.537.947-47</p>
								
								<p class="sh-w-1-3"><strong>CNS: </strong>801 4341 3995 4111</p>
								<p class="sh-w-1-3"><strong>CPF: </strong>135.537.947-47</p>
								<p class="sh-w-1-3"><strong>CPF: </strong>135.537.947-47</p>
								
								<p class="sh-w-1-4"><strong>CPF: </strong>135.537.947-47</p>
								<p class="sh-w-1-4"><strong>CPF: </strong>135.537.947-47</p>
								<p class="sh-w-1-4"><strong>CPF: </strong>135.537.947-47</p>
								<p class="sh-w-1-4"><strong>CPF: </strong>135.537.947-47</p>
							</div>
						</div>
						
						<div>
							<div class="btn-circle">
								<a href="#" data-icon="d" sh-component-target="pacienteMenu" sh-component="navigation"></a>
							</div>
							
							<a href="#">Agendar</a>
						</div>
						
					</li>
		
					<li data-id="8D68746F-92CE-4376-8860-F862AA2F60C6">
					
						<div>
							<div>
								<img alt="GUILHERME CARNEIRO ANTONIO" title="GUILHERME CARNEIRO ANTONIO" src="./resources/images/avatar/pessoa_m_90.png">
							</div>
							<p><strong>GUILHERME ANTONIO</strong></p>
							<p>23 anos</p>
							<p>AA-0001</p>
						</div>
						
						<div>
							<div>
								<p class="highlight">GUILHERME CARNEIRO ANTONIO</p>
								<p><strong>Mãe: </strong>CORINA DE PAULA</p>
								
								<p class="sh-w-1"><strong>CPF: </strong>135.537.947-47</p>
								
								<p class="sh-w-1-2"><strong>CPF: </strong>135.537.947-47</p>
								<p class="sh-w-1-2"><strong>CPF: </strong>135.537.947-47</p>
								
								<p class="sh-w-1-3"><strong>CNS: </strong>801 4341 3995 4111</p>
								<p class="sh-w-1-3"><strong>CPF: </strong>135.537.947-47</p>
								<p class="sh-w-1-3"><strong>CPF: </strong>135.537.947-47</p>
								
								<p class="sh-w-1-4"><strong>CPF: </strong>135.537.947-47</p>
								<p class="sh-w-1-4"><strong>CPF: </strong>135.537.947-47</p>
								<p class="sh-w-1-4"><strong>CPF: </strong>135.537.947-47</p>
								<p class="sh-w-1-4"><strong>CPF: </strong>135.537.947-47</p>
							</div>
						</div>
						
						<div>
							<div class="btn-circle">
								<a href="#" data-icon="d" sh-component-target="pacienteMenu" sh-component="navigation"></a>
							</div>
							
							<a href="#">Agendar</a>
						</div>
						
					</li>
					
				</ul>
				
				<div class="sh-lista-avatar sh-lista-avatar-60">
		
					<div class="sh-color-cinza" data-id="8D68746F-92CE-4376-8860-F862AA2F60C6">
					
						<div>
							<div>
								<img alt="GUILHERME CARNEIRO ANTONIO" title="GUILHERME CARNEIRO ANTONIO" src="./resources/images/avatar/pessoa_m_60.png">
							</div>
							<p><strong>GUILHERME ANTONIO</strong></p>
							<p>23 anos</p>
							<p>AA-0001</p>
						</div>
						
						<div>
							<div>
								<p class="highlight">GUILHERME CARNEIRO ANTONIO</p>
								<p><strong>Mãe: </strong>CORINA DE PAULA</p>
								
								<p class="sh-w-1"><strong>CPF: </strong>135.537.947-47</p>
								
							</div>
						</div>
						
						<div>
							<div class="btn-circle">
								<a href="#" data-icon="d" sh-component-target="pacienteMenu" sh-component="navigation"></a>
							</div>
							
							<a href="#">Agendar</a>
						</div>
						
					</div>
		
					<div class="sh-color-verde" data-id="8D68746F-92CE-4376-8860-F862AA2F60C6">
					
						<div>
							<div>
								<img alt="GUILHERME CARNEIRO ANTONIO" title="GUILHERME CARNEIRO ANTONIO" src="./resources/images/avatar/pessoa_m_60.png">
							</div>
							<p><strong>GUILHERME ANTONIO</strong></p>
							<p>23 anos</p>
							<p>AA-0001</p>
						</div>
						
						<div>
							<div>
								<p class="highlight">GUILHERME CARNEIRO ANTONIO</p>
								<p><strong>Mãe: </strong>CORINA DE PAULA</p>
								
								<p class="sh-w-1"><strong>CPF: </strong>135.537.947-47</p>
								
							</div>
						</div>
						
						<div>
							<div class="btn-circle">
								<a href="#" data-icon="d" sh-component-target="pacienteMenu" sh-component="navigation"></a>
							</div>
							
							<a href="#">Agendar</a>
						</div>
						
					</div>
					
					<div class="sh-color-agua" data-id="8D68746F-92CE-4376-8860-F862AA2F60C6">
					
						<div>
							<div>
								<img alt="GUILHERME CARNEIRO ANTONIO" title="GUILHERME CARNEIRO ANTONIO" src="./resources/images/avatar/pessoa_m_60.png">
							</div>
							<p><strong>GUILHERME ANTONIO</strong></p>
							<p>23 anos</p>
							<p>AA-0001</p>
						</div>
						
						<div>
							<div>
								<p class="highlight">GUILHERME CARNEIRO ANTONIO</p>
								<p><strong>Mãe: </strong>CORINA DE PAULA</p>
								
								<p class="sh-w-1"><strong>CPF: </strong>135.537.947-47</p>
								
							</div>
						</div>
						
						<div>
							<div class="btn-circle">
								<a href="#" data-icon="d" sh-component-target="pacienteMenu" sh-component="navigation"></a>
							</div>
							
							<a href="#">Agendar</a>
						</div>
						
					</div>
					
					<div class="sh-color-azul" data-id="8D68746F-92CE-4376-8860-F862AA2F60C6">
					
						<div>
							<div>
								<img alt="GUILHERME CARNEIRO ANTONIO" title="GUILHERME CARNEIRO ANTONIO" src="./resources/images/avatar/pessoa_m_60.png">
							</div>
							<p><strong>GUILHERME ANTONIO</strong></p>
							<p>23 anos</p>
							<p>AA-0001</p>
						</div>
						
						<div>
							<div>
								<p class="highlight">GUILHERME CARNEIRO ANTONIO</p>
								<p><strong>Mãe: </strong>CORINA DE PAULA</p>
								
								<p class="sh-w-1"><strong>CPF: </strong>135.537.947-47</p>
								
							</div>
						</div>
						
						<div>
							<div class="btn-circle">
								<a href="#" data-icon="d" sh-component-target="pacienteMenu" sh-component="navigation"></a>
							</div>
							
							<a href="#">Agendar</a>
						</div>
						
					</div>
					
					<div class="sh-color-laranja" data-id="8D68746F-92CE-4376-8860-F862AA2F60C6">
					
						<div>
							<div>
								<img alt="GUILHERME CARNEIRO ANTONIO" title="GUILHERME CARNEIRO ANTONIO" src="./resources/images/avatar/pessoa_m_60.png">
							</div>
							<p><strong>GUILHERME ANTONIO</strong></p>
							<p>23 anos</p>
							<p>AA-0001</p>
						</div>
						
						<div>
							<div>
								<p class="highlight">GUILHERME CARNEIRO ANTONIO</p>
								<p><strong>Mãe: </strong>CORINA DE PAULA</p>
								
								<p class="sh-w-1"><strong>CPF: </strong>135.537.947-47</p>
								
							</div>
						</div>
						
						<div>
							<div class="btn-circle">
								<a href="#" data-icon="d" sh-component-target="pacienteMenu" sh-component="navigation"></a>
							</div>
							
							<a href="#">Agendar</a>
						</div>
						
					</div>
					
					<div class="sh-color-amarelo no-border" data-id="8D68746F-92CE-4376-8860-F862AA2F60C6">
					
						<div>
							<div>
								<img alt="GUILHERME CARNEIRO ANTONIO" title="GUILHERME CARNEIRO ANTONIO" src="./resources/images/avatar/pessoa_m_60.png">
							</div>
							<p><strong>GUILHERME ANTONIO</strong></p>
							
							<p>AA-0001</p>
						</div>
						
						<div>
							<div>
								<p class="highlight">GUILHERME CARNEIRO ANTONIO</p>
								<p>Basta adicionar a classe "no-border" para não ter a borda</p>
								<p class="sh-w-1"><strong>CPF: </strong>135.537.947-47</p>
								
							</div>
						</div>
						
						<div>
							<div class="btn-circle">
								<a href="#" data-icon="d" sh-component-target="pacienteMenu" sh-component="navigation"></a>
							</div>
							
							<a href="#">Agendar</a>
						</div>
						
					</div>
		
					<div class="sh-color-vermelho no-border" data-id="8D68746F-92CE-4376-8860-F862AA2F60C6">
					
						<div>
							<div>
								<img alt="GUILHERME CARNEIRO ANTONIO" title="GUILHERME CARNEIRO ANTONIO" src="./resources/images/avatar/pessoa_m_60.png">
							</div>
							<p><strong>GUILHERME ANTONIO</strong></p>
							<p>23 anos</p>
							<p>AA-0001</p>
						</div>
						
						<div>
							<div>
								<p class="highlight">GUILHERME CARNEIRO ANTONIO</p>
								<p><strong>Mãe: </strong>CORINA DE PAULA</p>
								
								<p class="sh-w-1"><strong>CPF: </strong>135.537.947-47</p>
								
							</div>
						</div>
						
						<div>
							<div class="btn-circle">
								<a href="#" data-icon="d" sh-component-target="pacienteMenu" sh-component="navigation"></a>
							</div>
							
							<a href="#">Agendar</a>
						</div>
						
					</div>
					
				</div>
			</div>
			
			<div class="sh-w-1-2">
			
				<div class="sh-lista-avatar sh-lista-avatar-60 sh-lista-avatar-box">
		
					<div class="sh-color-cinza" data-id="8D68746F-92CE-4376-8860-F862AA2F60C6">
					
						<div>
							<div>
								<img alt="GUILHERME CARNEIRO ANTONIO" title="GUILHERME CARNEIRO ANTONIO" src="./resources/images/avatar/pessoa_m_60.png">
							</div>
							<p><strong>GUILHERME ANTONIO</strong></p>
							<p>23 anos</p>
							<p>AA-0001</p>
						</div>
						
						<div>
							<div>
								<p class="highlight">GUILHERME CARNEIRO ANTONIO</p>
								<p><strong>Mãe: </strong>CORINA DE PAULA</p>
								
								<p class="sh-w-1"><strong>CPF: </strong>135.537.947-47</p>
								
							</div>
						</div>
						
					</div>
					
					<div class="sh-color-cinza" data-id="8D68746F-92CE-4376-8860-F862AA2F60C6">
					
						<div>
							<div>
								<img alt="GUILHERME CARNEIRO ANTONIO" title="GUILHERME CARNEIRO ANTONIO" src="./resources/images/avatar/pessoa_m_60.png">
							</div>
							<p><strong>GUILHERME ANTONIO</strong></p>
						</div>
						
						<div>
							<div>
								<p class="highlight">GUILHERME CARNEIRO ANTONIO</p>
								<p><strong>Mãe: </strong>CORINA DE PAULA</p>
								
								<p class="sh-w-1"><strong>CPF: </strong>135.537.947-47</p>
								
							</div>
						</div>
						
					</div>
					
					<div class="sh-color-cinza" data-id="8D68746F-92CE-4376-8860-F862AA2F60C6">
					
						<div>
							<div>
								<img alt="GUILHERME CARNEIRO ANTONIO" title="GUILHERME CARNEIRO ANTONIO" src="./resources/images/avatar/pessoa_m_60.png">
							</div>
							<p><strong>GUILHERME ANTONIO</strong></p>
						</div>
						
					</div>
					
				</div>
			
			</div>
		</div>
		
		
	</body>
</html>

<script>
	require(['jquery'], function (jquery) {

		$('#navegadorSubPages a').on('click', function (evt) {

			var jqBotao = $(this);
			jqBotao.siblings('a[data-subPage]').removeClass('sh-btn-i');
			jqBotao.addClass('sh-btn-i');
			

			evt.preventDefault();
			var jqBody = $('body');
			jqBody.children('.subpage').hide();
			jqBody.children('#'+this.getAttribute('data-subpage')).show();

		});
		
	})

</script>