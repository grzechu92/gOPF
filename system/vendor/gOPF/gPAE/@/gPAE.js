/**
 * JavaScript jQuery gPAE Push Plugin for gOPF Framework
 * @/gOPF/gPAE/gPAE.js
 *
 * Requires:
 * @/System/Core/gOPF.js
 * @/System/Core/jQuery.js
 * @/System/Events/Events.js
 *
 * Version 2.0
 * jQuery 1.11.1
 * 
 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */

function gPAE(url, debug) {
    var self = this;

    this.events = new Events();

    this._connection = {
        connected: false,
        key: '',
        encrypted: false,
        ping: 0,
        reconnect: null
    };

    this._config = {
        url: url,
        debug: debug == undefined ? 0 : debug,
        reconnect: 0,
        timeout: 0
    };

    this.connect = function() {
        if (self._connection.connected) {
            self._error("already connected!")
        } else {
            self._pipe.send({command: "CONNECT"}, false);
            self._debug("connecting...", 1);
        }
    };

    this.reconnect = function() {
        self._pipe.send({command: "CONNECT"}, false);
        self.events.call("onReconnect");
        self._debug("reconnecting...", 1);

        clearTimeout(self._connection.reconnect);
    };

    this.disconnect = function() {
        if (!self._connection.connected) {
            self._error("connection not available")
        } else {
            self._connection.connected = false;
            self._pipe.close();

            self.events.call("onDisconnect");
            self._debug("disconnected (by client)", 1);
        }
    };

    this.listen = function() {
        if (self._connection.connected) {
            self._pipe.send({command: "HOLD", key: self._connection.key, ping: self._connection.ping}, self._config.timeout + self._connection.ping + 1000);
            self._debug("waiting...", 2);
        } else {
            self._error("connect before listening!");
        }
    };

    this.send = function(event, data) {
        self._pipe.send({command: "ACTION", event: event, data: data, key: self._connection.key, ping: self._connection.ping}, false);
        self._debug("sending data...", 2);
    };

    this._debug = function(message, level) {
        if (self._config.debug >= level) {
            console.log("gPAE: " + message);
        }
    };

    this._error = function(message) {
        console.error("[gPAE] " + message);
    };

    this._encrypt = function(data) {
        var encoded = gOPF.Base64.encode(JSON.parse(data));
        var offset = 0;
        var encrypted = "";

        while (offset <= encoded.length) {
            var chunk = encoded.substr(offset, 2);
            var checksum = gOPF.Base64.encode(chunk);
            encrypted += checksum.substr(0, 1)+chunk;

            offset += 2;
        }

        return {encrypted: encrypted};
    };

    this._decrypt = function(encrypted) {
        var decoded = "";
        var chunk = true;
        var offset = 1;

        while (chunk != "") {
            chunk = encrypted.substr(offset, 2);
            decoded += chunk;
            offset += 3;
        }

        return gOPF.Base64.decode(decoded);
    };

    this._router = function(action) {
        self._debug(action, 3);

        switch (action.command) {
            case "CONNECTED":
                self._connection.connected = true;
                self._connection.key = action.result.data.key;
                self._config.reconnect = action.result.data.config.reconnect;
                self._config.timeout = action.result.data.config.timeout;

                self.events.call("onConnect");
                self._debug("connected", 1);

                self.listen();
                break;

            case "ACTION":
                self.events.call(action.result.event, action.result.data);
                self._debug("event received: " + action.result.event, 2);

                self.listen();
                break;

            case "RENEW":
                self._debug("connection timeout", 1);

                self.listen();
                break;

            case "DISCONNECTED":
                self._pipe.close();
                self._connection.connected = false;

                self.events.call("onDisconnect");
                self._debug("disconnected (by server)", 1);

                break;
        }
    };

    this._pipe = {
        handlers: [],

        close: function() {
            this.handlers.map(function(handler, id) {
                this.handlers[id].abort();
                delete this.handlers[id];
            }, this);
        },

        send: function(data, timeout) {
            if (self._connection.encrypted) {
                data = self._encrypt(data);
            }

            var handler = jQuery.ajax({
                type: "POST",
                url:  self._config.url + "//" + data.command.toLowerCase(),
                global: false,
                cache: false,
                complete: self._pipe.callback,
                data: data,
                timeout: timeout
            });

            handler.id = this.handlers.length;
            handler.start = gOPF.microtime();

            this.handlers.push(handler);
        },

        callback: function(data) {
            delete self._pipe.handlers[data.id];

            if (data.readyState == 4) {
                if (data.responseText == "") {
                    return;
                }

                var content = JSON.parse(data.responseText);

                if (content != null) {
                    if (content.encrypted || self._connection.encrypted) {
                        self._connection.encrypted = true;
                        content = JSON.parse(self._decrypt(content.encrypted));
                    }

                    if (typeof content.time != "undefined" && self._connection.connected) {
                        self._connection.ping = Math.round(gOPF.microtime() - data.start - content.time * 1000);
                        self.events.call("onPingChange", self._connection.ping);
                    }

                    self._router(content);
                }
            } else {
                self._pipe.error();
            }
        },

        error: function() {
            if (self._connection.connected) {
                self._pipe.close();
                self._connection.connected = false;

                self.events.call("onConnectionLost");
                self._debug("connection lost", 1);

                self._connection.timeout = setTimeout(self.reconnect, self._config.reconnect);
            }
        }
    }
}