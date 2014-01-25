/**
 * JavaScript jQuery gPAE Push Plugin for gOPF Framework
 * 
 * Usage:
 * $.gPAE('config', {url: 'server url'[, debug: 0-2]});						set server url, and set debug level if required
 * $.gPAE('addEvent', 'event name', function(data) { (...) } );				add client event listener with specified name and action
 * $.gPAE('sendEvent', 'server event name', {data as object}); 				call server event with some data
 * $.gPAE('connect'); 														connects to the server
 * $.gPAE('disconnect'); 													disconnects from the server
 * 
 * Debug levels:
 * 0 -> (default) no debug info
 * 1 -> info about connection, system errors
 * 2 -> [level 1 ] + info about sending and receiving data
 * 
 * System events:
 * onPingChange(ping) 		called when ping value has been changed
 * onConnect() 				called when client is connected to push server
 * onDisconnect() 			called when client is disconnected from push server
 * onReconnect() 			called when client trying to connect to server
 * onConnectionLost() 		called when client lost connection with server (onDisconnect is called with it)
 * 
 * Version 1.0
 * jQuery 1.8
 * 
 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */

(function($) {
	var core = {
		connection: null,
		config: {url: null, debug: 0, reconnect: {frequency: 0}, timeout: {base: 0}},
		events: new Array(),
		
		debug: function(message, level) {
			if (this.config.debug >= level) {
				console.log('gPAE: '+message);
			}
		},
		
		error: function(message) {
			console.error('[gPAE] '+message);
		},
		
		callEvent: function(name, data) {
			if (this.events[name]) {
				this.events[name](data);
			}
		},
		
		getCurrentMictotime: function() {
			return (new Date().getMilliseconds()/1000)+new Date().getTime();
		},
		
		encrypt: function(data) {
			var encoded = jQuery.gOPF('encodeBase64', jQuery.gOPF('encodeJSON', data));
			var offset = 0;
			var encrypted = '';
			
			while (offset <= encoded.length) {
				var chunk = encoded.substr(offset, 2);
				var checksum = jQuery.gOPF('encodeBase64', chunk);
				encrypted += checksum.substr(0, 1)+chunk;
				
				offset += 2;
			}
			
			return {encrypted: encrypted};
		},
		
		decrypt: function(encrypted) {
			var decoded = '';
			var chunk = true;
			var offset = 1;
			
			while (chunk != '') {
				chunk = encrypted.substr(offset, 2);
				decoded += chunk;
				offset += 3;
			}
			
			return jQuery.gOPF('decodeBase64', decoded);
		},
		
		action: function(data) {
			switch (data.command) {
				case 'CONNECTED':
					connection.connected = true;					
					connection.key = data.key;
					core.config.timeout.base = data.timeout;
					core.config.reconnect.ferquency = data.reconnect;
					
					core.callEvent('onConnect');
					core.debug('connected', 1);
					
					connection.askForData();
				break;
	
				case 'CATCH':
					core.callEvent(data.event, data);
					core.debug('catched event: '+data.event, 2);
	
					connection.askForData();
				break;
	
				case 'RENEW':
					core.debug('connection timeout', 1);
					
					connection.askForData();
				break;
				
				case 'DISCONNECTED':
					connection.closeHandlers();
					connection.connected = false;
					
					core.callEvent('onDisconnect');
					core.debug('disconnected (by server)', 1);
				break;
			}
		}
	};
	
	var connection = {
		enabled: false,
		connected: false,
		encrypted: false,
		ping: 0,
		timeout: 0,
		key: '',
		
		handler: {connect: null, hold: null, send: null, reconnect: null, disconnect: null},
		
		closeHandlers: function() {
			for (type in this.handler) {
				if (this.handler[type] && this.handler[type].start) {
					this.handler[type].abort();
					this.handler[type] = null;
				}
			}
		},
		
		sendRequest: function(connection, data, timeout) {
			if (this.encrypted) {
				data = core.encrypt(data);
			}
			
			this.handler[connection] = jQuery.ajax({
				type: 'POST',
				url: core.config.url+'//'+connection,
				global: false,
				cache: false,
				complete: function(data) { jQuery.gPAE('__callback', data); },
				data: data,
				timeout: timeout
			});

			this.handler[connection].start = core.getCurrentMictotime();
		},
		
		callback: function(data) {
			if (data.readyState == 4) {
				var content = jQuery.parseJSON(data.responseText);
				
				if (content != null) {
					if (content.encrypted || connection.encrypted == true) {
						connection.encrypted = true;
						content = jQuery.parseJSON(core.decrypt(content.encrypted));
					}
					
					if (typeof content.time != 'undefined' && connection.connected == true) { 
						connection.ping = Math.round(core.getCurrentMictotime() - data.start - content.time*1000);
						connection.timeout = core.config.timeout.base+connection.ping+1000;
						
						core.callEvent('onPingChange', connection.ping);
					}
				
					connection.successCallback(content);
				}
			} else {
				connection.errorCallback();
			}
		},
		
		successCallback: function(data) {
			core.action(data);
		},
		
		errorCallback: function() {
			if (connection.enabled == true) {
				connection.closeHandlers();
				connection.connected = false;
				
				core.callEvent('onConnectionLost');
				core.debug('connection lost', 1);
				
				connection.reconnect.interval = setTimeout(connection.reconnect, core.config.reconnect.ferquency);
			}
		},
		
		connect: function() {
			connection.sendRequest('connect', {command: 'CONNECT'}, false);
			core.debug('connecting...', 1);
		},
		
		reconnect: function() {
			if (connection.enabled == true && connection.connected == false) {
				connection.sendRequest('reconnect', {command: 'CONNECT'}, false);
				
				core.callEvent('onReconnect');
				core.debug('reconnecting...', 1);
			} else {
				clearInterval(connection.reconnect.interval);
			}
		},
		
		disconnect: function() {
			connection.closeHandlers();
			connection.connected = false;
			
			core.callEvent('onDisconnect');
			core.debug('disconnected (by client)', 1);
		},
		
		askForData: function() {
			if (connection.connected) {
				connection.sendRequest('hold', {command: 'HOLD', key: connection.key, ping: connection.ping}, connection.timeout);
				core.debug('waiting...', 2);
			}
		},
		
		sendData: function(event, data) {
			connection.sendRequest('send', {command: 'CATCH', event: event, data: data, key: connection.key, ping: connection.ping}, false);
			core.debug('sending data...', 2);
		}
	};

	var methods = {			
		config: function(options) {
			core.config = jQuery.extend(core.config, options);
			core.debug('initialized', 1);
		},

		addEvent: function(name, action) {
			core.events[name] = action;
		},
		
		connect: function() {
			if (connection.connected) {
				core.error('already connected');
			} else {
				connection.enabled = true;
				connection.connect();
			}
		},
		
		disconnect: function() {
			if (!connection.connected) {
				core.error('connection not available');
			} else {
				connection.enabled = false;
				connection.disconnect();
			}
		},
		
		sendEvent: function(event, data) {
			if (!connection.connected) {
				core.error('connect before sending data');
			} else {
				connection.sendData(event, data);
			}
		},
		
		__callback: connection.callback,
		__successCallback: connection.successCallback,
		__errorCallback: connection.errorCallback
	};
	
	jQuery.gPAE = function(name) {
		if (methods[name]) {
			return methods[name].apply(this, Array.prototype.slice.call(arguments, 1));
		} else {
			console.error('[gPAE] unknown method has been called: '+name);
		}  
	};
})(jQuery);
