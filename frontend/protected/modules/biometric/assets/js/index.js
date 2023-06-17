const expressApp = require('express')();	  
const https = require('https'),
	  fs    = require('fs');
	  
expressApp.use(function (req, res, next) {
	const origin = req.get('origin');
	res.header('Access-Control-Allow-Origin', origin);
	res.header('Access-Control-Allow-Credentials', true);
	res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
	res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization, Cache-Control, Pragma');

	// intercept OPTIONS method
	if (req.method === 'OPTIONS') {
		res.sendStatus(204);
	} else {
		console.log(origin);
		next();
	}
});

const secureServer = https.createServer({
		key: fs.readFileSync('/home/supervisord/ssl/nginx-selfsigned.key'),
		cert: fs.readFileSync('/home/supervisord/ssl/nginx-selfsigned.crt')
	}, expressApp);
const ios = require('socket.io')(secureServer);

//const express = require('express'),
//http = require('http'),
//app = express(),
//server = http.createServer(app),
//io = require('socket.io').listen(server);

var clientids = {};

ios.on('connection', (socket) => {
  socket.on('client_data', function(data) {
		console.log('new client with data ' + data + ' is connected')
		socket.clientid = data; // get clientid

		//clientids.push(socket.clientid); // push client to clientids[]
		clientids[socket.clientid] = socket;

		//sendClientSocketId(socket);
		console.log('DEBUG: clientids = ' + Object.keys(clientids))
	});

    // call in java program
	socket.on('data detection', (patientid,templateOrPtntid) => {
       //log the message in console
       console.log("DEBUG: patient id: " + patientid + "; template: " +templateOrPtntid)
			// call in html
			//io.emit('server_data', {client: socket.clientid, template: messageContent});
			//var clientid = patientid;
			if (patientid in clientids) {
				clientids[patientid].emit('server_data', templateOrPtntid);
				console.log('DEBUG: clientids= ' + Object.keys(clientids))
		  } else {
				console.log('DEBUG: [Error] ' + patientid + ' is missing. Current clientids= ' + Object.keys(clientids))
      }
	});

	socket.on('disconnect', function(data) {
        console.log(data + ' has left.')
	    delete clientids[socket.clientid];
	    console.log('DEBUG: After disconnection list of clientids=' + Object.keys(clientids))
    })
})

//server.listen(3000, '0.0.0.0', ()=>{
//	console.log('Node app is running on port 3000')
//})

secureServer.listen(3000, '0.0.0.0', ()=>{
	console.log('Node app is running on port 3000')
})
