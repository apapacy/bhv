define(['bhv/widget/Combobox'], function(cmb) {
//////////////////////////////////////////////////////////////////////
function Constructor(element, valueElement, initialValue){
  this.derive(cmb, element, valueElement, initialValue, 10, "dbo.cennic", "kod", "name", "det")
};
Constructor.ISA = [cmb];
//Constructor.prototype.init=function(){}
return Constructor;
});
