<section class="sh-box sh-box-laranja sh-margin-x-auto sh-w-600">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Enviar teste</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=malaDiretaEnvioTeste/enviarTeste" method="post" novalidate sh-form >
		
			<fieldset class="sh-grid-box">
			
				<h3>Informe os emails separados por ";" ou ","</h3>
				
				<div class="sh-form-fs">
					<input type="hidden" name="idCampanha" id="idCampanha" value="<?php echo $_GET['id'] ?>" />
					<div class="sh-w-1">
						<label for="destinos">Destinos</label>
						<input type="text" name="destinos" id="destinos" placeholder="Destinos" required="true" />
					</div>
				</div>
				
			</fieldset>
				
			<div class="sh-btn-holder">
				<button type="submit" class="sh-btn-laranja">Enviar</button>
			</div>
			
		</form>
		
	</div>
	
</section>