/**
 * JavaScript gOPF Terminal Plugin object
 * 
 * @/System/Terminal/Terminal.js
 * 
 * Usage:
 * Terminal.init();					Initiates terminal connection
 * Terminal.send(command);			Allows to pass command to terminal
 * Terminal.abort();				Abort current command process (if supported)
 * Terminal.debug();				Prints current terminal session status
 * Terminal.check();				Checks terminal status
 * Terminal.lock();					Locks terminal
 * Terminal.unlock();				Unlocks terminal
 * Terminal.update(data);			Updates terminal data (prompt, output etc.)
 * Terminal.print(data);			Allows to put data into terminal output
 * Terminal.clear();				Clears terminal output
 * 
 * Requires:
 * @/System/Core/gOPF.js
 * @/gOPF/gPAE/gPAE.js
 * @/System/Terminal/style.css
 * 
 * 
 * Version 1.0
 * 
 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */

Terminal = {
	status: {
		user: null,
		prompt: null,
		host: null,
		path: null,
		buffer: null,
		type: null,
		prefix: null,
		initialized: false,
		logged: false,
		processing: false,
		clear: false,
		abort: false,
		command: null
	},	
		
	init: function() {
		$.gPAE("connect");
		
		$.gPAE("addEvent", "onConnect", function() {
			$.gPAE("sendEvent", "initialize");
		});
		
		$.gPAE("addEvent", "stream", function(data) {
			Terminal.status = data.value;
			Terminal.check(data.value);
			Terminal.update(data.value);
		});
	},
	
	send: function(command) {
		if (!Terminal.status.processing) {
			var command = (Terminal.status.prefix == null) ? command : Terminal.status.prefix+command;
			
			$.gPAE("sendEvent", "command", {command: command});
			Terminal.lock();
		}
	},
	
	abort: function() {
		$.gPAE("sendEvent", "abort");
	},
	
	debug: function() {
		$.gPAE("sendEvent", "debug");
	},
	
	complete: function(command) {
		if (!Terminal.status.processing) {
			var value = command.val();
			
			if (value != "") {
				$.gPAE("sendEvent", "complete", {command: value, position: command.get(0).selectionStart});
			}
		}
	},
	
	check: function(data) {
		if (Terminal.processing) {
			Terminal.lock();
		} else {
			Terminal.unlock();
		}
	},
	
	lock: function() {
		$("form").hide();
	},
	
	unlock: function() {
		$("form").show();	
	},
	
	update: function(data) {
		if (data.clear) {
			Terminal.clear();
		}
		
		Terminal.print(data.buffer);
		
		if (data.prompt == null) {
			$("#prompt").html(data.user+"@"+data.host+":"+data.path+'# ');			
		} else {
			$("#prompt").html(data.prompt);
		}
		
		if (data.command != '') {
			var command = $("#command");
			var position = command.get(0).selectionStart;
			
			var after = command.val().slice(position);
			var before = command.val().slice(0, position);
			
			var command = before+data.command+after;
			
			if (command != null) {
				$("#command").val();
			}
		}
		
		$("#command").prop("type", data.type);
		
		if (data.processing) {
			$("form").hide();
		} else {
			$("form").show();
		}
		
		document.body.scrollTop = document.body.scrollHeight;
	},
	
	print: function(content) {
		$("#console").append(content);
	},
	
	clear: function() {
		$("#console").html("");
	}
}

$.gPAE("config", {url: "/terminal/connection", debug: 2});

$(document).ready(function() {
	$("#command").focus();
	
	Terminal.init();
	
	$("form").submit(function(e) {
		if (!Terminal.status.processing) {
			var value = $("#command").val();
			
			Terminal.send(value);
			Terminal.print($("#prompt").html() + (($("#command").prop("type") == "password") ? "" : value)+"\n");
			
			$("#command").val("");
		}
			
		e.preventDefault();
		return false;
	});
	
	$("#command").blur(function(e) {
		setTimeout(function() { $("#command").focus(); }, 1);
	});
	
	$("body").keydown(function(e) {
		if (e.keyCode == 9) {
			Terminal.complete($("#command"));
			
			e.preventDefault();
			return false;
		}
		
		if (e.keyCode == 76 && e.ctrlKey) {
			Terminal.clear();
			
			e.preventDefault();
			return false;
		}
		
		if (e.keyCode == 67 && e.ctrlKey) {
			Terminal.abort();
			
			e.preventDefault();
			return false;
		}
		
		if (e.keyCode == 68 && e.ctrlKey && e.shiftKey) {
			Terminal.debug();
			
			e.preventDefault();
			return false;
		}
	});
});