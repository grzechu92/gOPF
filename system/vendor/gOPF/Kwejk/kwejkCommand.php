<?php
	namespace gOPF\Kwejk;
	use \gOPF\Kwejk;
	use \reCaptcha\reCaptcha;
	
	class kwejkCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = self::$session;
			
			if ($this->getParameter('login')) {
				$k = new Kwejk(__VARIABLE_PATH.'/kwejkCommandCookie', '', false, false);
				$k->login($this->getParameter('username'), $this->getParameter('password'));
			}
			
			if ($this->getParameter('request')) {
				$session->buffer(htmlentities($k->sendRequest($this->getParameter('request'))));
			}
			
			if ($this->getParameter('captcha')) {
				$key = reCaptcha::getCaptchaChallenge(Kwejk::CAPTCHA_KEY);
				$image = reCaptcha::getCaptchaImage($key);
				$status = $session->pull();
				
				$status->storage['captcha'] = $key;
				$status->prompt = 'Resolve captcha: ';
				$status->prefix = 'kwejk -resolve ';
				$status->buffer('<img src="'.$image.'" />');
				
				$session->push($status);
			}
			
			if ($this->getParameter('resolve')) {
				$status = $session->pull();

				$status->storage['captcha'] .= ':'.$this->getParameter('resolve');
				$status->prompt = null;
				$status->prefix = null;
				$status->buffer($this->getParameter('resolve'));
								
				$session->push($status);
			}
			
			if ($this->getParameter('stored')) {
				$session->buffer($session->pull()->storage['captcha']);
			}
			
			if ($this->getParameter('upload')) {
				$k = new Kwejk(__VARIABLE_PATH.'/kwejkCommandCookie', '', false, false);
				list($challengeID, $solved) = explode(':', $session->pull()->storage['captcha']);
				
				$captcha = new Captcha($challengeID, $solved);
				$k->sendImage($captcha, $this->getParameter('file'), $this->getParameter('title'));
			}
			
			if ($this->getParameter('logout')) {
				$k = new Kwejk(__VARIABLE_PATH.'/kwejkCommandCookie', '', false, false);
				$k->logout();
			}
		}
	}
?>