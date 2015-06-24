<?php

namespace Sh {
	
	/**
	 * Sheer Plataform Version 0.0.1
	 * @author Guilherme
	 * 24/08/2010
	 *
	 * Classe Genérica de Logger.
	 */
	class Logger {
		
		protected $id = null;
		protected $name = null;
		protected $file = null;
		protected $maxFileSize = null;
		
		public function __construct($values) {
			if( !$this->isValid($values) ) {
				return false;
			}
			
			$this->id = trim($values['id']);
			$this->name = trim($values['name']);
			$this->file = SH_LOG_PATH.trim($values['file']);
			$this->maxFileSize = ((integer) $values['maxFileSize'])*1024;
			
			$this->createFile();
			
			return $this;
		}
		
		/**
		 * @author Guilherme
		 * 24/08/2010
		 *
		 * Enter description here ...
		 * @param array $values
		 * @return boolean
		 */
		private function isValid($values) {
			if(!isset($values['id']) && strlen(trim($values['id'])) == 0) {
				return false;
			}
			else if(!isset($values['file']) && strlen(trim($values['file'])) == 0) {
				return false;
			}
			else return true;
		}
		
		/**
		 * Create log file in case of not exists
		 */
		public function createFile() {
			if(!is_file($this->file)) {
				$path = preg_split('/\//', $this->file);
				$curpath = $path[0].'/';
				for ($i=1; $i<(count($path)-1);$i++) {
					$curpath = $curpath.$path[$i].'/';
					if(!is_dir($curpath)) {
						mkdir($curpath);
						chmod($curpath, 0777);
					}
				}
				$file = fopen($this->file, 'a');
				chmod($this->file, 0777);
				fwrite($file, "SHEER 1.0\n");
				fclose($file);
			}
		}
		
		/**
		 * This will write in file an log message
		 * @param string $message
		 * @param boolean $simpleMessage Quando true, vamos gravar simplismente a mensagem passada
		 */
		public function log($message, $simpleMessage=false) {
			//verificando o tamanho do log e o repartindo caso necessário
			$this->verifySize();
			
			if( !$simpleMessage ) {
				$newMessage = '#'.date('Y-m-d H:i:s')." => ".$message;
				$newMessage .= "\n";
			}
			else {
				$newMessage = $message."\n";
			}
			
			
			
			$file = fopen($this->file, 'a');
			fwrite($file, $newMessage);
			fclose($file);
		}
		
		/**
		 * @author Guilherme
		 * 24/08/2010
		 *
		 * This will verify if log file is on acceptable size. If it is bigger, rename the file and create a new one.
		 */
		public function verifySize() {
			$size = filesize($this->file);
			if($size > $this->maxFileSize) {
				$newname = $this->file.'.'.date('omd.Gis');
				rename($this->file, $newname);
				$this->createFile();
			}
		}
		
		
		public function getId() {
			return $this->id;
		}
		public function getName() {
			return $this->name;
		}
		public function getFile() {
			return $this->file;
		}
		public function getMaxFileSize() {
			return $this->maxFileSize;
		}
	}
}