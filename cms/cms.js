define(['bhv/util', 'bhv/classes', 'domReady!'],
//domReady! don't work in early version  FF
//                                       patched by me
//---------------------------------------^^^^^^^^^^^^^^^^^^^^^^^^
//if (document.readyState === "complete" or ! document.readyState) {
//  pageLoaded();
//}
function (util, classes) {
alert(1)
var cms = new Object()
cms.bhv = cms.util = util;
cms.classes = classes;
cms.jq = jQuery;
cms.jq.ajaxSetup ({
    cache: false
});
cms.us = _;
cms.bb = Backbone;
alert(cms);
return cms;
});



