// Make connection
var socket = io.connect('https://'+socketServerHost+':3000', { transport : ['websocket'] });

if ( !!document.getElementById("clientId") ) {
    outputClientId = document.getElementById('clientId').value;
}

var bDisconnected = false;

// send data to server
socket.emit('client_data', outputClientId);

//send data to html from java
socket.on('server_data', function(data) {
    var outputFingerTemp = document.getElementById('fptemplate');
    outputFingerTemp.value = data;
});

socket.on('connect', () => {
    if (bDisconnected) {
        socket.emit('client_data', outputClientId);
        bDisconnected = false;
    }
});

socket.on('disconnect', (reason) => {
    bDisconnected = true;
    socket.connect();
});


