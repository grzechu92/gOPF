<?php
    namespace gOPF\gPAE;

    /**
     * Communication command list
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Command {
        /**
         * Command for connecting
         * @var string
         */
        const CONNECT = 'CONNECT';

        /**
         * Command for connected state
         * @var string
         */
        const CONNECTED = 'CONNECTED';

        /**
         * Command for disconnecting
         * @var string
         */
        const DISCONNECT = 'DISCONNECT';

        /**
         * Command for disconnected state
         * @var string
         */
        const DISCONNECTED = 'DISCONNECTED';

        /**
         * Command for complete action
         * @var string
         */
        const ACTION = 'ACTION';

        /**
         * Command for hold request
         * @var string
         */
        const HOLD = 'HOLD';

        /**
         * Command for renewing connection
         * @var string
         */
        const RENEW = 'RENEW';
    }
?>