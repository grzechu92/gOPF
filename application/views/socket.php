<script>
    function log(message) {
        document.write(message + "<br />")
    }

    var socket = new WebSocket("ws://127.0.0.1:8888/");
    socket.onopen = function() {
        log("Socket connection opened");
    }

    socket.onmessage = function(data) {
        log("Data received: "+ JSON.stringify(data.data));
    }
</script>