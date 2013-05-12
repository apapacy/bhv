define(['bhv/widget/Combobox'], function(cmb) {
//////////////////////////////////////////////////////////////////////
return function Constructor(element, valueElement, initialValue){
  this.derive(cmb,true,[element, valueElement, initialValue, 10,"dbo.cennic", "kod", "name", "name"])
};
//Constructor.prototype.init=function(){}
});
