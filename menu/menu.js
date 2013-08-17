function showPopup(element){
  var popup= firstElement(element.childNodes);
  if (popup) {
    popup.offsetWidth;
    popup.style.top = bhv.top(element)+element.offsetHeight+"px";
    popup.style.left = bhv.left(element) + "px";
    popup.offsetWidth;
  } else
    return;  
  for (var i = 0; i < popup.childNodes.length; i++)
    if (popup.childNodes[i].itemDisabled)
      if (popup.childNodes[i].itemDisabled())
        popup.childNodes[i].className = "itemDisabled";
      else
        popup.childNodes[i].className = "itemOther";  
}

function firstElement(nodes) {
  for(var i = 0; i < nodes.length; i++)
    if (nodes[i].nodeType == 1)
      return nodes[i];
  return undefined;
}

function div_onmouseout() {
    this.className = 'other';
}

function div_onmouseover() {
  showPopup(this);
  this.className = 'selected';
}

function addMenuBehivior(element, o) {
  for (var i = 0; i < o.length; i += 2) {
    var div = document.createElement("DIV");
    div.appendChild(document.createTextNode(o[i]));
    element.appendChild(div);
    div.className = "other";
    div.onmouseout = div_onmouseout;
    div.onmouseover = div_onmouseover;
    if (typeof o[i+1] == "function")
        div.onclick = o[i+1];
    else if (typeof o[i+1] == "object" && o[i+1].length)
        addPopup(div, o[i+1]);    
  }
}

function item_onmouseover(){
  this.className = 'itemSelected';
}

function item_onmouseout() {
  this.className = 'itemOther';
}

function item_onclick() {
  if (! this.isDisabled())
    this.onClick();
}


function addPopup(element, o) {
  var div = document.createElement("DIV");
  div.className = "popup";
  element.appendChild(div);
  for (var i=0; i < o.length; i+=2) {
    var item = document.createElement("DIV");
    item.appendChild(document.createTextNode(o[i]));
    div.appendChild(item);
    item.className = "itemOther";
    item.onmouseout = item_onmouseout;
    item.onmouseover = item_onmouseover;
    if (typeof o[i+1] == "function")
      item.onclick = o[i+1];
    else if (typeof o[i+1] == "object") 
      for (var p in o[i+1]) 
        if (o[i+1][p] != Object.prototype[p]) {
          item[p] = o[i+1][p];
          item[p].onclick = item_onclick;
        }
  }
}
