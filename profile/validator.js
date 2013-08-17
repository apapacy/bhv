function Validator(input, event, callback) {
this.input = input;
this.output = document.createElement("span");
this.output.style.display = "none";
this.output.style.color = "red";
this.output.style.backgroundColor= "yellow";
this.output.style.overflow="visible";
this.output.style.position="absolute";
document.getElementsByTagName("BODY")[0].appendChild(this.output);

input[event]=callback
input.validator = this;

}

Validator.prototype.show = function(message) {
this.output.innerHTML = "<nobr>" + message + "</nobr>";
this.output.style.left= bhv.left(this.input) + this.input.offsetWidth+"px";
this.output.style.top= bhv.top(this.input)+"px";
this.output.style.display= "block";
}

Validator.prototype.hide = function() {
this.output.innerHTML = "";
this.output.style.display= "none";
}








