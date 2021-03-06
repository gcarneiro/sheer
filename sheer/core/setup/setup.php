<?php

//DETERMINANDO ROOT PATH DO SHEER
if( isset($sheerRootPath) && is_dir($sheerRootPath) ) {
	//colocando a barra no final
	if( strrpos($sheerRootPath, '/') != strlen($sheerRootPath)-1 ) {
		$sheerRootPath = $sheerRootPath.'/';
	}
	define('SH_ROOT_PATH', $sheerRootPath);
}
else {
	define('SH_ROOT_PATH', '../sheer/');
}

//Primeiramente inicio a biblioteca geral de funções
require_once SH_ROOT_PATH.'/core/library/Library.php';

//Marcando execução por linha de comando
if( !defined('SH_CLI') ) {
	define('SH_CLI', false);
}
//caso não seja uma execução por linha verifico o www e removo
if ( !SH_CLI ) {
	//Verifico se a url possui www e caso sim o removo
	\Sh\Library::relocationNoWWW();
}


/*
 * DEFINIÇÕES DO SHEER
*/

define('SH_CORE_PATH', 				SH_ROOT_PATH.'core/');
define('SH_SETUP_PATH', 			SH_CORE_PATH.'setup/');

//DEFINIÇÕES DE LOG
define('SH_LOG_PATH', SH_ROOT_PATH.'log/');
define('SH_LOG_CONFIG_FILE', SH_SETUP_PATH.'global/loggers.xml');

//DEFINIÇÕES GERAIS DO SHEER
define('SH_RENDERER_STYLES_PATH', SH_CORE_PATH.'renderer/styles/');
define('SH_MODULE_PATH', SH_CORE_PATH.'src/modulos/');
define('SH_NAVIGATION_PATH', SH_CORE_PATH.'src/navigation/');
define('SH_TEMPLATES_PATH', SH_CORE_PATH.'src/templates/');

define('SH_PUBLIC_DATA', './data/');
define('SH_PUBLIC_DATA_FILES', SH_PUBLIC_DATA.'files/');
define('SH_PUBLIC_DATA_PICTURES', SH_PUBLIC_DATA.'pictures/');

define('SH_LIBRARY_PATH', SH_CORE_PATH.'library/');
define('SH_BOLETOPHP_PATH', SH_LIBRARY_PATH.'boletophp/');

/*
 * DEFINIÇÕES DO PROJETO
*/
//setup
define('SH_PROJECT_PATH', SH_ROOT_PATH.'project/');
define('SH_PROJECT_SETUP_PATH', SH_PROJECT_PATH.'setup/');
define('SH_PROJECT_DATA_PATH', SH_PROJECT_PATH.'data/');
define('SH_PROJECT_CONFIG_JSON', SH_PROJECT_SETUP_PATH.'config.json');
define('SH_PROJECT_MODULE_PATH', SH_PROJECT_PATH.'src/modulos/');
//navegacao
define('SH_PROJECT_STYLES_PATH', SH_PROJECT_PATH.'src/styles/');
define('SH_PROJECT_TEMPLATES_PATH', SH_PROJECT_PATH.'src/templates/');
define('SH_PROJECT_NAVIGATION_PATH', SH_PROJECT_PATH.'src/navigation/');
//log
define('SH_PROJECT_LOG_CONFIG_FILE', SH_PROJECT_SETUP_PATH.'loggers.xml');

//verificando marcador do cli
if( !defined('SH_CLI') && isset($_GET['shcli']) && $_GET['shcli']=='1' ) {
	define('SH_CLI', true);
}


/*
 * IMPORTACOES SHEER
 */
require_once SH_SETUP_PATH.'SheerSetup.php';
require_once SH_ROOT_PATH.'core/logger/Logger.php';
require_once SH_ROOT_PATH.'core/logger/LoggerProvider.php';
require_once SH_ROOT_PATH.'core/exception/SheerException.php';
require_once SH_ROOT_PATH.'core/exception/ActionException.php';
require_once SH_ROOT_PATH.'core/exception/DatabaseException.php';
require_once SH_ROOT_PATH.'core/exception/FatalErrorException.php';

require_once SH_ROOT_PATH.'core/module/ModuleFactory.php';
require_once SH_ROOT_PATH.'core/module/compiler/ModuleCompiler.php';
require_once SH_ROOT_PATH.'core/module/compiler/DataSourceCompiler.php';
require_once SH_ROOT_PATH.'core/module/compiler/DataSourceFieldCompiler.php';
require_once SH_ROOT_PATH.'core/module/compiler/DataProviderCompiler.php';
require_once SH_ROOT_PATH.'core/module/compiler/RenderableCompiler.php';
require_once SH_ROOT_PATH.'core/module/compiler/ActionHandlerCompiler.php';
require_once SH_ROOT_PATH.'core/module/Module.php';
require_once SH_ROOT_PATH.'core/module/Renderable.php';
require_once SH_ROOT_PATH.'core/module/renderable/RenderableDataSource.php';
require_once SH_ROOT_PATH.'core/module/renderable/RenderableDataProvider.php';
require_once SH_ROOT_PATH.'core/module/renderable/RenderableStyle.php';
require_once SH_ROOT_PATH.'core/module/DataSource.php';
require_once SH_ROOT_PATH.'core/module/datasource/DataSourceField.php';
require_once SH_ROOT_PATH.'core/module/DataProvider.php';
require_once SH_ROOT_PATH.'core/module/ActionHandler.php';
require_once SH_ROOT_PATH.'core/module/ModuleControl.php';

require_once SH_ROOT_PATH.'core/renderer/RendererLibrary.php';
require_once SH_ROOT_PATH.'core/renderer/Renderer.php';
require_once SH_ROOT_PATH.'core/renderer/RendererManager.php';

require_once SH_ROOT_PATH.'core/image/ImageLibrary.php';
require_once SH_ROOT_PATH.'core/image/GenericImageProcessor.php';
require_once SH_ROOT_PATH.'core/image/ImageProcessor.php';

require_once SH_ROOT_PATH.'core/jobs/GenericJob.php';
require_once SH_ROOT_PATH.'core/jobs/JobRunner.php';

require_once SH_ROOT_PATH.'core/content/ContentActionManager.php';
require_once SH_ROOT_PATH.'core/content/GenericContentProvider.php';
require_once SH_ROOT_PATH.'core/content/ContentProvider.php';
require_once SH_ROOT_PATH.'core/content/ContentProviderManager.php';
require_once SH_ROOT_PATH.'core/content/dataParser/GenericDataParser.php';
require_once SH_ROOT_PATH.'core/content/queryGenerator/select/Select.php';
require_once SH_ROOT_PATH.'core/content/queryGenerator/select/FilterProcessor.php';

require_once SH_LIBRARY_PATH.'LibraryValidation.php';
require_once SH_LIBRARY_PATH.'LibraryLifetime.php';
require_once SH_LIBRARY_PATH.'PathResolver.php';
require_once SH_LIBRARY_PATH.'RuntimeVariables.php';
require_once SH_LIBRARY_PATH.'ProjectConfig.php';
require_once SH_LIBRARY_PATH.'DatabaseConnectionProvider.php';
require_once SH_LIBRARY_PATH.'DatabaseManager.php';
require_once SH_LIBRARY_PATH.'ContentLogCollector.php';
require_once SH_LIBRARY_PATH.'UUID.php';
require_once SH_LIBRARY_PATH.'RuntimeInfo.php';
require_once SH_LIBRARY_PATH.'RuntimeAgentInfo.php';
require_once SH_LIBRARY_PATH.'Events.php';
require_once SH_LIBRARY_PATH.'cielo/Cielo.php';
require_once SH_LIBRARY_PATH.'Facebook/shFacebook.php';
require_once SH_LIBRARY_PATH.'Facebook/autoload.php';

require_once SH_ROOT_PATH.'core/mailer/MailerProvider.php';
require_once SH_ROOT_PATH.'core/mailer/phpmailer/class.phpmailer.php';
require_once SH_ROOT_PATH.'core/mailer/phpmailer/class.pop3.php';
require_once SH_ROOT_PATH.'core/mailer/phpmailer/class.smtp.php';

require_once SH_ROOT_PATH.'core/module/datasource/field/FieldString.php';
require_once SH_ROOT_PATH.'core/module/datasource/field/FieldEmail.php';
require_once SH_ROOT_PATH.'core/module/datasource/field/FieldText.php';
require_once SH_ROOT_PATH.'core/module/datasource/field/FieldHtml.php';

require_once SH_ROOT_PATH.'core/module/datasource/field/FieldInteger.php';
require_once SH_ROOT_PATH.'core/module/datasource/field/FieldFloat.php';
require_once SH_ROOT_PATH.'core/module/datasource/field/FieldDecimal.php';
require_once SH_ROOT_PATH.'core/module/datasource/field/FieldDinheiro.php';

require_once SH_ROOT_PATH.'core/module/datasource/field/FieldDate.php';
require_once SH_ROOT_PATH.'core/module/datasource/field/FieldDateTime.php';

require_once SH_ROOT_PATH.'core/module/datasource/field/FieldFile.php';
require_once SH_ROOT_PATH.'core/module/datasource/field/FieldImage.php';


require_once SH_ROOT_PATH.'core/action/GenericAction.php';
require_once SH_ROOT_PATH.'core/action/AddAction.php';
require_once SH_ROOT_PATH.'core/action/queryGenerator/Add.php';
require_once SH_ROOT_PATH.'core/action/UpdateAction.php';
require_once SH_ROOT_PATH.'core/action/queryGenerator/Update.php';
require_once SH_ROOT_PATH.'core/action/DeleteAction.php';
require_once SH_ROOT_PATH.'core/action/queryGenerator/Delete.php';

require_once SH_ROOT_PATH.'core/accessControl/session/SessionControl.php';
require_once SH_ROOT_PATH.'core/accessControl/authentication/AuthenticationControl.php';

require_once SH_ROOT_PATH.'core/request/page/PageRequest.php';
require_once SH_ROOT_PATH.'core/request/page/PageProcessor.php';
require_once SH_ROOT_PATH.'core/request/page/PageHolderProcessor.php';
require_once SH_ROOT_PATH.'core/request/dataProvider/DataProviderRequest.php';
require_once SH_ROOT_PATH.'core/request/action/ActionRequest.php';
require_once SH_ROOT_PATH.'core/request/renderer/RendererRequest.php';

//INICIANDO PROCESSAMENTO
\Sh\SheerSetup::init();

require_once SH_ROOT_PATH.'project/setup/general.php';
require_once SH_ROOT_PATH.'project/setup/tempoVidaControlador.php';



