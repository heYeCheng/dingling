function Ajax(recvType){
	var aj=new Object();
	aj.recvType=recvType ? recvType.toUpperCase() : 'HTML' //HTML XML
	aj.targetUrl='';
	aj.sendString='';
	aj.resultHandle=null;

	aj.createXMLHttpRequest=function(){
		var request=false;
		
		//window对象中有XMLHttpRequest存在就是非IE，包括（IE7，IE8）
		if(window.XMLHttpRequest){
			request=new XMLHttpRequest();

			if(request.overrideMimeType){
				request.overrideMimeType("text/xml");
			}
		

		//window对象中有ActiveXObject属性存在就是IE
		}else if(window.ActiveXObject){
			
			var versions=['Microsoft.XMLHTTP', 'MSXML.XMLHTTP', 'Msxml2.XMLHTTP.7.0','Msxml2.XMLHTTP.6.0','Msxml2.XMLHTTP.5.0', 'Msxml2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP'];

			for(var i=0; i<versions.length; i++){
					try{
						request=new ActiveXObject(versions[i]);

						if(request){
							return request;
						}
					}catch(e){
						request=false;
					}
			}
		}
		return request;
	}

	aj.XMLHttpRequest=aj.createXMLHttpRequest();

	aj.processHandle=function(){
		if(aj.XMLHttpRequest.readyState == 4){
			if(aj.XMLHttpRequest.status == 200){
				if(aj.recvType=="HTML")
					aj.resultHandle(aj.XMLHttpRequest.responseText);
				else if(aj.recvType=="XML")
					aj.resultHandle(aj.XMLHttpRequest.responseXML);
			}
		}
	}

	aj.get=function(targetUrl, resultHandle){
		aj.targetUrl=targetUrl;	
		
		if(resultHandle!=null){
			aj.XMLHttpRequest.onreadystatechange=aj.processHandle;	
			aj.resultHandle=resultHandle;	
		}
		if(window.XMLHttpRequest){
			aj.XMLHttpRequest.open("get", aj.targetUrl);
			aj.XMLHttpRequest.send(null);
		}else{
			aj.XMLHttpRequest.open("get", aj.targetUrl, true);
			aj.XMLHttpRequest.send();
		}
		
	}
	return aj;
}
