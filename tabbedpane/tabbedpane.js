if (typeof bhv == "undefined")
  var bhv = {};
if (typeof bhv.tabbedpane == "undefined")
  bhv.tabbedpane = {};



bhv.tabbedpane.selectTab = function(){
  if (! this.loaded) {
    this.loaded = true;
    bhv.sendRequest("GET",this.adress,null,true, 
             bhv.tabbedpane.loadTab, bhv.tabbedpane.errorTab, [this]);
  }
  var barPane = this.parentNode;
  var divs = barPane.childNodes;
  for(var i=0; i <divs.length; i++)
    if (divs[i].tagName == "SPAN")
      bhv.tabbedpane.unSelectTab.call(divs[i]);
  this.className = "tabSelected";
  this.pane.className = "paneSelected";
  this.pane.style.height = (parseInt(this.parentNode.parentNode.style.height)-this.parentNode.offsetHeight 
    - this.parentNode.parentNode.tabbedBar.offsetHeight - this.parentNode.parentNode.space.offsetHeight) + "px";

}

bhv.tabbedpane.unSelectTab = function() {
  this.className = "tab";
  this.pane.className = "pane"
}

bhv.tabbedpane.loadTab = function(thi){

  thi.pane.innerHTML = bhv.relocateSRC(this.responseText, thi.adress )

}

bhv.tabbedpane.errorTab = function(thi){

  thi.pane.innerHTML = "<h1>Страница недоступна</h1>"

}


bhv.tabbedpane.addTabbedPaneBehivior = function(rootElement, paneModel) {

  rootElement.innerHTML="";
  rootElement.className = "tabbed";

  rootElement.tabbedBar = document.createElement("DIV");
  rootElement.tabbedBar.className = "tabbedBar";
  rootElement.appendChild(rootElement.tabbedBar);

  rootElement.space =document.createElement("DIV");
  rootElement.space.innerHTML == "&nbsp;";
  rootElement.space.className = "tabbedSpace";
  rootElement.appendChild(rootElement.space);


  rootElement.tabbedPane = document.createElement("DIV");
  rootElement.tabbedPane.className = "tabbedPane";
  rootElement.appendChild(rootElement.tabbedPane);

  for(var i = 0; i < paneModel.length; i++) {
    var div = document.createElement("span");
    span = document.createElement("SPAN");
    span.className = "tabLeftBefor";
    span.innerHTML = "&nbsp";
    div.appendChild(span);
    var span = document.createElement("SPAN");
    span.className = "tabLeft";
    span.innerHTML = "&nbsp";
    div.appendChild(span);
    span=document.createElement("SPAN");
    span.className = "tabCenter";
    span.appendChild(document.createTextNode(paneModel[i][0]));
    div.appendChild(span)
    span = document.createElement("SPAN");
    span.innerHTML = "&nbsp;"
    span.className = "tabRight";
    div.appendChild(span)
    span = document.createElement("SPAN");
    span.innerHTML = "&nbsp"
    span.className = "tabRightAfter";
    div.appendChild(span)
    div.className = "tab";
    div.adress = paneModel[i][1]
    div.onclick = bhv.tabbedpane.selectTab;
    rootElement.tabbedBar.appendChild(div);
    var pane = document.createElement("DIV");
    pane.className = "pane"
    rootElement.tabbedPane.appendChild(pane);
    div.pane = pane;
  }
}
