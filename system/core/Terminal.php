<?php
	namespace System;
	use \gOPF\gPAE\Client;
	use \gOPF\gPAE\Result;
	use \gOPF\gPAE;
	use \System\Filesystem;
	use \System\Terminal\Command;
	use \System\Terminal\Renderer;
	use \System\Terminal\Status;
    use \System\Terminal\Session;

	/**
	 * Terminal main initialization and router object
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Terminal extends Singleton {
		/**
		 * Terminal session handler
		 * @var \System\Terminal\Session
		 */
		private $session;

		/**
		 * Terminal output renderer
		 * @var \System\Terminal\Renderer
		 */
		private $renderer;

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
		public function handler() {
			$this->session = new Session();
			$this->renderer = new Renderer();

			$this->engine = new gPAE();
			$this->registerEvents();

			return $this->engine->run();
		}

        /**
         * Return Terminal session object
         *
         * @return \System\Terminal\Session
         */
        public function getSession() {
            return $this->session;
        }

		/**
		 * Registers terminal events
		 */
		private function registerEvents() {
			$this->engine->events->client->on('initialize', function(Client $client) {
				$session = $this->getSession();
				$status = $session->pull();

				if ($status->initialized) {
					return;
				}

				if (!$status->logged) {
					$this->execute('login -initialize');
					$status = $session->pull();
				}

				$status->processing = false;
				$status->initialized = true;
				$session->push($status);
			});

			$this->engine->events->server->on('stream', function(Client $client) {
                $session = $this->getSession();
				$status = $session->pull();

				if ($status->updated != $client->container->session) {
					$value = clone($status);
					$value->buffer = $this->renderer->render($value->buffer);

					$status->buffer = '';
					$status->command = '';
					$status->complete = '';
					$status->clear = false;

					$client->container->session = $status->updated;
					$session->push($status);

					$output = new \stdClass();
					$output->value = $value;

					return new Result('stream', $output);
				}
			});

			$this->engine->events->client->on('command', function(Client $client) {
				$this->execute($client->data->command, !($client->data->secret == 'true'));
			});

			$this->engine->events->client->on('reset', function(Client $client) {
                $session = $this->getSession();
				$session->push(new Status());

				$this->execute('login -initialize');
			});

			$this->engine->events->client->on('abort', function(Client $client) {
                $session = $this->getSession();
				$status = $session->pull();

				if ($status->processing) {
					$status->abort = true;
				}

				$session->push($status);
			});

			$this->engine->events->client->on('debug', function(Client $client) {
                $session = $this->getSession();

				$session->buffer(print_r($session->pull(), true));
			});

			$this->engine->events->client->on('upload', function(Client $client) {
				sleep(1);

                $session = $this->getSession();
				$status = $session->pull();

				if ($status->logged) {
					try {
						$file = explode(',', $client->data->content);
						$path = __ROOT_PATH . $status->path . $client->data->name;

						Filesystem::write($path, base64_decode($file[1]));
						Filesystem::chmod($path, 0777);

						$status->files[$client->data->id] = true;
					} catch (\Exception $e) {
						$status->buffer($e->getMessage());

						$status->files[$client->data->id] = false;
					}
				} else {
					$status->files[$client->data->id] = false;
				}

				$status->update();
				$session->push($status);
			});

			$this->engine->events->client->on('complete', function(Client $client) {
                $session = $this->getSession();
				$status = $session->pull();

				if (!$status->logged) {
					return;
				}

				$buffer = '';
				$matched = array();

				$command = $client->data->command;
				$position = $client->data->position;

				$complete = substr($command, 0, $position);
				$exploded = explode(' ', $complete);
				$location = $exploded[count($exploded)-1];


				if ($location[0] == DIRECTORY_SEPARATOR) {
					$path = __ROOT_PATH.DIRECTORY_SEPARATOR;
					$location = substr($location, 1);
				} else {
					$path = __ROOT_PATH . $status->path;
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
					/** @var $file \SplFileInfo */
					if (strpos($file->getPathname(), $path.($extended ? DIRECTORY_SEPARATOR : '').$location) === 0) {
						$matched[$file->getFilename()] = $file->getFilename().($file->isDir() ? DIRECTORY_SEPARATOR : '');
					}
				}

				ksort($matched);

				switch (count($matched)) {
					case 0:
						return;

					case 1:
						$status->complete = str_replace($location, '', array_pop($matched));
						$status->update();

						$session->push($status);
						return;
				}

				foreach ($matched as $file) {
					$buffer .= "\n".$file;
				}

				$status->buffer($buffer);
				$status->update();

				$session->push($status);
			});
		}

		/**
		 * Executes command request
		 *
		 * @param string $name Command content
		 * @param bool $history If command has secret data, don't save it to history
		 * @param bool $silent If silent, data won't be send to client
		 */
		private function execute($name, $history = false, $silent = false) {
            $session = $this->getSession();
			$status = $session->pull();

			$status->processing = true;
			$session->push($status);

//			if (!$status->logged && $commandName != 'login -initialize') {
//				$this->execute('login -initialize');
//				return;
//			}

			try {
				$command = Command::factory($name, $this);

                if (!$status->logged && $command->getName() != 'login') {
                    $this->execute('login -initialize');
                    return;
                }

                if ($status->logged && $history && empty($status->prefix)) {
                    $session->history($name);
                }

				if ($command->getParameter('help')) {
					$session->buffer($command->help()->build());
				} else {
					$command->execute();
				}
			} catch (\System\Loader\Exception $e) {
				$session->buffer('Unknown command: '.$commandName);
			} catch (\System\Terminal\Exception $e) {
				$session->buffer($e->getMessage());
			} catch (\System\Core\Exception $e) {
				$session->buffer('System error:'."\n".$e->getMessage());
			} catch (\Exception $e) {
				$session->buffer('Unknown error:'."\n".$e->getMessage());
			}

			$status = $session->pull();

			$status->processing = false;
			$status->abort = false;

			$session->push($status);

			if (!$silent) {
				$session->update();
			}
		}
	}
?>