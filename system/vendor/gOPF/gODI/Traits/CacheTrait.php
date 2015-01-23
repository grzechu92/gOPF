<?php
	namespace gOPF\gODI\Traits;
	use \gOPF\gODI\Statement;
	use \gOPF\gODI\Statement\Cache;

	/**
	 * CacheTrait - allows to cache
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	trait CacheTrait {
		/**
		 * Statement cache flag
		 * @var bool
		 */
		private $cacheable = false;

		/**
		 * @var \gOPF\gODI\Statement\Cache
		 */
		private $cache;

		/**
		 * Initialize cache statement
		 *
		 * @param int $expires Cache element expire time (in seconds)
		 * @param int $type Cache type (Statement::COMMON, Statement::USER, Statement::RUNTIME)
		 * @return \gOPF\gODI\Statement
		 */
		public function cache($expires, $type = Statement::COMMON) {
			$this->cache = new Cache($expires, $type);
			$this->cacheable = true;

			return $this;
		}
	}
?>