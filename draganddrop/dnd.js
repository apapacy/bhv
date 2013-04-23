if (typeof bhv == "undefined")
    bhv = {};

bhv.dnd = {};
bhv.dnd.draggedElement = null;
bhv.addEventListener = function(element, strEvent, callback){
      if (element.addEventListener) 
        element.addEventListener(strEvent, callback, false);
      else
        element.attachEvent("on" + strEvent, callback);
}

bhv.addEventListener(document, "mousemove", 
    function(event){
     event = event || window.event
     try{
      if (bhv.dnd.border.style.display == "block") {
        if (! bhv.dnd.isResize) {
          bhv.dnd.border.style.top = event.clientY - bhv.dnd.border.bhv_offsetTop + "px";
          bhv.dnd.border.style.left= event.clientX - bhv.dnd.border.bhv_offsetLeft + "px";
        } else {
          bhv.dnd.border.style.height = event.clientY - bhv.dnd.border.offsetTop + "px";
          bhv.dnd.border.style.width= event.clientX - bhv.dnd.border.offsetLeft + "px";
        }
      }
     }catch(ex){}
    }
)

    
bhv.addEventListener(document, "mouseup", 
    function(event){
      event = event || window.event
      if ( bhv.dnd.draggedElement)
        bhv.dnd.draggedElement.endDrag(event || window.event)
    }
)



bhv.addEventListener(document, "selectstart", 
    function(event){
        if (bhv.dnd.draggedElement)
            return false;
    }
)
bhv.addEventListener(document, "select", 
    function(event){
       if (bhv.dnd.draggedElement)
            return false;
    }
)

document.onmousedown = function() {
  if ( bhv.dnd.draggedElement)
    return false;
} 



function DnD(element, handle, resize){ 
  if (! handle)
    handle = element;
  this.element = element;
  this.handle = handle;
  this.resize = resize;
  this.element.dndmodel = this;
  handle.dndmodel = this;
  bhv.addEventListener(handle, "mousedown", this.startDrag)
  if (resize) {
    resize.dndmodel = this;
    bhv.addEventListener(resize, "mousedown", this.startDrag)
  }
}

DnD.prototype = {
    destroy: function(){
        this.element.dndmodel = null;
        this.element = null;
    }
,///////////////////////////////////////////////////////////////////////////////
    startDrag: function(event){
        event = event || window.event;
        if (! bhv.dnd.border) {
          bhv.dnd.border = document.createElement("DIV");
          bhv.dnd.border.style.borderStyle = "solid";
          bhv.dnd.border.style.borderWidth = "4px";
          bhv.dnd.border.style.borderColor = "red";
          bhv.dnd.border.style.position = "absolute";
          bhv.dnd.border.style.zIndex = "1000";
          document.getElementsByTagName("body")[0].appendChild(bhv.dnd.border);
        }
        var element;
        if (this.tagName)
          element = this;
        else {
          element = event.srcElement || event.target || event.relatedTarget;
          while (element && ! element.dndmodel)
            element = element.parentNode;
        }
        if (element == element.dndmodel.resize)
          bhv.dnd.isResize = true;
        else
          bhv.dnd.isResize = false;
        element = element.dndmodel.element
        bhv.dnd.draggedElement = element.dndmodel;
        element.dndmodel.currentX = element.offsetX;
        element.dndmodel.currentY = element.offsetY;
        element.dndmodel.pointX = event.clientX;
        element.dndmodel.pointY = event.clientY;
        bhv.dnd.border.style.width = element.offsetWidth + "px";
        bhv.dnd.border.style.height = element.offsetHeight + "px";
        bhv.dnd.border.style.left = bhv.left(element) + "px";
        bhv.dnd.border.style.top = bhv.top(element) + "px";
        bhv.dnd.border.bhv_offsetLeft = event.clientX - bhv.left(element);
        bhv.dnd.border.bhv_offsetTop = event.clientY - bhv.top(element);
        bhv.dnd.border.style.display = "block";

    }
,///////////////////////////////////////////////////////////////////////////////
    endDrag: function(event){
    bhv.dnd.draggedElement = null
    if (bhv.dnd.isResize) {
      this.element.style.height = bhv.dnd.border.offsetHeight + "px";
      this.element.style.width = bhv.dnd.border.offsetWidth + "px";
    try{
      bhv.dnd.border.style.display= "none";
      this.element.model.validate();
    }catch(ex){};
      return;
    }
    try{bhv.dnd.border.style.display= "none";}catch(ex){};
    if (this.element.style.position != "absolute")
      this.element.style.position="relative"
    if (! this.element.style.left)
      this.element.style.left = 0  + "px"
    this.element.style.left = parseInt(this.element.style.left) 
        - this.pointX + (event.clientX || 0)  + "px";
    if (! this.element.style.top)
      this.element.style.top = 0  + "px"
    this.element.style.top = parseInt(this.element.style.top) 
      - this.pointY + ( event.clientY) + "px";

    }
    

}
