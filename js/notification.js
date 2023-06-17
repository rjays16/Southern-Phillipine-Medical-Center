var totalRefno='';
class NotificationChannel {
    events = [];
    channelInstanc;
    isPrivate = false;
    isNewListenerAdded = true;
    isWisperListnerAdded = false;
    constructor(channelInst, isPrivate = false) {
        this.channelInstanc = channelInst;
        this.isPrivate = isPrivate;

        console.log('constructor this.channelInstanc',this.channelInstanc);
    }
    
    _addEvent(EventClass) {
        if(!this.events[EventClass]){
            this.events[EventClass] = {
                listen:{},
                whisper:{},
            };
            console.log('_addEvent this.channelInstanc',this.channelInstanc);
            console.log('Added new event.', EventClass);
        }

        return this;
    }

    _addListenEvent(EventClass, containerID, callback) {
        if(Object.entries(this.events[EventClass].listen).length == 0){
            console.log('Object.entries(this.events[EventClass].listen)',Object.entries(this.events[EventClass].listen));
            let thisInst = this;
            this.channelInstanc.listen(EventClass,function(data){
                console.log('Event listened: '+EventClass+' triggered.');
                for (const [key, callbacks] of Object.entries(thisInst.events[EventClass].listen)) {
                    callbacks(data);
                }
            });
        }

        this.events[EventClass].listen[containerID] = callback;

        return this;
    }
    
    _removeListenEvent(EventClass, containerID) {
        if(this.events[EventClass]){
            if(this.events[EventClass].listen[containerID])
                delete this.events[EventClass].listen[containerID];
        }
        return this;
    }


    _addWhisperEvent(EventClass, containerID, callback) {
        if(Object.entries(this.events[EventClass].whisper).length == 0){
            let thisInst = this;
            this.channelInstanc.listenForWhisper(EventClass,function(data){
                console.log('Event whisper: '+EventClass+' triggered.');
                for (const [key, callbacks] of Object.entries(thisInst.events[EventClass].whisper)) {
                    callbacks(data);
                }
            });
        }

        this.events[EventClass].whisper[containerID] = callback;

        return this;
    }

    _removeWhisperEvent(EventClass, containerID) {
        if(this.events[EventClass]){
            if(this.events[EventClass].whisper[containerID])
                delete this.events[EventClass].whisper[containerID];
        }
        return this;
    }

    listen(EventClass, containerID, callback){
        if(!containerID || !EventClass|| !callback)
            alert('All parameters are required.');

        this._addEvent(EventClass);
        this._addListenEvent(EventClass, containerID, callback);
        return this;
    }

    removeListener(EventClass, containerID){
        this._removeListenEvent(EventClass, containerID);
        return this;
    }

    listenWhisper(EventClass, containerID, callback){
        if(!containerID || !EventClass|| !callback)
            alert('All parameters are required.');
        this._addEvent(EventClass);
        this._addWhisperEvent(EventClass, containerID, callback);
        return this;
    }


    removeWhisperListener(EventClass, containerID){
        this._removeWhisperEvent(EventClass, containerID);
        return this;
    }


    whisper(EventClass, payload){
        if(!this.events[EventClass] || !EventClass)
            alert('EventClass: '+EventClass+' does not exist.');
        if(!this.isPrivate)
            alert('Whisper only allowed on private channels.');
        console.log('this.channelInstanc',this.channelInstanc);
        this.channelInstanc.whisper(EventClass, payload);
    }


}


class Notification {
    channels = [];
    echoVar;
    constructor(host, token){
        window['io'] = io;
        console.log("window['io']",window['io']);

        let Echo = window.node_modules['laravel-echo'];
        console.log("Echo",Echo);

        this.echoVar = new Echo.default({
            broadcaster: 'socket.io',
            host: host,
            auth: {
            headers: {
                    Accept: 'application/json',
                    Authorization: 'Bearer '+token
                },
            }
        });

        let echV = this.echoVar;
        this.echoVar.connector.socket.on('connect', function(){
            console.log('connected', echV.socketId());
        });
        this.echoVar.connector.socket.on('disconnect', function(){
            console.log('socket disconnected');
        });
        this.echoVar.connector.socket.on('reconnecting', function(attemptNumber){
            console.log('reconnecting socket', attemptNumber);
        });

        console.log("this.echoVar",this.echoVar);
    }

    private(channelName){
        if(!this.channels[channelName]){
            let channelInst =  this.echoVar.private(channelName);
            this.channels[channelName] = new NotificationChannel(channelInst, true);
        }
        return this.channels[channelName];
    }


    channel(channelName){
        if(!this.channels[channelName]){
            let channelInst =  this.echoVar.channel(channelName);
            this.channels[channelName] = new NotificationChannel(channelInst, false);
        }
        return this.channels[channelName];
    }

    notifApi(stickyClass, setting, config) {        
        const user_token = localStorage.getItem('notifToken');
        var host = localStorage.getItem('ehrMobileHost');
        var loc = window.location;
        var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=laboratory/notification';
       
        /***
         * Show laboratory notifications 
         */
        $j.ajax({
            type:'GET',
            url: host+"/notifications",
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer '+user_token,
                "Access-Control-Allow-Origin": '*'
            },
             // crossDomain: true,
             // dataType: 'jsonp',
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
                // console.log(data);

                config['ER_Admission']['counter'] = 0;
                config['ER']['counter'] = 0;
                config['IPD_IPBM']['counter'] = 0;
                parent.$j('div.sticky').css("display","none");
                // console.log(data);            
                if(data) {
                  var list_refs = '';
                    $j.map(data, function(n){
                        var depKeyNames = Object.keys(config);
                       
                        if ( (n.data.patient_type === depKeyNames[0] ||
                              n.data.patient_type === depKeyNames[1] ||
                              n.data.patient_type === depKeyNames[2]) && n.data.hasOwnProperty('refno') ) {
                            const patient_type = n.data.patient_type;
                            // console.log(n.data.refno);
                            var refno = n.data.refno;
                            $j.ajax({
                                url:baseUrl+"/IsPrint",
                                data:{ 
                                    refno :  refno
                                },
                                success: function(obj) {
                                    // console.log(obj);
                                    var obj = JSON.parse(obj);
                                    if(obj.data != null) {
                                        // console.log("refno:"+obj.data.refno+",is_printed"+obj.data.is_printed+"from:"+n.data.consult_id);
                                        if(n.data.consult_id == null){
                                            if(obj.data.is_printed == '0'){
                                        // console.log("refno:"+obj.data.refno+",is_printed"+obj.data.is_printed+"from:"+n.data.consult_id+",Patient Type: "+patient_type);
                                        // console.log(n.data);
                                            var list_ref  = obj.data.refno;
                                                        stickyClass.placeAlert(
                                                        setting,
                                                        patient_type,
                                                        n.data,
                                                        ++config[patient_type]['counter'],
                                                        config[patient_type]['color'],
                                                        config[patient_type]['department_name']
                                                    );
                                            }

                                        }
                                    }                                                                                                                                                                    
                                }
                            });                           
                        }
                    });
                }
                
                /***
                 * Initialize alert for Online Consultation Requests if with permission ...
                 */
                $j.ajax({
                    url : '/' + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/consultation/isOnlineConsultTriage',            
                    dataType : 'json',
                    success : function(response) {
                        if (response.permitted) {
                            var bpo_color = '#e89758';	
                            $j.ajax({
                                url : '/' + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/consultation/consultRequestCount',
                                dataType : 'json',
                                success : function(response) {
                                    if (response.count > 0) {
                                        let data = {
                                            url: loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/online/'
                                        }
                                        
                                        stickyClass.triageAlert(setting, bpo_color, data, response.count);
                                    }
                                }
                            });
                        }
                    }
                });

                /***
                 * Initialize alert for Online Consultation Requests in MedRec if with permission ...
                 */
                 $j.ajax({
                    url : '/' + loc.pathname.split('/')[1]+'/index.php?r=medRec/consultation/isOnlineConsultMedRec',            
                    dataType : 'json',
                    success : function(response) {
                        if (response.permitted) {
                            var bpo_color = '#e89758';	
                            $j.ajax({
                                url : '/' + loc.pathname.split('/')[1]+'/index.php?r=medRec/consultation/consultMedRecCount',
                                dataType : 'json',
                                success : function(response) {
                                    if (response.count > 0) {
                                        let data = {
                                            url: loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=medRec/online/'
                                        }
                                        
                                        stickyClass.medRecAlert(setting, bpo_color, data, response.count);
                                    }
                                }
                            });
                        }
                    }
                });

            },
            error: function(data){
                console.log("error");
                console.log(data);
            }
        });        	        
    }

    initAlerts(){
        console.log('Initialized alerts..');
        const setting = {'speed': 'fast', 'autoclose': false, 'duplicates': true};
        const username = localStorage.getItem('username');
        const config = {
            'ER_Admission': {
                'color': '#27f807',
                'counter': 0,
                'department_name': 'IPD',
                'tab_index': 5
            },
            'ER': {
                'color': '#f87777',
                'counter': 0,
                'department_name': 'ER',
                'tab_index': 4
            },
            'IPD_IPBM': {
                'color': '#68ceff',
                'counter': 0,
                'department_name': 'IPBM-IPD',
                'tab_index': 7
            },
        }
        var depKeyNames = Object.keys(config);
        let stickyClass = new StickyAlert();
    
        // Direct fetching of unseen notification
        if(username)
            this.notifApi(stickyClass, setting, config);
        // New Order Listener
        var newLabChannelName = localStorage.getItem('username') === "" ? "lab._.order" : "lab."+localStorage.getItem('username')+".order";
    
        this.private(newLabChannelName+"."+depKeyNames[0])
            .listen('NewLabOrderEvent','page1',function(data){
                var patient_type = "ER_Admission";
                stickyClass.placeAlert(setting, patient_type, data, ++config.ER_Admission.counter, config.ER_Admission.color, config.ER_Admission.department_name);
            });
    
        this.private(newLabChannelName+"."+depKeyNames[1])
            .listen('NewLabOrderEvent','page1',function(data){
                var patient_type = "ER";
                stickyClass.placeAlert(setting, patient_type, data, ++config.ER.counter, config.ER.color, config.ER.department_name);
            });
    
        this.private(newLabChannelName+"."+depKeyNames[2])
            .listen('NewLabOrderEvent','page1',function(data){
                var patient_type = "IPD_IPBM";
                stickyClass.placeAlert(setting, patient_type, data, ++config.IPD_IPBM.counter, config.IPD_IPBM.color, config.IPD_IPBM.department_name);
            });
    
        // Main notification for BPO Triage
        this.private("bpo.consultation")
            .listen('BpoNewConsultationEvent','page1',function(data) {                
				var loc = window.location;
                $j.ajax({
                    url : '/' + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/consultation/isOnlineConsultTriage',            
                    dataType : 'json',
                    success : function(response) {
                        if (response.permitted) {            
                            var color = '#e89758';				
                            $j.ajax({
                                url : '/' + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/consultation/consultRequestCount',
                                dataType : 'json',
                                success : function(response) {
                                    stickyClass.triageAlert(setting, color, data, response.count);
                                }
                            });                                                            
                        }
                    }
                });
            });
            
        // Main notification for BPO MedRec
        this.private("bpo.medrec.teleconsult.entry")
            .listen('BpoNewTeleconsultMedRecEvent','page1',function(data) {
				var loc = window.location;
                $j.ajax({
                    url : '/' + loc.pathname.split('/')[1]+'/index.php?r=medRec/consultation/isOnlineConsultMedRec',            
                    dataType : 'json',
                    success : function(response) {
                        if (response.permitted) {
                            var color = '#e89758';				
                            $j.ajax({
                                url : '/' + loc.pathname.split('/')[1]+'/index.php?r=medRec/consultation/consultMedRecCount',
                                dataType : 'json',
                                success : function(response) {
                                    stickyClass.medRecAlert(setting, color, data, response.count);
                                }
                            });                             
                        }
                    }
                });
            });        
    }

    initTeleconsultTriageAlert() {
        this.private("bpo.consultation")
            .listen('BpoNewConsultationEvent','page2',function(data) {                                
                if ($('#online-grid').length > 0) {
                    $.fn.yiiGridView.update('online-grid');
                }
            });
            
        this.private("bpo.triageconsult")
            .listen('BpoTriageConsultEvent','page2',function(data) {                               
                if ($('#'+data.consult_id).length > 0) {
                    $('#'+data.consult_id).data('status', "1");
                }
            });
            
        this.private("bpo.done.triageconsult")
            .listen('BpoDoneTriageConsultEvent','page2',function(data) {                           
                if ($('#online-grid').length > 0) {
                    $.fn.yiiGridView.update('online-grid');
                }
            });        
    }

    initTeleconsultMedRecAlert() {
        this.private("bpo.medrec.teleconsult.entry")
            .listen('BpoNewTeleconsultMedRecEvent','medrecpage',function(data) {                                
                if ($('#online-grid').length > 0) {
                    $.fn.yiiGridView.update('online-grid');
                }
            });
            
        this.private("bpo.medrec.teleconsult.register")
            .listen('BpoTeleconsultRegisterEvent','medrecpage',function(data) {                               
                if ($('#'+data.consult_id).length > 0) {
                    $('#'+data.consult_id).data('status', "1");
                }
            });
            
        this.private("bpo.medrec.teleconsult.done")
            .listen('BpoTeleconsultDoneRegisterEvent','medrecpage',function(data) {                           
                if ($('#online-grid').length > 0) {
                    $.fn.yiiGridView.update('online-grid');
                }
            });          
    }
}

class StickyAlert{    
    placeAlert(setting, patient_type, data, ctr, color, dep) {
        var l = window.location,
            baseUrl = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1];
        const tab_index = {
            'ER_Admission':  5,
            'ER': 4,
            'IPD_IPBM': 7
        };
        if(data.refno){
            this.listRef(data.refno)
        }else{
            this.listRef(data.param_data.refno)
        }
        // this.listRef(data.refno)

        parent.$j('div.sticky.'+patient_type).css("display","none");
        parent.$j.sticky("You have <span id='ipd-count'>"+ctr+"</span> pending "+dep+" laboratory request/s",
            baseUrl + '/modules/laboratory/seg-lab-request-new-list.php?sid=r5hgomnsj2k0oorl08rb2u5hj3&lang=en&samplelist=0&user_origin=lab&checkintern=1&tabindex='+tab_index[patient_type]+'&listRef='+totalRefno.substring(1),
            patient_type,
            setting
        );
        parent.$j("div.sticky-queue div.sticky."+patient_type).css("background",color);
    }

    triageAlert(setting, color, data, nRequests) {              
        parent.$j('div.sticky.BPO_CONSULT').css("display","none");
        var stext = '%u online %s for consultation %s been submitted for triage!';
        stext = vsprintf(stext, [nRequests, (nRequests > 1) ? 'requests' : 'request', (nRequests > 1) ? 'have' : 'has']);
        parent.$j.sticky(stext,
            data.url,
            "BPO_CONSULT",
            setting
        );
        parent.$j("div.sticky-queue div.sticky.BPO_CONSULT").css("background",color);
    }

    // Alert for medical records personnel ...
    medRecAlert(setting, color, data, nConsults) {
        parent.$j('div.sticky.MEDREC_CONSULT').css("display","none");
        var stext = '%u online consultation %s awaiting HRN and creation of encounter!';        
        stext = vsprintf(stext, [nConsults, (nConsults > 1) ? 'requests' : 'request']);
        parent.$j.sticky(stext,
            data.url,
            "MEDREC_CONSULT",
            setting
        );
        parent.$j("div.sticky-queue div.sticky.MEDREC_CONSULT").css("background",color);
    }

    listRef(refno){
        // alert("xx");
        totalRefno += ","+refno;       
    }
}