<?php

namespace Sh;

class PageHolderProcessor extends \Sh\PageProcessor {
	
	/**
	 * Método responsável por renderizar todos os holders de uma página e 
	 * montar um array de retorno a ser transformado em json
	 * 
	 * @return mixed
	 */
	public function render () {
		
		//Declarando array de resposta dos holders
		$responseHolders = array(
			'status' 		=> true,
			'holders' 		=> array(),
			'title' 		=> $this->config['config']['title'],
			'description' 	=> $this->config['config']['description']
		);
		//Carregando html do template
		$html = $this->template;
		
		//BUSCANDO E PROCESSANDO TODOS OS HOLDERS CONTIDOS NO TEMPLATE
		$tmp = $this->renderHolders($html);
		$holders = $tmp['searchs'];
		$htmls = $tmp['replaces'];
		
		//PARA CADA HOLDER PROCESSADO IREI PROCESSAR SEUS HTML E CONSUMIR SEU ALIAS
		//TAMBÉM NESTE LOOP JÁ MONTO O ARRAY DE RETORNO FINAL
		$regex = '/{{(template|renderer)\.([\w\+\-\.]+)}}/';
		foreach ( $htmls as $key=>&$htmlHolder ) {
			/*
			 * Consumindo todos os alias de conteudo e aplicando para gerar html final
			 * Alias suportados:
			 * 		template -> Carrega templates estaticos
			 * 		renderer -> Executa um renderable explicitado de forma direta
			 */
			do {
				$searchs = array();
				$replaces = array();
				
				//RENDERABLES
				$tmp = $this->renderRenderers($htmlHolder);
				$searchs = array_merge($searchs, $tmp['searchs']);
				$replaces = array_merge($replaces, $tmp['replaces']);
				
				//BUSCANDO SUBTEMPLATES
				$tmp = $this->renderSubTemplates($htmlHolder);
				$searchs = array_merge($searchs, $tmp['searchs']);
				$replaces = array_merge($replaces, $tmp['replaces']);
				
				//GERANDO HTML FINAL
				$htmlHolder = str_replace($searchs, $replaces, $htmlHolder);
				//Buscando matches faltantes
				$matches = preg_match($regex, $htmlHolder);
				
			} while ($matches);
			
			//GERANDO RESPONSE FINAL
			//Determinando identificador do holder
			$mt = str_replace('{{holder.', '', $holders[$key]);
			$mt = str_replace('}}', '', $mt);
			$responseHolders['holders'][$mt] = $htmlHolder;
			
		}
		
		$responseHolders['status'] = true;
		
		return $responseHolders;
		
	}
	
}