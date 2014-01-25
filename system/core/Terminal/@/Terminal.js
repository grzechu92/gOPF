/**
 * JavaScript gOPF Terminal Plugin object
 * 
 * @/System/Terminal/Terminal.js
 * 
 * Usage:
 * Terminal.init();						Initiates terminal connection
 * Terminal.send(command);				Allows to pass command to terminal
 * Terminal.abort();					Abort current command process (if supported)
 * Terminal.debug();					Prints current terminal session status
 * Terminal.check();					Checks terminal status
 * Terminal.lock();						Locks terminal
 * Terminal.unlock();					Unlocks terminal
 * Terminal.update(data);				Updates terminal data (prompt, output etc.)
 * Terminal.print(data);				Allows to put data into terminal output
 * Terminal.clear();					Clears terminal output
 * Terminal.upload(id, name, content);	Allows to upload file into terminal gate
 * 
 * Requires:
 * @/System/Core/gOPF.js
 * @/gOPF/gPAE/gPAE.js
 * @/System/Terminal/style.css
 * 
 * 
 * Version 1.1
 * 
 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
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
		command: null,
		complete: null,
		updated: null,
		storage: new Array(),
		history: new Array(),
		files: new Array()
	},
	
	uploader: {
		queue: new Array(),
		uploading: false,
		locked: false,
		upload: function() {
			if (!Terminal.uploader.uploading) {
				var element = Terminal.uploader.queue.pop();
				
				if (element != undefined) {
					Terminal.lock();
					Terminal.uploader.locked =  true;
					Terminal.uploader.uploading = true;
					Terminal.upload(element.id, element.name, element.content);
					
					$("#"+element.id).html('UPLOADING');
				} else {
					if (Terminal.uploader.locked) {
						Terminal.uploader.locked = false;
						Terminal.unlock();
					}
				}
			}
		}
	},
	
	position: 0,
		
	init: function() {
		$.gPAE("connect");
		
		$.gPAE("addEvent", "onConnect", function() {
			$.gPAE("sendEvent", "initialize");
		});
		
		$.gPAE("addEvent", "onDisconnect", function() {
			$.gPAE("connect");
		});
		
		$.gPAE("addEvent", "stream", function(data) {
			Terminal.status = data.value;
			Terminal.check(data.value);
			Terminal.update(data.value);
		});
		
		setInterval(Terminal.uploader.upload, 500);
	},
	
	send: function(command) {
		if (!Terminal.status.processing) {
			var command = (Terminal.status.prefix == null) ? command : Terminal.status.prefix + command;
			
			$.gPAE("sendEvent", "command", {command: command, secret: !($("#command").prop("type") == "text")});
			Terminal.lock();
		}
	},
	
	abort: function() {
		$.gPAE("sendEvent", "abort");
	},
	
	debug: function() {
		$.gPAE("sendEvent", "debug");
	},
	
	reset: function() {
		$.gPAE("sendEvent", "reset");
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
				
		if (data.complete != '') {
			var command = $("#command");
			var position = command.get(0).selectionStart;
			
			var after = command.val().slice(position);
			var before = command.val().slice(0, position);
			
			var command = before+data.complete+after;
			
			if (command != null) {
				$("#command").val(command);
			}
		}
		
		if (data.command != '') {
			$("#command").val(data.command);
		}
		
		$("#command").prop("type", data.type);
		
		if (data.processing) {
			Terminal.lock();
		} else {
			Terminal.unlock();
		}
		
		for (var c in data.files) {
			$("#"+c).html(data.files[c] ? "DONE" : "FAIL");
			Terminal.uploader.uploading = false;
		}
		
		$("#command").focus();
		
		Terminal.position = data.history.length;
	},
	
	print: function(content) {
		$("#console").append(content);
	},
	
	clear: function() {
		$("#console").html("");
	},
	
	upload: function(id, name, content) {
		$.gPAE("sendEvent", "upload", {id: id, name: name, content: content});
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
	
	$("body").keydown(function(e) {
		$("#command").focus();
		
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
		
		if (e.keyCode == 38) {
			if (Terminal.position-- <= -1) {
				Terminal.position = -1;
				$("#command").val('');
			} else {
				$("#command").val(Terminal.status.history[Terminal.position]);
			}
			
			e.preventDefault();
			return false;
		}
		
		if (e.keyCode == 40) {
			if (Terminal.position+1 > Terminal.status.history.length) {
				$("#command").val('');
			} else {
				Terminal.position++;
				$("#command").val(Terminal.status.history[Terminal.position]);
			}
			
			e.preventDefault();
			return false;
		}
		
		if (e.keyCode == 192 && e.shiftKey) {
			Terminal.reset();
			
			e.preventDefault();
			return false;
		}
	});
	
	$("html").on("drop", function(e) {
		var files = e.originalEvent.dataTransfer.files;
		console.log("drop", files);
		
		for (var i = 0; i < files.length; i++) {
			(function(file) {
				var id = (Math.random()+"").split(".")[1];
				
				Terminal.print("Uploading file "+file.name+"... <span id="+id+">QUEUED</span>\n");
				
				var reader = new FileReader();
				reader.onload = function(e) {
					Terminal.uploader.queue.push({id: id, name: file.name, content: e.currentTarget.result});
				};
				
				reader.readAsDataURL(file);
		    })(files[i]);
		}
		
		e.stopPropagation();
		e.preventDefault();
	});
	
	$("html").on("dragover dragleave", function(e) {
		if (e.type == "dragover") {
			Terminal.lock();
		} else {
			Terminal.unlock();
		}
		
		e.stopPropagation();
		e.preventDefault();
	});
});