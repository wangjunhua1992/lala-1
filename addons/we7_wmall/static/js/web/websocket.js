define(['jquery'], function($) {
	var websocket = {
		ws: null,
		onGetMessage: null,
		heartCheck: {
			timeout: 20000,//毫秒
			timeoutObj: null,
			serverTimeoutObj: null,
			reset: function(){
				this.timeoutObj && clearTimeout(this.timeoutObj);
				this.serverTimeoutObj && clearTimeout(this.serverTimeoutObj);
				return this;
			},
			start: function(){
				var self = this;
				this.reset();
				this.timeoutObj = setTimeout(function(){
					//这里发送一个心跳，后端收到后，返回一个心跳消息，
					//onmessage拿到返回的心跳就说明连接正常
					var pingData = {
						type: 'ping',
						data: {}
					};
					if(websocket.ws.readyState == 1) {
						websocket.onSend(pingData);
					}
					self.serverTimeoutObj = setTimeout(function(){//如果超过一定时间还没重置，说明后端主动断开了
						websocket.ws.close();
					}, self.timeout)
				}, this.timeout)
			}
		}
	};
	websocket.init = function() {
		//var wssUrl = 'ws://127.0.0.1:2345';

		var pathname = window.location.pathname;
		var start = pathname.indexOf('/addons/');
		var basepath = pathname.substring(0, start);
		var siteRoot = window.location.protocol + '//' + window.location.host + basepath;
		var siteRootBase = siteRoot;
		siteRootBase = siteRootBase.replace('https://', '');
		siteRootBase = siteRootBase.replace('http://', '');
		var wssUrl = 'wss://' + siteRootBase + '/wss';
		websocket.ws = new WebSocket(wssUrl);
		websocket.ws.onopen = websocket.onOpen;
		websocket.ws.onmessage = websocket.onMessage;
		websocket.ws.onerror = websocket.onError;
		websocket.ws.onclose = websocket.onClose;
		return websocket;
	};
	websocket.onOpen = function() {
		websocket.heartCheck.start();
		irequire(["web/tiny"], function(tiny){
			tiny.removeStorage('wsInitTimes');
		});
		console.log('webSocket init success');
	};
	websocket.onMessage = function(e) {
		websocket.heartCheck.start();
		var data = JSON.parse(e.data);
		var type = data.type;
		var result = data.data;
		switch(type) {
			case 'connect':
				irequire(["web/tiny"], function(tiny){
					var clientId = result.clientId;
					$.post(tiny.getUrl('kefu/config/bind'), {client_id: clientId}, function(data){
						var result = data.message;
						if(result.errno) {
							Notify.error(result.message);
						}
						console.log('Bind Success ClientID:' + clientId);
					}, 'json');
				});

				break;
			case 'message':
				//收到消息播放音乐
				var audio = new Audio();
				audio.src = "../addons/we7_wmall/static/message.mp3";
				audio.play();
				console.log('收到了消息');
				if(typeof websocket.onGetMessage == 'function') {
					websocket.onGetMessage(result);
				}
				break;
			case 'ping':
				console.log('ping message')
				break;
			default:
		}
	};
	websocket.onError = function() {
		//发生错误重新连接
		irequire(["web/tiny"], function(tiny){
			var wsInitTimes = tiny.getStorage('wsInitTimes');
			if(!wsInitTimes || !wsInitTimes.times) {
				wsInitTimes = {
					times: 0
				}
			}
			if(wsInitTimes.times < 3) {
				wsInitTimes.times++;
				tiny.setStorage('wsInitTimes', wsInitTimes, 5 * 60);
				websocket.init();
			}
		});
	};
	websocket.onClose = function() {
		console.log('webSocket close');
	};
	websocket.onSend =  function(dataObj, callback) {
		if(websocket.ws.readyState == 1) {
			websocket.ws.send(JSON.stringify(dataObj));
			if(typeof callback == 'function') {
				callback();
			}
		}
	};
	return websocket;
})
