<?php 
	namespace System\Terminal;
	use \System\Config;
	use \System\Storage;
    use \System\Terminal;

    /**
	 * Base terminal command object
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Command implements \System\Terminal\CommandInterface {
        /**
         * Default command namespace
         * @var string
         */
        const DEFAULT_NAMESPACE = '\\System\\Terminal\\Command\\';

		/**
		 * Storage key for terminal asking service
		 * @var string
		 */
		const ANSWER_CONTAINER_NAME = '__TERMINAL_ANSWER';

		/**
		 * Terminal instance
		 * @var \System\Terminal
		 */
		private static $terminal;
		
		/**
		 * Terminal session instance
		 * @var \System\Terminal\Session
		 */
		private static $session;

        /**
         * Command data
         * @var \System\Terminal\Data
         */
        private $data;

        /**
         * @see \System\Terminal\CommandInterface::initialize()
         */
        final public function initialize(Data $data) {
            $this->data = $data;
        }

        /**
         * Create command instance from command string
         *
         * @param string $raw Command string
         * @param \System\Terminal $terminal Terminal instance
         * @return \System\Terminal\CommandInterface
         * @throws \System\Terminal\Exception
         */
		final public static function factory($raw, Terminal $terminal = null) {
            if ($terminal instanceof Terminal) {
                self::$terminal = $terminal;
                self::$session = $terminal->getSession();
            }

			$config = Config::factory('terminal.ini', Config::APPLICATION);
			$commands = $config->get('commands');

            $data = new Data($raw);
            $command = $data->getCommand();

			if ($command[0] != '\\') {
				if (array_key_exists($command, $commands)) {
					$class = $commands[$command];
				} else {
					$class = self::DEFAULT_NAMESPACE.ucfirst($command);
				}
			} else {
				$class = $command;
			}
			
			$instance = new $class();
			
			if ($instance instanceof CommandInterface && $instance instanceof self) {
                $instance->initialize($data);

				return $instance;
			} else {
				throw new Exception('IS NOT A VALID COMMAND');
			}
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::onInstall()
		 */
		public function onInstall() {}
		
		/**
		 * @see \System\Terminal\CommandInterface::onUninstall()
		 */
		public function onUninstall() {}

        /**
         * @see \System\Terminal\CommandInterface::getName()
         */
		final public function getName() {
			$exploded = explode('\\', get_called_class());

			return strtolower($exploded[count($exploded) - 1]);
		}

        /**
         * @see \System\Terminal\CommandInterface::getParameter()
         */
		final public function getParameter($name) {
			if (isset($this->data->getParameters()[$name])) {
                $parameter = $this->data->getParameters()[$name];

				if (empty($parameter->getValue())) {
					return true;
				} else {
					return $parameter->getValue();
				}
			} else {
				return false;
			}
		}

        /**
         * @see \System\Terminal\CommandInterface::getValue()
         */
        final public function getValue() {
            return $this->data->getValue();
        }

		/**
		 * Ask client with a question and receive his answer
		 *
		 * @param string $question Question content
		 * @param array $answers Possible answers
		 * @return string Client answer
		 */
		final protected function ask($question, array $answers = array()) {
			$session = $this->getSession();

			$status = $session->pull();
			$status->prompt = $question.' '.(!empty($answers) ? '['.implode('/', $answers).'] ' : '');
			$status->processing = false;
			$status->prefix = 'answer ';
			$status->update();

			$session->push($status);

			while (true) {
				Storage::read();
				$answer = Storage::get(self::ANSWER_CONTAINER_NAME);

				if (!empty($answer)) {
                    Storage::delete(self::ANSWER_CONTAINER_NAME);
                    Storage::write();

					$status = $session->pull();
					$status->processing = true;
					$status->prompt = null;
					$status->prefix = null;
					$status->update();

					$session->push($status);

					return $answer;
				}

				usleep(100000);
			}
		}

        /**
         * Return terminal instance
         *
         * @return \System\Terminal
         */
        final protected function getTerminal() {
            return self::$terminal;
        }

        /**
         * Return terminal session instance
         *
         * @return \System\Terminal\Session
         */
        final protected function getSession() {
            return self::$session;
        }

        /**
         * Buffer some content to terminal output
         *
         * @param string $content Content to print
         */
        final protected function buffer($content) {
            $this->getSession()->buffer($content);
        }
	}
?>