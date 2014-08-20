<?php
	namespace System;
	
	/**
	 * Error page generator
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Error {
		/**
		 * Number of lines to print in error page
		 * @var int
		 */
		const LINES = 5;
		
		/**
		 * Show files content on error page
		 * @var int
		 */
		const FILES = true;
		
		/**
		 * Thrown exception container
		 * @var \Exception
		 */
		private $exception;
		
		/**
		 * Constructor of error page
		 * 
		 * @param \Exception $exception
		 */
		public function __construct(\Exception $exception) {
			ob_end_clean();
			
			$this->exception = $exception;
			$customPath = __APPLICATION_PATH.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR;
			
			$this->setLog();
			$this->sendHeader();
			
			if (isset($exception->HTTP) && is_file($customPath.$exception->HTTP.'.php')) {
				$this->displayCustomErrorPage($customPath.$exception->HTTP.'.php');
			} else {
				if (\System\Core::STAGE == __DEVELOPMENT) {
					$this->displayFrameworkErrorPage();
				}
			}
		}
		
		/**
		 * Displays custom error page
		 * 
		 * @param string $path Custom error page file
		 */
		private function displayCustomErrorPage($path) {
			include $path;
		}
		
		/**
		 * Displays framework error page
		 */
		private function displayFrameworkErrorPage() {
			$view = \System\View::instance();
			$view->setFrame(__SYSTEM_PATH.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'error.php');
			
			$this->fillTemplate();
			$view->render();
		}
		
		/**
		 * Sends specific header for the web browser
		 */
		private function sendHeader() {
			if (!isset($this->exception->HTTP)) {
				$this->exception->HTTP = 500;
			}
			
			switch ($this->exception->HTTP) {
				case 401:
					header('HTTP/1.1 401 Unauthorized');
					break;
		
				case 404:
					header('HTTP/1.1 404 Not Found');
					break;
						
				case 500:
					header('HTTP/1.1 500 Internal Server Error');
					break;
			}
		}
		
		/**
		 * Fills error page template
		 */
		private function fillTemplate() {
			$trace = $this->exception->getTrace();
			$parameters = '';
			
			if (count($trace[0]['args']) > 0) {
				foreach ($trace[0]['args'] as $parameter) {
					$parameters .= (is_array($parameter) ? 'Array' : (is_object($parameter) ? get_class($parameter) : $parameter)).', ';
				}
			}
			
			$whileExecuting = (isset($trace[0]['file']) ? self::preparePath($trace[0]['file']).' ['.$trace[0]['line'].'] » ' : '').$trace[0]['class'].$trace[0]['type'].$trace[0]['function'].'('.substr($parameters, 0, -2).')';
			
			View::factorySection('httpErrorCode', null)->setContent($this->exception->HTTP);
			View::factorySection('errorMessage', null)->setContent($this->exception->getMessage());
			View::factorySection('whileExecuting', null)->setContent($whileExecuting);
			
			if (isset($trace[0]['file'])) {
				View::factorySection('contentFile', null)->setContent(self::preparePath($trace[0]['file']).' [ '.$trace[0]['line'].' ]');
				View::factorySection('content', null)->setContent($this->getContent($trace[0]['file'], $trace[0]['line']));
			}
			
			View::factorySection('exceptionFile', null)->setContent(self::preparePath($this->exception->getFile()).' [ '.$this->exception->getLine().' ]');
			View::factorySection('exception', null)->setContent($this->getContent($this->exception->getFile(), $this->exception->getLine()));
		}
		
		/**
		 * Returns part of file, where exception has been thrown
		 *
		 * @param string $file File name
		 * @param integer $errorLine Line number
		 * @return string File content
		 */
		private function getContent($file, $errorLine) {
			if (empty($file)) {
				return false;
			}
				
			$begin = $errorLine - self::LINES;
			$end = $errorLine + self::LINES;
				
			$code = array();
			$return = '';
				
			$lines = 1;
				
			foreach (file($file) as $line) {
				$code[$lines++] = $line;
			}
				
			for ($current=$begin; $current<=$end; $current++) {
				if (!isset($code[$current])) {
					continue;
				}
				
				$class = '';
				
				if ($current <= 0) {
					continue;
				}
		
				if ($current == $errorLine) {
					$class = ' highlight';
				}
		
				$return .= '<span class="line'.$class.'"><span class="number">'.$current.'</span> '.str_replace("\t", '  ', $code[$current]).'</span>';
		
				unset($class);
			}
				
			return $return;
		}
		
		/**
		 * Sets log with error message
		 */
		private function setLog() {
			new Log($this->exception->getMessage(), Log::ERROR, Log::SYSTEM);
		}
		
		/**
		 * Protects sensitive data from printing
		 * 
		 * @param string $path
		 * @return string Protected path
		 */
		private static function preparePath($path) {
			return str_replace(array(__APPLICATION_PATH, __SYSTEM_PATH), array('/application', '/system'), $path);
		}
	}
?>