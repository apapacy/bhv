define(['bhv/widget/Combobox'], function(cmb) {
//////////////////////////////////////////////////////////////////////
return function Constructor(element, valueElement, initialValue){
  this.derive(String).derive(Array).derive(cmb, true, element, valueElement, initialValue, 10, "dbo.cennic", "kod", "name", "det")
};
//Constructor.prototype.init=function(){}
});
