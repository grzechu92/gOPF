<?php
	namespace gOPF\gODI;

    /**
     * gODI Select statement
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
	class Select extends Statement {
		use \gOPF\gODI\Traits\FieldsTrait;
		use \gOPF\gODI\Traits\SearchTrait;
		use \gOPF\gODI\Traits\LimitTrait;
		use \gOPF\gODI\Traits\SortTrait;
        use \gOPF\gODI\Traits\JoinTrait;
        use \gOPF\gODI\Traits\CacheTrait;

        /**
         * @see \gOPF\gODI\Statement::build()
         */
        public function build() {
			$parts = array(
				'SELECT '.implode(', ', $this->fields),
				'FROM '.$this->table,
                (!empty($this->join) ? implode(' ', $this->join) : ''),
				(!empty($this->search) ? 'WHERE '.implode(' ', $this->search) : ''),
				(!empty($this->orderBy) ? 'ORDER BY '.implode(' ', array($this->orderBy, $this->orderType)) : ''),
				(($this->limitable > 0) ? 'LIMIT :_offset, :_limit' : '')
			);
			
			return trim(implode(' ', $parts));
		}

        /**
         * Get all results
         *
         * @return array Query results
         */
        public function all() {
            if ($this->cacheable) {
                $this->cache->checksum($this->checksum());

                if ($this->cache->isValid()) {
                    return $this->cache->get();
                } else {
                    $result = $this->execute(Statement::RETURN_DATA);

                    $this->cache->set($result);
                    return $result;
                }
            } else {
                return $this->execute(Statement::RETURN_DATA);
            }
		}

        /**
         * Get specified number of results
         *
         * @param int $limit Number of results
         * @param int $offset Offset from get results
         * @return array|mixed Result of array of results
         */
        public function get($limit = 1, $offset = 0) {
            $this->limit($limit, $offset);

            if ($this->cacheable) {
                $this->cache->checksum($this->checksum());

                if ($this->cache->isValid()) {
                    return $this->cache->get();
                } else {
                    $result = $this->execute(Statement::RETURN_DATA);
                    $result = (count($result) == 1) ? $result[0] : $result;

                    $this->cache->set($result);
                    return $result;
                }
            } else {
                $result = $this->execute(Statement::RETURN_DATA);
                return (count($result) == 1) ? $result[0] : $result;
            }
		}
	}
?>