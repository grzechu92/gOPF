<?php
	use \System\View;
?>

<script>
    var socket = new WebSocket("ws://127.0.0.1:8888/");

    socket.onopen = function() {
        console.log("onOpen");
    }

    socket.onmessage = function(data) {
        //console.log("onMessage", data);
        console.log(data.data);
    }

    socket.onerror = function(data) {
        console.log("onError", data);
    }

    socket.onclose = function() {
        console.log("onClose");
    }
</script>