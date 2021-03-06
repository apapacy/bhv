function accordionTitleOnClick() {
    var accordion = this.parentNode;
    var childs = accordion.childNodes;
    var div = [];

    for  (var i = 0; i < childs.length; i++)
        if (childs[i].tagName == "DIV")
            div[div.length] = childs[i];

    var currentTitle = null;
    var currentPane = null;
    var nextTitle = this;
    var nextPane = null;

    for (var i = 0; i < div.length; i ++)
        if (div[i] == nextTitle)
            nextPane = div[i+1]

    if (this.contentURL && !this.isLoaded){
      this.isLoaded = true;
      bhv.sendRequest("get",this.contentURL,null,true,function(pane){pane.innerHTML = this.responseText},null,[nextPane])
    }

    for (var i = 0; i < div.length; i ++)
        if (div[i].className == "accordionCurrentTitle")
            currentTitle = div[i];
        else if (div[i].className == "accordionCurrentPane")
            currentPane = div[i];



    currentTitle.className = "accordionOtherTitle";
    nextTitle.className = "accordionCurrentTitle";
    currentPane.className = "accordionOtherPane";
    nextPane.className = "accordionCurrentPane";   
    nextPane.style.height = calculatePaneHeight(accordion)+"px";    



}

function accordionAddBehivior(accordion, model,activate) {
    if (! activate)
      activate = 0;
    var accordion = document.getElementById(accordion);
    accordion.className = "accordionComponent";
    if (model instanceof Array)
      for  (var i = 0; i < model.length; i++){
        var div = document.createElement("DIV");
        div.appendChild(document.createTextNode(model[i][0]))
        div.contentURL = model[i][1];
        div.isLoaded = false;
        accordion.appendChild(div)
        div = document.createElement("DIV");
        div.appendChild(document.createTextNode(model[i][0]))
        accordion.appendChild(div)        
      }
        

    var childs = accordion.childNodes;
    var div = [];

    for  (var i = 0; i < childs.length; i++)
        if (childs[i].tagName == "DIV")
            div[div.length] = childs[i];


    for (var i = 0; i < div.length; i += 2){
        div[i].onclick = accordionTitleOnClick;
        if (i == activate*2){
            div[i].className = "accordionCurrentTitle";
            div[i+1].className = "accordionCurrentPane";
            if (model instanceof Array)
                div[i].onclick();
            
        }
        else{
            div[i].className = "accordionOtherTitle";
            div[i+1].className = "accordionOtherPane";
        }
    }   

    div[1].style.height = calculatePaneHeight(accordion)+"px";

}

function calculatePaneHeight(accordion){

    var childs = accordion.childNodes;
    var div = [];

   for  (var i = 0; i < childs.length; i++)
       if (childs[i].tagName == "DIV")
           div[div.length] = childs[i];

    var paneHeight = accordion.clientHeight;

    for  (var i = 0; i < div.length; i += 2)
        paneHeight -= div[i].offsetHeight;


    return paneHeight;

}