<?php

namespace Sh;

/**
 * @author guilherme
 * 
 * Classe estática para servir para a renderização de elementos em geral
 *
 */
abstract class RendererLibrary {
	
	static public function newConfig() {
		
		return array(
			'div' => array(
				'id'			=> null,
				'class' 		=> null,
				'attributes'	=> ''		
			),
			'label' => array(
				'exibir' 		=> true,
				'id'			=> null,
				'nome' 			=> null,
				'class' 		=> null,
				'attributes'	=> ''
			),
			'field' => array (
				'id'				=> null,
				'placeholder' 		=> null,
				'blankOption'		=> null,
				'name'				=> null,
				'uppercase'			=> false,
				'lowercase'			=> false,
				'required'			=> false,
				'mask'				=> null,
				'validationType'	=> null,
				'class'				=> null,
				'dpFilters'			=> array(),
				'attributes'		=> ''				
			)
		);
	}
	
	
	static public function renderInputBoxFromField (\Sh\DataSourceField $field, $value=null, $fieldConfig=null, $divConfig=null, $labelConfig=null) {
		
		//Tratando as configurações
		if( !$fieldConfig ) 	{ $fieldConfig = array(); }
		if( !$divConfig ) 		{ $divConfig = array(); }
		if( !$labelConfig ) 	{ $labelConfig = array(); }
		
		/*
		 * Variáveis de controle geral
		 */
		$configuration = self::newConfig();
		
		/*
		 * DEFININDO CONFIGURAÇÕES COMPARTILHADAS ENTRE LABEL E INPUT
		 */
		//PLACEHOLDER E NOME
		//buscando pelo placeholder das configurações
		if( isset($fieldConfig['placeholder']) && strlen($fieldConfig['placeholder']) ) {
			$configuration['label']['nome'] = $configuration['field']['placeholder'] = $fieldConfig['placeholder'];
		}
		//tendo placeholder e sendo false, não coloco
		else if ( isset($fieldConfig['placeholder']) && !$fieldConfig['placeholder'] ) {
			$configuration['field']['placeholder'] = null;
			$configuration['label']['nome'] = $field->getName();
		}
		//não tendo placeholder assumo sendo o nome do field
		else {
			$configuration['label']['nome'] = $configuration['field']['placeholder'] = $field->getName();
		}
		//Se eu tiver o nome para o label setado, utilizo ele apenas para o label
		if( isset($labelConfig['name']) ) {
			$configuration['label']['nome'] = $labelConfig['name'];
		}
		//Determinando se devo esconder o label
		if( isset($labelConfig['hide']) && $labelConfig['hide'] ) {
			$configuration['label']['exibir'] = false;
		}
		
		//ID - temos que manter o mesmo id para o label e para o input
		if( isset($fieldConfig['id']) && strlen($fieldConfig['id']) ) {
			$configuration['label']['id'] = $configuration['field']['id'] = $fieldConfig['id'];
		}
		else if ( isset($labelConfig['id']) && strlen($labelConfig['id']) ) {
			$configuration['label']['id'] = $configuration['field']['id'] = $labelConfig['id'];
		}
		else {
			$configuration['label']['id'] = $configuration['field']['id'] = $field->getId();
		}
		
		//PROCESSANDO CONFIGURAÇÕES DO FIELD
		//name
		if( isset($fieldConfig['name']) && strlen($fieldConfig['name']) ) {
			$configuration['field']['name'] = $fieldConfig['name'];
		}
		else {
			$configuration['field']['name'] = $field->getId();
		}
		unset($fieldConfig['name']);
		//required
		if( isset($fieldConfig['required']) ) {
			$configuration['field']['required'] = !!$fieldConfig['required'];
		}
		else {
			$configuration['field']['required'] = $field->getRequired();
		}
		unset($fieldConfig['required']);
		//blankOption
		if( isset($fieldConfig['blankOption']) && is_string($fieldConfig['blankOption']) && strlen($fieldConfig['blankOption']) ) {
			$configuration['field']['blankOption'] = $fieldConfig['blankOption'];
		}
		else if( isset($fieldConfig['blankOption']) && $fieldConfig['blankOption'] )  {
			$configuration['field']['blankOption'] = $fieldConfig['blankOption'];
		}
		unset($fieldConfig['blankOption']);
		//uppercase
		if( isset($fieldConfig['uppercase']) ) {
			$configuration['field']['uppercase'] = !!$fieldConfig['uppercase'];
		}
		else {
			$configuration['field']['uppercase'] = $field->getUpperCase();
		}
		unset($fieldConfig['uppercase']);
		//lowercase
		if( isset($fieldConfig['lowercase']) ) {
			$configuration['field']['lowercase'] = !!$fieldConfig['lowercase'];
		}
		else {
			$configuration['field']['lowercase'] = $field->getUpperCase();
		}
		unset($fieldConfig['lowercase']);
		//dpFilters
		if( isset($fieldConfig['dpFilters']) && is_array($fieldConfig['dpFilters']) ) {
			$configuration['field']['dpFilters'] = $fieldConfig['dpFilters'];
		}
		unset($fieldConfig['dpFilters']);
		
		//PROPRIEDADES QUE VIRAM ATRIBUTOS DIRETOS
		//mascara
		if( isset($fieldConfig['mask']) && is_string($fieldConfig['mask']) && strlen($fieldConfig['mask']) ) {
			$configuration['field']['mask'] = $fieldConfig['mask'];
		}
		else {
			$configuration['field']['mask'] = $field->getMask();
		}
		unset($fieldConfig['mask']);
		//validationType
		if( isset($fieldConfig['validationType']) && is_string($fieldConfig['validationType']) && strlen($fieldConfig['validationType']) ) {
			$configuration['field']['validationType'] = $fieldConfig['validationType'];
		}
		else {
			$configuration['field']['validationType'] = $field->getValidationType();
		}
		unset($fieldConfig['validationType']);
		
		//Processando classe de todos os elementos
		if( isset($fieldConfig['class']) ) {
			$configuration['field']['class'] = $fieldConfig['class'];
		}
		if( isset($divConfig['class']) ) {
			$configuration['div']['class'] = $divConfig['class'];
		}
		if( isset($labelConfig['class']) ) {
			$configuration['label']['class'] = $labelConfig['class'];
		}
		unset($fieldConfig['class']);
		unset($divConfig['class']);
		unset($labelConfig['class']);
		
		//Processando Id da div
		if( isset($divConfig['id']) ) {
			$configuration['div']['id'] = $divConfig['id'];
		}
		unset($divConfig['id']);
		
		//DEFINIR O FORMATO DE RENDERIZACAO
		$renderType = $field->getRenderType();
		if( isset($fieldConfig['renderType']) && is_string($fieldConfig['renderType']) && strlen($fieldConfig['renderType'])>0 ) {
			$renderType = $fieldConfig['renderType'];
			unset($fieldConfig['renderType']);
		}
		
		//Gerando os atributos para cada elemento
		$configuration['field']['attributes'] = self::getInputAttributesFromConfigArray($fieldConfig);
		$configuration['div']['attributes'] = self::getInputAttributesFromConfigArray($divConfig);
		$configuration['label']['attributes'] = self::getInputAttributesFromConfigArray($labelConfig);
		
		//Processando e renderizando field
		switch($renderType) {
			case 'textarea':
				break;
			case 'html':
				break;
			case 'file':
				break;
			case 'text':
			default:
				break;
		}
		
		var_dump($configuration);
		exit;
		
	}
	
	/**
	 * Método para efetuar a renderização de um field para um formulário no formato "BOX", "COMPLETO", ou seja, contendo a div que envolve e o label descritivo
	 * @param \Sh\DataSourceField $field
	 * @param string $value Valor aplicado ao campo
	 * @param array $fieldConfigUser Array de configurações para sobrescrever as configuracões do próprio field
	 * 			Serão aceitos valores como [ "label", "id", "name", "renderType", "required", "mask", "lowercase", "uppercase", "dpFilters", "..." ]
	 * 			Os "..." podem ser qualquer outro elemento, este será transformado em um atributo do html field, com id="value"
	 * @param array $otherConfig Array de configurações a ser utilizado para inserir attributos html dentro da div e label.
	 * 			Este deve possuir inicialmente duas chaves: "div", "label". Cada chave deve ser um array contendo os próprios atributos
	 * @return string
	 * 
	 * @deprecated Depreciado em favor do \Sh\RendererLibrary::renderInputBoxFromField
	 */
	static public function renderFieldBox (\Sh\DataSourceField $field, $value = null, $fieldConfigUser=array(), $otherConfig=array()) {
		
		//TRATAMENTO ESPECIAL PARA INPUT FILE
		if( $field->getDataType() == 'file' || $field->getDataType() == 'image' ) {
			return self::renderFieldFile($field, $value, $fieldConfigUser, $otherConfig);
		}
	
		//DETERMINANDO CONFIGURAÇÕES DA DIV E LABEL
		if( !is_array($otherConfig) ) {
			$otherConfig = array('div'=>array(), 'label'=>array());
		}
		if( !isset($otherConfig['div']) || !is_array($otherConfig['div']) ) {
			$otherConfig['div'] = array();
		}
		if( !isset($otherConfig['label']) || !is_array($otherConfig['label']) ) {
			$otherConfig['label'] = array();
		}
		
		//determinando show-labels para os tipos html e textarea
		if( $field->getDataType() == 'html' ) {
			if( !isset($otherConfig['label']['class']) ) {
				$otherConfig['label']['class'] = '';
			}
			$otherConfig['label']['class'] .= ' show-labels';
		}
		
		
		$divAttr = self::getInputAttributesFromConfigArray($otherConfig['div']);
		$labelAttr = self::getInputAttributesFromConfigArray($otherConfig['label']);
		
		//DETERMINANDO CONFIGURAÇÕES DO FIELD
		//FIXME, ESTE PASSO É UTILIZADO DUAS VEZES, TANTO AQUI QUANTO NO "RENDERFIELD"
		$tmp = self::parseUserRenderConfiguration($fieldConfigUser, $field);
		
	
		$html = '<div '.$divAttr.'>';
			$html .= '<label '.$labelAttr.' for="'.$tmp['final']['id'].'">'.$tmp['final']['label'].'</label>';
			$html .= self::renderField($field, $value, $fieldConfigUser, $otherConfig);
		$html .= '</div>';
	
		return $html;
	}
	
	/**
	 * Método para efetuar a renderização de um field para um formulário. Este irá produzir somente os inputs necessários, sem div e label
	 * @param \Sh\DataSourceField $field
	 * @param string $value Valor aplicado ao campo
	 * @param array $fieldConfigUser Array de configurações para sobrescrever as configuracões do próprio field
	 * 			Serão aceitos valores como [ "label", "id", "name", "renderType", "required", "mask", "lowercase", "uppercase", "dpFilters", "..." ]
	 * 			Os "..." podem ser qualquer outro elemento, este será transformado em um atributo do html field, com id="value"
	 * @return string
	 * 
	 * @deprecated Depreciado em favor do \Sh\RendererLibrary::renderInputFromField
	 */
	static public function renderField (\Sh\DataSourceField $field, $value = null, $fieldConfigUser=array()) {
		
		//TRATAMENTO ESPECIAL PARA INPUT FILE
		if( $field->getDataType() == 'file' || $field->getDataType() == 'image' ) {
			return self::renderFieldFile($field, $value, $fieldConfigUser, null);
		}
		
		$fieldConfigFinal = array();
		$html = '';
		
		//DETERMINANDO CONFIGURAÇÕES
		$tmp = self::parseUserRenderConfiguration($fieldConfigUser, $field);
		$fieldConfigFinal = $tmp['final'];
		$fieldConfigUser = $tmp['user'];
		
		//PRODUZINDO ATRIBUTOS ADICIONAIS AO FIELD
		{
			$attributes = '';
			//mascara
			if( $fieldConfigFinal['mask'] ) { $attributes .= 'mask="'.$fieldConfigFinal['mask'].'" '; }
			//uppercase e lowercase
			if( $fieldConfigFinal['uppercase'] ) { $attributes .= 'data-uppercase="uppercase" '; }
			else if( $fieldConfigFinal['lowercase'] ) { $attributes .= 'data-lowercase="lowercase" '; }
			//outros
			$attributes .= self::getInputAttributesFromConfigArray($fieldConfigUser);
			
		}
		
		//DETERMINANDO MODELO DE RENDERIZACAO E RENDERIZANDO
		//renderização com opções
		if( $field->hasOptions() ) {
			//CAPTURANDO AS OPÇÕES DISPONÍVEIS
			$options = $field::getOptionsDataFromConfig($field->getOptions(), $fieldConfigFinal['dpFilters']);
		
			//PRECISO DETERMINAR COMO IREMOS PRODUZIR O HTML
			//radio 
			if( $fieldConfigFinal['renderType'] == 'radio' ) {
				$html .= self::doHtmlRadio($options, $fieldConfigFinal['name'], $field::formatSheerDataToInput($value), null, null, $fieldConfigFinal['required']);
			}
			//checkbox
			else if ( $fieldConfigFinal['renderType'] == 'checkbox' ) {
				$html .= self::doHtmlCheckbox($options, $fieldConfigFinal['name'], $field::formatSheerDataToInput($value), null, null, $fieldConfigFinal['required']);
			}
			//select
			else {
				
				$html .= '<select id="'.$fieldConfigFinal['id'].'" name="'.$fieldConfigFinal['name'].'" '.$fieldConfigFinal['required'].' '.$attributes.'>';
					$html .= self::doHtmlSelectOptions($options, $field::formatSheerDataToInput($value), null, null, $fieldConfigFinal['blankOption']);
				$html .= '</select>';
			}
		}
		//RENDERIZANDO FIELDS SEM OPÇÕES
		else {
			//CAMPO HTML
			if( $field->getDataType() == 'html' ) {
				$html .= '<textarea id="'.$fieldConfigFinal['id'].'" name="'.$fieldConfigFinal['name'].'" '.$fieldConfigFinal['required'].' '.$attributes.' richtext="simple">'.$field::formatSheerDataToInput($value).'</textarea>';
			}
			//OUTROS
			else {
				$html .= '<input type="text" id="'.$fieldConfigFinal['id'].'" name="'.$fieldConfigFinal['name'].'" '.$fieldConfigFinal['required'].' value="'.$field::formatSheerDataToInput($value).'" '.$attributes.' />';
			}
			
				
		}
		
		return $html;
	}
	
	/**
	 * Método para efetuar a renderização de um field[type=file] para um formulário no formato "BOX", "COMPLETO", ou seja, contendo a div que envolve e o label descritivo
	 * @param \Sh\DataSourceField $field
	 * @param string $value Valor aplicado ao campo
	 * @param array $fieldConfigUser Array de configurações para sobrescrever as configuracões do próprio field
	 * 			Serão aceitos valores como [ "label", "id", "name", "renderType", "required", "mask", "lowercase", "uppercase", "dpFilters", "..." ]
	 * 			Os "..." podem ser qualquer outro elemento, este será transformado em um atributo do html field, com id="value"
	 * @param array $otherConfig Array de configurações a ser utilizado para inserir attributos html dentro da div e label.
	 * 			Este deve possuir inicialmente duas chaves: "div", "label". Cada chave deve ser um array contendo os próprios atributos
	 * @return string
	 */
	static protected function renderFieldFile (\Sh\DataSourceField $field, $value = null, $fieldConfigUser=array(), $otherConfig=array()) {
		
		//DETERMINANDO CONFIGURAÇÕES DA DIV E LABEL
		{
			if( !is_array($otherConfig) ) {
				$otherConfig = array('div'=>array(), 'label'=>array());
			}
			if( !isset($otherConfig['div']) || !is_array($otherConfig['div']) ) {
				$otherConfig['div'] = array();
			}
			if( !isset($otherConfig['label']) || !is_array($otherConfig['label']) ) {
				$otherConfig['label'] = array();
			}
			
			//determinando a classe especial "sh-input-file"
			if( isset($otherConfig['div']['class']) ) {
				$otherConfig['div']['class'] = $otherConfig['div']['class'].' sh-input-file';
			}
			else {
				$otherConfig['div']['class'] = 'sh-input-file';
			}
			
			//determinando show-labels para os tipos html e textarea
			if( !isset($otherConfig['label']['class']) ) {
				$otherConfig['label']['class'] = '';
			}
			$otherConfig['label']['class'] .= ' show-labels';
			
			
			$divAttr = self::getInputAttributesFromConfigArray($otherConfig['div']);
			$labelAttr = self::getInputAttributesFromConfigArray($otherConfig['label']);
		}
		
		//DETERMINANDO CONFIGURAÇÕES DO FIELD
		$tmp = self::parseUserRenderConfiguration($fieldConfigUser, $field);
		$fieldConfigFinal = $tmp['final'];
		$fieldConfigUser = $tmp['user'];
		
		//tratando o required especialmente caso o campo possua valor, se possuir valor o campo deixa de ser obrigatório
		if( is_array($value) ) {
			$fieldConfigFinal['required'] = '';
		}
		
		//DEFININDO ELEMENTOS DE VALOR ANTERIOR
		$currentText = '<a href="#">&nbsp;<!-- --></a>';
		$currentFile = '<input type="hidden" id="'.$fieldConfigFinal['id'].'_current" name="'.$fieldConfigFinal['name'].'_current" />';
		//fileDocument
		if( is_array($value) && $field->getDataType() == 'file' ) {
			$currentText = '<a href="'.$value['downloadLink'].'">'.$value['name'].'</a>';
			$currentFile = '<input type="hidden" id="'.$fieldConfigFinal['id'].'_current" name="'.$fieldConfigFinal['name'].'_current" required value="'.$value['id'].'" />';
		}
		else if ( is_array($value) && $field->getDataType() == 'image' ){
			$currentText = '<a href="'.$value['pictures']['sheer']['sh_image']['downloadLink'].'">'.$value['name'].'</a>';
			$currentFile = '<input type="hidden" id="'.$fieldConfigFinal['id'].'_current" name="'.$fieldConfigFinal['name'].'_current" required value="'.$value['id'].'" />';
		}
		
		$html = '';
		$html .= '<div '.$divAttr.'>';
			$html .= '<label '.$labelAttr.' for="'.$fieldConfigFinal['id'].'">'.$fieldConfigFinal['label'].'</label>';
			$html .= '<div>';
				$html .= '<span data-icon="f" data-input-trigger></span>';
				$html .= '<div>';
					$html .= $currentText;
					$html .= '<label><input type="checkbox" id="'.$fieldConfigFinal['id'].'_remove" name="'.$fieldConfigFinal['id'].'_remove" value="1" data-op="remove"  /> Remover arquivo</label>';
				$html .= '</div>';
				$html .= '<input type="file" id="'.$fieldConfigFinal['id'].'" name="'.$fieldConfigFinal['name'].'" '.$fieldConfigFinal['required'].' />';
				$html .= $currentFile;
			$html .= '</div>';
		$html .= '</div>';
		
		return $html;
	}
	
	/**
	 * Método para formar a string de attributos a ser utilizada no criação do nó input
	 * @param array $config
	 * @return string
	 */
	static protected function getInputAttributesFromConfigArray($config) {
		$attributes = '';
		
		if( $config ) {
			foreach ( $config as $k=>$v ) {
				$attributes .= $k.'="'.$v.'" ';
			}
		}
		
		return $attributes;
	}
	
	/**
	 * Método para determinar as configurações finais de renderização a partir do array de configurações passados pelo usuario
	 * @param array $userConfig
	 * @param \Sh\DataSourceField $field
	 * @return array( "final", "user" )
	 */
	static protected function parseUserRenderConfiguration ($userConfig, \Sh\DataSourceField $field) {
		
		$config = array(
			'final' => array(),
			'user' => array()
		);
		
		//label
		if( !isset($userConfig['label']) || !$userConfig['label'] ) { $config['final']['label'] = $field->getName(); }
		else { $config['final']['label'] = $userConfig['label']; }
		
		//id
		if( !isset($userConfig['id']) || !$userConfig['id'] ) { $config['final']['id'] = $field->getId(); }
		else { $config['final']['id'] = $userConfig['id']; }
		
		//name
		if( !isset($userConfig['name']) || !$userConfig['name'] ) { $config['final']['name'] = $field->getId(); }
		else { $config['final']['name'] = $userConfig['name']; }
		
		//formato de renderização
		$optionsConfig = $field->getOptions();
		if( isset($userConfig['renderType']) && $userConfig['renderType'] ) {
			$config['final']['renderType'] = $userConfig['renderType'];
		}
		else {
			$config['final']['renderType'] = $optionsConfig['renderType'];
		}
		
		//blankoption
		if( !isset($userConfig['blankOption']) ) {
			$config['final']['blankOption'] = null;
		}
		else {
			$config['final']['blankOption'] = $userConfig['blankOption'];
		}
		
		
		//required
		//se tivermos determinação de required utilizamos ela
		if( isset($userConfig['required']) ) {
			if( !!$userConfig['required'] ) { $config['final']['required'] = 'required="required"'; }
			else { $config['final']['required'] = ''; }
		}
		//se nao tiver determinacao
		else {
			if( $field->getRequired() ) { $config['final']['required'] = 'required="required"'; }
			else { $config['final']['required'] = ''; }
		}
		
		//mascara
		if( !isset($userConfig['mask']) || !$userConfig['mask'] ) {
			$config['final']['mask'] = $field->getMask();
		}
		else { $config['final']['mask'] = $userConfig['mask']; }
		
		//uppercase
		if( !isset($userConfig['uppercase']) || $userConfig['uppercase'] === null ) {
			$config['final']['uppercase'] = $field->getUpperCase();
		}
		else { $config['final']['uppercase'] = $userConfig['uppercase']; }
		
		//lowercase
		if( !isset($userConfig['lowercase']) || $userConfig['lowercase'] === null ) {
			$config['final']['lowercase'] = $field->getLowerCase();
		}
		else { $config['final']['lowercase'] = $userConfig['lowercase']; }
		
		//Verificando filtros para o dataProvider
		$config['final']['dpFilters'] = array();
		if( isset($userConfig['dpFilters']) && $userConfig['dpFilters'] ) {
			$config['final']['dpFilters'] = $userConfig['dpFilters'];
		}
		
		//removo as opções tratadas
		unset($userConfig['label']);
		unset($userConfig['id']);
		unset($userConfig['name']);
		unset($userConfig['required']);
		unset($userConfig['mask']);
		unset($userConfig['uppercase']);
		unset($userConfig['lowercase']);
		unset($userConfig['renderType']);
		unset($userConfig['blankOption']);
		unset($userConfig['dpFilters']);
		
		$config['user'] = $userConfig;
		return $config;
		
	}
	
	/**
	 * Método gerador de string html a ser utilizada como select de um formulário
	 * @param array($id, $valor) $data
	 * @param string $fieldid Identificador e name do input
	 * @param string $selected	Identificador do conteudo selecionado
	 * @param string $keyname
	 * @param string $valuename
	 * @param string $blankOption
	 * @return string
	 * 
	 * TODO FAZER A FUNÇÃO INTERPRETAR OS PARAMETROS
	 * 
	 */
	static public function doHtmlSelect($data, $fieldid, $selected=null, $keyname=null, $valuename=null, $blankOption = null, $params = array()) {
	
		$html = '<select id="'.$fieldid.'" name="'.$fieldid.'">';
			$html .= self::doHtmlSelectOptions($data, $selected, $keyname, $valuename, $blankOption);
		$html .= '</select>';
	
		return $html;
	}
	
	
	/**
	 * Método gerador de string html a ser utilizada como options para um select
	 * 
	 * @param array($id, $valor) $data
	 * @param string $selected
	 * @param string $keyname
	 * @param string $valuename
	 * @param string $blankOption
	 * @return string
	 */
	static public function doHtmlSelectOptions($data, $selected, $keyname=null, $valuename=null, $blankOption = null) {
		$html = '';
		
		//se devemos inserir uma opção inválida
		if($blankOption) {
			$html .= '<option value="">'.$blankOption.'</option>';
		}
	
		if($data) {
				
			foreach($data as $k=>$v) {
	
				$key = $k;
				$value = $v;
	
				if(isset($keyname)) { $key = $v[ $keyname ]; }
				if(isset($valuename)) { $value = $v[$valuename]; }
	
				$s='';
				if( $selected !== null && $key !== null && $selected == $key ) { $s='selected="selected"'; }
				$html .= '<option value="'.$key.'" id="'.$key.'" '.$s.'>'.$value.'</option>';
			}
		}
		
		return $html;
	}
	
	
	/**
	 * Método gerador de string html a ser utilizada como opções me forma de radio
	 * Este método não considera BlankOption
	 * 
	 * @param array $data
	 * @param string $idField
	 * @param string $selected
	 * @param string $keyname
	 * @param string $valuename
	 * @param string $attributes String direta para ser inserida como attributo dos radios
	 * @return string
	 */
	static public function doHtmlRadio($data, $idField, $selected=null, $keyname=null, $valuename=null, $attributes='') {
		$html = '';
		
		if($data) {
	
			foreach($data as $k=>$v) {
	
				$key = $k;
				$value = $v;
	
				if(isset($keyname)) { $key = $v[ $keyname ]; }
				if(isset($valuename)) { $value = $v[$valuename]; }
	
				$s='';
				if( $selected !== null && $key !== null && $selected == $key ) { $s='checked="checked"'; }
				
				$html .= '<label class="sh-radio">';
					$html .= '<input type="radio" id="'.$idField.'_'.$key.'" name="'.$idField.'" '.$s.' value="'.$key.'" '.$attributes.'>';
					$html .= $value;
				$html .= '</label>';
			}
		}
	
		return $html;
	}

	
	
	/**
	 * Método gerador de string html a ser utilizada como opções me forma de checkbox
	 * Este método não considera BlankOption
	 * 
	 * @param array $data
	 * @param string $idField
	 * @param string $selected
	 * @param string $keyname
	 * @param string $valuename
	 * @param string $attributes String direta para ser inserida como attributo dos radios
	 * @return string
	 */
	static public function doHtmlCheckbox($data, $idField, $selected=null, $keyname=null, $valuename=null, $attributes='') {
		$html = '';
	
		if($data) {
	
			foreach($data as $k=>$v) {
	
				$key = $k;
				$value = $v;
	
				if(isset($keyname)) { $key = $v[ $keyname ]; }
				if(isset($valuename)) { $value = $v[$valuename]; }
	
				$s='';
				//verificando valor atual
				if( $selected !== null && $key !== null ) {
					if( is_array($selected) ) {
						if( isset($key, $selected) && !!$selected[$key] ) {
							$s='checked="checked"';
						}
					}
					else if ( $selected == $key ) {
						$s='checked="checked"';
					}
				}
				
				$html .= '<label class="sh-checkbox">';
					$html .= '<input type="checkbox" id="'.$idField.'_'.$key.'" name="'.$idField.'[]" '.$s.' value="'.$key.'" '.$attributes.'>';
					$html .= $value;
				$html .= '</label>';
			}
		}
	
		return $html;
	}
	
	/**
	 * Retorna o html do bloco vazio para tabelas
	 * 
	 * @param string $texto Texto a ser exibido como vazio no holder
	 * @return string
	 */
	static public function getEmptyHolderHtml ($texto="Nenhum resultado encontrado") {
		$html = '';
		
		$html = '<div class="data-center">';
			$html .= '<p data-icon="g" style="font-size: 1.3em;"></p>';
			$html .= '<p style="text-transform: uppercase">'.$texto.'</p>';
		$html .= '</div>';
		return $html;
	}
	
}