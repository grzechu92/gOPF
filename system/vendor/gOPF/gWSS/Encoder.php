<?php
    namespace gOPF\gWSS;

    /**
     * Encoder helper for gWSS
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Encoder {
        /**
         * WebSocket magic key
         * @var string
         */
        const MAGIC_KEY = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

        /**
         * Generate key for Sec-WebSocket-Accept header
         *
         * @param string $key WebSocket key
         * @return string WebSocket accept key
         */
        public static function generateAcceptToken($key) {
            return base64_encode(sha1($key.self::MAGIC_KEY, true));
        }

        /**
         * Encode data to WebSocket format
         *
         * @param string $data Data to encode
         * @param string $type Data type
         * @return string Encoded string
         */
        public static function encodeWebSocket($data, $type = 'text') {
            switch ($type) {
                case 'continuous':
                    $b1 = 0;
                    break;

                case 'binary':
                    $b1 = 2;
                    break;

                case 'close':
                    $b1 = 8;
                    break;

                case 'ping':
                    $b1 = 9;
                    break;

                case 'pong':
                    $b1 = 10;
                    break;

                case 'text':
                    default:
                    $b1 = 1;
                    break;
            }

            $b1 += 128;
            $length = strlen($data);
            $lengthField = '';

            if ($length < 126) {
                $b2 = $length;
            } elseif ($length <= 65536) {
                $b2 = 126;
                $hexLength = dechex($length);

                if (strlen($hexLength) % 2 == 1) {
                    $hexLength = '0' . $hexLength;
                }

                $n = strlen($hexLength) - 2;

                for ($i = $n; $i >= 0; $i = $i - 2) {
                    $lengthField = chr(hexdec(substr($hexLength, $i, 2))) . $lengthField;
                }

                while (strlen($lengthField) < 2) {
                    $lengthField = chr(0) . $lengthField;
                }
            } else {
                $b2 = 127;
                $hexLength = dechex($length);

                if (strlen($hexLength) % 2 == 1) {
                    $hexLength = '0' . $hexLength;
                }

                $n = strlen($hexLength) - 2;

                for ($i = $n; $i >= 0; $i = $i - 2) {
                    $lengthField = chr(hexdec(substr($hexLength, $i, 2))) . $lengthField;
                }

                while (strlen($lengthField) < 8) {
                    $lengthField = chr(0) . $lengthField;
                }
            }

            return chr($b1) . chr($b2) . $lengthField . $data;
        }

        /**
         * Decode data from WebSocket format
         *
         * @param string $raw Raw WebSocket data
         * @return string Decoded WebSocket data
         */
        public static function decodeWebSocket($raw) {
            $length = ord($raw[1]) & 127;

            if ($length == 126) {
                $masks = substr($raw, 4, 4);
                $data = substr($raw, 8);
            } elseif ($length == 127) {
                $masks = substr($raw, 10, 4);
                $data = substr($raw, 14);
            } else {
                $masks = substr($raw, 2, 4);
                $data = substr($raw, 6);
            }

            $text = '';
            for ($i = 0; $i < strlen($data); ++$i) {
                $text .= $data[$i] ^ $masks[$i % 4];
            }

            return $text;
        }
    }
?>