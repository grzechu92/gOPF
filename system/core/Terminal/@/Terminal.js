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
		processing: false
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
		if (!Terminal.processing) {
			var command = (Terminal.status.prefix == null) ? command : Terminal.status.prefix+command;
			
			$.gPAE("sendEvent", "command", {command: command});
			Terminal.lock();
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
		Terminal.print(data.buffer);
		
		if (data.prompt == null) {
			$("#prompt").html(data.user+"@"+data.host+":"+data.path);			
		} else {
			$("#prompt").html(data.prompt);
		}
		
		$("#command").prop("attr", data.type);
		
		if (data.processing) {
			$("form").hide();
		} else {
			$("form").show();
		}
	},
	
	print: function(content) {
		$("#console").append(content);
	}
}

$.gPAE("config", {url: "/terminal/connection", debug: 2});

$(document).ready(function() {
	$("#command").focus();
	
	Terminal.init();
	
	$("*").click(function() {
		$("#command").focus();
	});
	
	$("form").submit(function(e) {
		Terminal.send($("#command").val());
		Terminal.print($("#prompt").html()+$("#command").val()+"\n");
		
		$("#command").val("");
		
		e.preventDefault();
		return false;
	});
});