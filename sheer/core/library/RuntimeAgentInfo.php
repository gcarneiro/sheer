<?php

namespace Sh;

/**
 * Sheer Plataform Version 0.5.0
 * @author Guilherme
 * Oct 6, 2010
 *
 * Classe para pegar informações precisas sobre o browser e sistema operacional do cliente.
 */
abstract class RuntimeAgentInfo {

	static protected $initialized 	= false;
    
	static protected  $browserName		= 'unknown';
    static protected  $browserVersion		= '0.0.0';
    static protected  $osName				= 'unknown';
    static protected  $agent				= 'unknown';
    
    static public function init () {
    	if( self::$initialized ) { return true; }
    	
    	self::$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    	self::setBrowserInfo();
    	self::setOSInfo();
    	
    	self::$initialized = true;
    	return true;
    	
    }

    static protected function setBrowserInfo() {
    	$browsers	= array("firefox", "msie", "opera", "chrome", "safari", "mozilla", "seamonkey", "konqueror", "netscape", 
                            "gecko", "navigator", "mosaic", "lynx", "amaya", "omniweb", "avant", "camino", "flock", "aol");
    	
    	foreach($browsers as $browser) { 
            if ( preg_match("#($browser)[/ ]?([0-9.]*)#", self::$agent, $match) ) { 
                self::$browserName 		= $match[1] ; 
                self::$browserVersion 	= $match[2] ;
                break;
            } 
        }
    }
    
    /**
     * @author Guilherme
     * Oct 6, 2010
     * 
     * Define informações sobre o Sistema Operacional do cliente
     * @return string
     */
    static private function setOSInfo() {
    	$OS			= null;

		if(strpos(self::$agent,"windows nt 5.1") !== false)			{ $OS = 'Windows XP'; }
		elseif ( strpos(self::$agent,"windows nt 6.0") !== false )	{ $OS = 'Windows Vista'; }
		elseif ( strpos(self::$agent,"windows nt 6.1") !== false )	{ $OS = 'Windows 7'; }
		elseif ( strpos(self::$agent,"windows 98") !== false )		{ $OS = 'Windows 98'; }
		elseif ( strpos(self::$agent,"windows nt 5.0") !== false )	{ $OS = 'Windows 2000'; }
		elseif ( strpos(self::$agent,"windows nt 5.2") !== false )	{ $OS = 'Windows 2003 server'; }
		elseif ( strpos(self::$agent,"windows nt 6.0") !== false )	{ $OS = 'Windows Vista'; }
		elseif ( strpos(self::$agent,"windows nt") !== false )		{ $OS = 'Windows NT'; }
		elseif ( strpos(self::$agent,"win 9x 4.90") !== false && strpos(self::$agent,"win me") )	{ $OS = 'Windows ME'; }
		elseif ( strpos(self::$agent,"win ce") !== false )			{ $OS = 'Windows CE'; }
		elseif ( strpos(self::$agent,"win 9x 4.90") !== false )		{ $OS = 'Windows ME'; }
		elseif ( strpos(self::$agent,"iphone") !== false )			{ $OS = 'iPhone'; }
		elseif ( strpos(self::$agent,"mac os x") !== false )			{ $OS = 'Mac OS X'; }
		elseif ( strpos(self::$agent,"macintosh") !== false )			{ $OS = 'Macintosh'; }
		elseif ( strpos(self::$agent,"linux") !== false )				{ $OS = 'Linux'; }
		elseif ( strpos(self::$agent,"freebsd") !== false )			{ $OS = 'Free BSD'; }
		elseif ( strpos(self::$agent,"symbian") !== false )			{ $OS = 'Symbian'; }
	
		self::$osName = $OS;
    }
    
    static public function getBrowserName() {
    	return self::$browserName;
    }
    static function getBrowserVersion() {
    	return self::$browserVersion;
    }
    static function getBrowserFullName() {
    	return self::$browserName.' '.self::$browserVersion;
    }
    static function getAgent() {
    	return self::$agent;
    }
    static function getOsName() {
    	return self::$osName;
    }
}
