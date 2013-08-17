
function WindowURL(title){
	this.derive("bhv.widget.BaseWindow",title);
	this.loadURL=loadURL
	this.loadTextURL=loadTextURL
	this.loadIFrame=loadIFrame
	this.loadDiv=loadDiv
};


function loadURL(url, param){
	var myself=this;
	bhv.sendRequest("GET", url,param, true, handleLoadingURL, null, [myself,url])
}

function handleLoadingURL(myself, url) {
  var strResponse = bhv.relocateSRC(this.responseText, url);
	myself.content.innerHTML=strResponse;
}


function loadTextURL(url, param){
	var myself = this;
	bhv.sendRequest("GET", url, param, true, handleLoadingTextURL, null, [myself])
}

function handleLoadingTextURL(myself){
	alert("in: "+this.responseText)
	var strResponse=new String(this.responseText);
	strResponse=strResponse.replace(/</g,"&lt;")
	strResponse=strResponse.replace(/>/g,"&gt;")
	strResponse=strResponse.replace(/\n/g,"<br>")
	strResponse=strResponse.replace(/ /g,"&nbsp;")
	strResponse=strResponse.replace(/\t/g,"&nbsp;&nbsp;&nbsp;&nbsp;")
	myself.content.innerHTML=strResponse;

}

function loadIFrame(url){
  var iFrame = document.createElement("IFRAME");
  iFrame.src = url;
  iFrame.width="100%"
  iFrame.height ="100%"
  iFrame.scrolling="auto"
  this.content.appendChild(iFrame)

}


function loadDiv(oDiv){
  if (typeof oDiv == "string")
	oDiv=document.getElementById(oDiv);
  oDiv.parentNode.removeChild(oDiv)
  this.content.appendChild(oDiv)
}

