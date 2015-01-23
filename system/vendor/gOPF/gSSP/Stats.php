<?php
    namespace gOPF\gSSP;

    /**
     * gSSP Server stats object
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Stats {
        /**
         * Slots array
         * @var \gOPF\gSSP\Slot[]
         */
        private $slots;

        /**
         * Generate stats object
         *
         * @param \gOPF\gSSP\Slot[] $slots Current server slots
         */
        public function __construct($slots) {
            $this->slots = $slots;
        }

        /**
         * Return amount of slots with selected status
         *
         * @param string $status Status type
         * @return int Status amount
         */
        public function get($status) {
            $sum = 0;

            foreach ($this->slots as $slot) {
                if ($slot->status == $status) {
                    $sum++;
                }
            }

            return $sum;
        }
    }
?>