<?php
	namespace gOPF\Kwejk;
	use \System\Terminal\Help\Line;
	use \gOPF\Kwejk;
	use \reCaptcha\reCaptcha;
	use \System\Terminal\Exception;
	
	/**
	 * Terminal command: build (allows to use KwejkAPI without web interface)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class kwejkCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * Cookie name for Kwejk session
		 * @var string
		 */
		const COOKIE_NAME = 'kwejkCommandCookie';
		
		/**
		 * Command storage
		 * @var array
		 */
		private $storage = array();
		
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$lines = array();
			$help = new \System\Terminal\Help('KwejkAPI interface command');
			
			$lines[] = new Line('kwejk -status', 'show Kwejk session status');
			$lines[] = new Line('kwejk -login -username [username] -password [password]', 'authorize user');
			$lines[] = new Line('kwejk -proxy [ip:port]', 'set proxy connection IP');
			$lines[] = new Line('kwejk -captcha', 'generate and store single captcha for future use');
			$lines[] = new Line('kwejk -upload -file [system path] -title [title]', 'upload image');
			$lines[] = new Line('kwejk -ip', 'show API session IP');
			
			$help->addLines($lines);
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = self::$session;
			$this->readStorage();
			
			try {
				if ($this->getParameter('login')) {
					$this->kwejk()->login($this->getParameter('username'), $this->getParameter('password'));
				}
				
				if ($this->getParameter('status')) {
					$logged = $this->kwejk()->isLogged();
					$output = 'Logged as: '.(empty($logged) ? 'not authorized' : $logged)."\n";
					$output .= 'Captchas: '.count($this->storage['captchas']);
					
					self::$session->buffer($output);
				}
				
				if ($this->getParameter('upload')) {
					$this->kwejk()->sendImage($this->getCaptcha(), $this->getParameter('file'), $this->getParameter('title'));
				}
				
				if ($this->getParameter('logout')) {
					$this->kwejk()->logout();
				}
			} catch (\gOPF\Kwejk\Exception $e) {
				self::$session->buffer($e->getMessage());
			}
			
			if ($this->getParameter('captcha')) {
				$key = reCaptcha::getCaptchaChallenge(Kwejk::CAPTCHA_KEY);
				$this->storage['resolving'] = $key;

				$status = $session->pull();
				$status->prompt = 'Resolve captcha: ';
				$status->prefix = 'kwejk -resolve ';
				$status->buffer('<img src="'.reCaptcha::getCaptchaImage($key).'" />');
				$session->push($status);
			}
			
			if ($this->getParameter('resolve')) {
				if (!isset($this->storage['captchas'])) {
					$this->storage['captchas'] = array();
				}
				
				$this->storage['captchas'][] = $this->storage['resolving'].':'.$this->getParameter('resolve');
				unset($this->storage['resolving']);
				
				$status = $session->pull();
				$status->prompt = null;
				$status->prefix = null;
				$session->push($status);
			}
			
			if ($this->getParameter('proxy')) {
				if ($this->getParameter('proxy') === true) {
					unset($this->storage['proxy']);
				}
				
				$this->storage['proxy'] = $this->getParameter('proxy');
			}
			
			if ($this->getParameter('ip')) {
				$session->buffer($this->kwejk()->ip());
			}
		}
		
		/**
		 * Save command storage
		 */
		public function __destruct() {
			$this->writeStorage();
		}
		
		/**
		 * Initialize Kwejk API instance
		 * 
		 * @return \gOPF\Kwejk Kwejk API instance
		 */
		private function kwejk() {
			if (isset($this->storage['proxy'])) {
				$proxy = $this->storage['proxy'];
			} else {
				$proxy = '';
			}
			
			return new Kwejk(__VARIABLE_PATH.DIRECTORY_SEPARATOR.self::COOKIE_NAME, $proxy, false, false, true);
		}
		
		/**
		 * Generate captcha from storage
		 * 
		 * @return \gOPF\Kwejk\Captcha Solved captcha
		 */
		private function getCaptcha() {
			if (count($this->storage['captchas']) == 0) {
				throw new Exception('There is no captchas available');
			}
			
			$last = array_pop($this->storage['captchas']);
			$exploded = explode(':', $last);
			
			return new \gOPF\Kwejk\Captcha($exploded[0], $exploded[1]);
		}
		
		/**
		 * Read command storage
		 */
		private function readStorage() {
			$status = self::$session->pull();
			
			if (isset($status->storage[__CLASS__])) {
				$this->storage = $status->storage[__CLASS__];
			} else {
				$this->storage = array();
			}
		}
		
		/**
		 * Write command storage
		 */
		private function writeStorage() {
			$status = self::$session->pull();
			$status->storage[__CLASS__] = $this->storage;
			self::$session->push($status);
		}
	}
?>