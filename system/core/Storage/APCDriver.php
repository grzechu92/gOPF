<?php
	namespace System\Storage;
	use System\Drivers\DriverInterface;
	use System\Drivers\APC;
	
	/**
	 * APC storage driver
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class APCDriver extends APC implements DriverInterface {
		/**
		 * @see System\Drivers.APC::prefix
		 */
		protected $prefix = 'gOPF-STORAGE-';
	}
?>