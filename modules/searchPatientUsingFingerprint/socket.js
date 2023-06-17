function initFPSocket(pid) 
{
    // Make connection
    var socket = io.connect('http://127.0.0.1:3000');

    // Query DOM	
    var outputFingerTemp = document.getElementById('fingerprint');
    //var outputClientId = document.getElementById('ptnt').value;
    var outputClientId = pid;

    var bDisconnected = false;

    // send data to server
    socket.emit('new html', outputClientId);

    //send data to html from java
    socket.on('data display', function(data) {
      console.log('Data sent to html!');
      outputFingerTemp.innerHTML = data;  
    });

    socket.on('connect', () => {
            console.log('Connection established!');
            if (bDisconnected) {
                    socket.emit('new html', outputClientId);
                    bDisconnected = false;
            }
    });

    socket.on('disconnect', (reason) => {
            console.log('Connection was closed!');
            bDisconnected = true;
            socket.connect();
    });
}