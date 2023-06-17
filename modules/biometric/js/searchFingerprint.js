function initFPSocket(clientid, socketServer) 
{    
    // Make connection
    var socket = io.connect('https://'+socketServer+':3000', { transport : ['websocket'] });
    var bDisconnected = false;

    // send data to server
    socket.emit('client_data', clientid);

    //send data to html from java
    socket.on('server_data', function(data) {
        document.getElementById('foundPid').value = data;
        showFoundPatientProfile();
    });

    socket.on('connect', () => {
        if (bDisconnected) {
            socket.emit('client_data', clientid);
            bDisconnected = false;
        }
    });

    socket.on('disconnect', (reason) => {
        bDisconnected = true;
        socket.connect();
    });
}      