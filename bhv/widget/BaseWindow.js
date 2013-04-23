mod.focuseWindow = function(win) {
  win = win || this;
  if (typeof win.div != "object" || win.div.className != "windowPanel")
    return;
  var els = mod.div.childNodes;
  for (var i = 0;i < els.length; i++)
    if (typeof els[i] == "object" && els[i].className == "windowPanel" && els[i] !=  win.div)
      if (parseInt(els[i].style.zIndex) != 50)
        els[i].toplevel = els[i].style.zIndex = 50;
  win.div.toplevel = win.div.style.zIndex = 51;
}
//----------------------------------------------------------------------------------------------------------
function BaseWindow(title) {
  if (typeof mod.div != "object")
    mod.div = document.getElementById("bhv_contentPane");
  var myself = this;
  this.isExpand = false;
  this.isCollapse = false;
  this.title = title || "xajA";
  this.focuseWindow = mod.focuseWindow;
  this.lastTop = (window.innerHeight || document.documentElement.clientHeight) * 0.1 + "px";
  this.lastLeft = (window.innerWidth || document.documentElement.clientWidth) * 0.1 + "px";
  this.lastWidth = (window.innerWidth || document.documentElement.clientWidth) * 0.8 + "px";
  this.lastHeight = (window.innerHeight || document.documentElement.clientHeight) * 0.6 + "px";
  var div = this.div = document.createElement("div");
  div.model = this;
  div.toplevel = 51;
  div.className = "windowPanel";
  div.style.position = "absolute";
  div.style.top = this.lastTop;
  div.style.left = this.lastLeft;
  div.style.width = this.lastWidth;
  div.style.height = this.lastHeight;
  div.style.visibility = "hidden";


  var head = this.head = document.createElement("div");
  head.model = this;
  head.className = "windowTitleBar";
  head.onmousedown = function(){this.model.focuseWindow();};


  var headW = this.headW = document.createElement("span");
  headW.model = this;
  headW.appendChild(document.createElement("b").appendChild(document.createTextNode("@j")));
  headW.className="windowW"

  var headX = this.headX = document.createElement("span");
  headX.appendChild(document.createTextNode("X"));
  headX.className = "windowTitleButton";
  headX.model = this;
  headX.onmousedown=function(event) {
	this.className = "windowPressedTitleButton";
	this.model.hide()
	event = event || window.event;
	event.cancelBubble = true;
	event.returnValue = false;
  }

  var headO = this.headO = document.createElement("span");
  headO.appendChild(document.createTextNode("O"))
  headO.className = "windowTitleButton";
  headO.model = this;
  headO.onmousedown = function(event) {
    headO.className = "windowPressedTitleButton";
    this.model.toggleExpand();
    event = event || window.event;
    event.cancelBubble = true;
    event.returnValue = false;
  }

  var headU = this.headU = document.createElement("span");
  headU.appendChild(document.createTextNode("_"));
  headU.className = "windowTitleButton";
  headU.model = this;
  headU.onmousedown = function(event) {
    this.className = "windowPressedTitleButton";
    this.model.toggleCollapse();
    event = event || window.event;
    event.cancelBubble = true;
    event.returnValue = false;
  }

  var headT = this.headT = document.createElement("span");
  headT.model = this;
  headT.appendChild(document.createTextNode(this.title));
  headT.className = "windowTitle";

  var footer = this.footer = document.createElement("div");
  footer.model = this;
  footer.className="windowTitleBar";
  footer.onmousedown = function(){this.model.focuseWindow();};

  var footerX = this.footerX = document.createElement("span");
  footerX.appendChild(document.createTextNode(".:i"));
  footerX.className = "windowFooterInsetBox";
  footerX.model = this;
  footerX.onmousedown = function(){this.model.focuseWindow();};

  new DnD(div, head, footerX);

  var content = this.content = document.createElement("div");
  content.model = this;
  content.className = "windowBody";
  content.onclick = function(){this.model.focuseWindow();};

  mod.div.appendChild(this.div)
  this.div.appendChild(head)
  this.head.appendChild(headW)
  this.head.appendChild(headT)
  this.head.appendChild(headX)
  this.head.appendChild(headO)
  this.head.appendChild(headU)
  this.div.appendChild(content)
  this.div.appendChild(footer)
  this.footer.appendChild(footerX)

  this.show = function(){
    this.focuseWindow();
    this.headX.className = "windowTitleButton";
    this.headO.className = "windowTitleButton";
    this.headU.className = "windowTitleButton";
    this.content.style.height = this.div.clientHeight 
      - this.head.offsetHeight - this.footer.offsetHeight+"px";
    this.div.style.visibility = "visible";
  }

  this.validate = this.show;
  this.hide = function(){
    setTimeout(function(){div.style.visibility = "hidden";},1000);
  }

  this.toggleExpand = function() {
    setTimeout(function() {
      myself.headO.className = "windowTitleButton";
      myself.content.style.display = "block";
      myself.footer.style.display = "block";
      if (! myself.isExpand) {
        myself.headO.innerHTML = "o";
        myself.isExpand = true;
        myself.headU.innerHTML = "_";
        if (! myself.isCollapse) {
          myself.lastTop = Math.max(myself.div.offsetTop, 0) + "px";
          myself.lastLeft = Math.max(myself.div.offsetLeft, 0) + "px";
          myself.lastWidth = myself.div.offsetWidth + "px";
          myself.lastHeight = myself.div.offsetHeight + "px";
        }
        myself.isCollapse = false;
        myself.div.style.top = "0px";
        myself.div.style.left = "0px";
        myself.div.style.width = (window.innerWidth || document.documentElement.clientWidth) + "px"
        myself.div.style.height = (window.innerHeight || document.documentElement.clientHeight) + "px"
      }else {
        myself.headO.innerHTML = "O";
        myself.isExpand = false;
        myself.headU.innerHTML = "_";
        myself.isCollapse = false;
        myself.div.style.top = myself.lastTop;
        myself.div.style.left = myself.lastLeft;
        myself.div.style.width = myself.lastWidth;
        myself.div.style.height = myself.lastHeight;
      }
      myself.show()
    },1000);
  }

  this.toggleCollapse = function() {
    setTimeout(function() {
      myself.headU.className = "windowTitleButton";
      if (! myself.isCollapse) {
        myself.headU.innerHTML = "o";
        myself.isCollapse = true;
        if (myself.isExpand) {
          myself.isExpand = false;
          myself.headO.innerHTML = "O";
        }else {
          myself.lastTop = Math.max(myself.div.offsetTop, 0) + "px";
          myself.lastLeft = Math.max(myself.div.offsetLeft, 0) + "px";
          myself.lastWidth = myself.div.offsetWidth + "px";
          myself.lastHeight = myself.div.offsetHeight + "px";
        }
        myself.div.style.top = myself.lastTop;
        myself.div.style.left = myself.lastLeft;
        myself.div.style.height = myself.head.offsetHeight + "px";
        myself.content.style.display = "none";
        myself.footer.style.display = "none";
        var minWidth = 0;
        for (var i = 0; i < myself.head.childNodes.length; i++)
          minWidth += parseInt(myself.head.childNodes[i].offsetWidth) + 10;
        myself.div.style.width = minWidth + "px";
        myself.div.style.height = myself.head.offsetHeight + "px";
      }else {
        myself.headU.innerHTML = "_";
        myself.isCollapse = false;
        myself.div.style.top = myself.lastTop;
        myself.div.style.left = myself.lastLeft;
        myself.div.style.width = myself.lastWidth;
        myself.div.style.height = myself.lastHeight;
        myself.content.style.display = "block";
        myself.footer.style.display = "block";
      }
      myself.show()
    },1000);
  }

}


