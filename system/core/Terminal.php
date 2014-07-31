<?php
	namespace System;
    use gOPF\gPAE\Config;
    use gOPF\gPAE;
    use System\Filesystem;
    use System\Terminal\Command;
    use System\Terminal\Parser;
    use System\Terminal\Status;

    /**
	 * Terminal main initialization and router object
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Terminal {
        /**
         * Terminal command suffix
         * @var string
         */
        const COMMAND_SUFFIX = 'Command';

		/**
		 * Terminal instance handler
		 * @var \System\Terminal
		 */
		public static $instance;

		/**
		 * Terminal session handler
		 * @var \System\Terminal\Session
		 */
		public static $session;

		/**
		 * Terminal output parser
		 * @var \System\Terminal\Parser
		 */
		private $parser;

		/**
		 * gPAE push engine handler
		 * @var \gOPF\gPAE
		 */
		private $engine;

		/**
		 * Initiates gPAE engine for listening terminal events
		 *
		 * @return \gOPF\gPAE\Response gPAE result
		 */
		public function connection() {
			self::$instance = $this;
			self::$session = new \System\Terminal\Session();

			Command::$terminal = self::$instance;
			Command::$session = self::$session;

            $config = new Config();
            $config->interval = 1000;

			$this->parser = new Parser();
			$this->engine = new gPAE($config);
			$this->registerEvents();

			return $this->engine->run();
		}

		/**
		 * Creates event for client
		 *
		 * @param string $name Event name
		 * @param \Closure $closure Event body
		 */
		private function addClientEvent($name, \Closure $closure) {
			$this->engine->addClientEvent(new Event($name, $closure));
		}

		/**
		 * Creates event for server
		 *
		 * @param string $name Event name
		 * @param \Closure $closure Event body
		 */
		private function addServerEvent($name, \Closure $closure) {
			$this->engine->addServerEvent(new Event($name, $closure));
		}

		/**
		 * Registers terminal events
		 */
		private function registerEvents() {
			$this->addClientEvent('initialize', function($push) {
				$session = Terminal::$session;
				$status = $session->pull();

				if ($status instanceof Status && $status->processing && $status->initialized) {
					return;
				}

				if (!$status instanceof Status) {
					$status = new Status();
					$status->initialize();
				}

				if (!$status->logged) {
					$session->push($status);
					$this->execute('login -initialize');
				}

				$status = $session->pull();
				$status->processing = false;
				$session->push($status);
			});

			$this->addServerEvent('stream', function($push) {
				$session = Terminal::$session;
				$status = $session->pull();

				if ($status instanceof Status && $status->updated != $push->container->session) {
					$value = clone($status);
					$value->buffer = $this->parser->parse($value->buffer);

					$status->buffer = '';
					$status->command = '';
					$status->complete = '';
					$status->clear = false;

					$push->container->session = $status->updated;
					$session->push($status);

					return array('value' => $value);
				}
			});

			$this->addClientEvent('command', function($push) {
				$this->execute($push->data->command, !($push->data->secret == 'true'));
			});

			$this->addClientEvent('reset', function($push) {
				$session = Terminal::$session;

				$status = $session->pull();
				$status->initialize();

				$session->push($status);
				$session->write();

				$this->execute('login -initialize');
			});

			$this->addClientEvent('abort', function($path) {
				$session = Terminal::$session;

				if ($session->processing) {
					$session->abort = true;
				}
			});

			$this->addClientEvent('debug', function($push) {
				$session = Terminal::$session;

				$session->buffer(print_r($session->pull(), true));
				$session->update();
			});

			$this->addClientEvent('upload', function($push) {
				sleep(1);

				$session = Terminal::$session;
				$status = $session->pull();

				if ($status->logged) {
					try {
						$file = explode(',', $push->data->content);
						$path = __ROOT_PATH.$status->path.$push->data->name;

						Filesystem::write($path, base64_decode($file[1]));
						Filesystem::chmod($path, 0777);
						$status->files[$push->data->id] = true;
					} catch (\Exception $e) {
						$status->buffer($e->getMessage());
						$status->files[$push->data->id] = false;
					}
				} else {
					$status->files[$push->data->id] = false;
				}

				$status->update();
				$session->push($status);
			});

			$this->addClientEvent('complete', function($push) {
				$session = Terminal::$session;

				if (!$session->logged) {
					return;
				}

				$buffer = '';
				$matched = array();

				$command = $push->data->command;
				$position = $push->data->position;

				$complete = substr($command, 0, $position);
				$exploded = explode(' ', $complete);
				$location = $exploded[count($exploded)-1];


				if ($location[0] == DIRECTORY_SEPARATOR) {
					$path = __ROOT_PATH.DIRECTORY_SEPARATOR;
					$location = substr($location, 1);
				} else {
					$path = __ROOT_PATH.$session->path;
				}

				if (strpos($location, DIRECTORY_SEPARATOR) > 0) {
					$extended = true;
					$exploded = explode(DIRECTORY_SEPARATOR, $location);

					$location = array_slice($exploded, -1)[0];
					$path .= implode(DIRECTORY_SEPARATOR, array_slice($exploded, 0, -1));
				} else {
					$extended = false;
				}

				foreach (new \DirectoryIterator($path) as $file) {
					if (strpos($file->getPathname(), $path.($extended ? DIRECTORY_SEPARATOR : '').$location) === 0) {
						$matched[] = $file->getFilename().($file->isDir() ? DIRECTORY_SEPARATOR : '');
					}
				}

				switch (count($matched)) {
					case 0:
						return;

					case 1:
						$session->complete = str_replace($location, '', $matched[0]);
						$session->update();
						return;
				}

				foreach ($matched as $file) {
					$buffer .= "\n".$file;
				}

				$session->buffer($buffer);
				$session->update();
			});
		}

		/**
		 * Executes command request
		 *
		 * @param string $command Command content
		 * @param bool $history If command has secret data, don't save it to history
		 * @param bool $silent If silent, data won't be send to client
		 */
		private function execute($command, $history = false, $silent = false) {
			$session = Terminal::$session;
			$status = $session->pull();

			$parsed = Command::parse($command);

			$session->processing = true;

			if (!$status->logged && $parsed->name != 'login') {
				$this->execute('login -initialize');
				return;
			}

			try {
				if ($status->logged && $history && empty($status->prefix)) {
					$session->history($command);
				}

				$command = Command::factory($parsed);

				if (array_key_exists('help', $parsed->parameters)) {
					$session->buffer($command->help()->build());
				} else {
					$command->execute();
				}
			} catch (\System\Loader\Exception $e) {
				$session->buffer('Unknown command: '.$parsed->name);
			} catch (\System\Terminal\Exception $e) {
				$session->buffer($e->getMessage());
			} catch (\System\Core\Exception $e) {
				$session->buffer('System error:'."\n".$e->getMessage());
			} catch (\Exception $e){
				$session->buffer('Unknown error:'."\n".$e->getMessage());
			}

			$session->processing = false;
			$session->abort = false;

			if (!$silent) {
				$session->update();
			}
		}
	}
?>