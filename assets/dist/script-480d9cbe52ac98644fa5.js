!function(){var t={960:function(t,e,r){t.exports=r(410)},410:function(t){var e=function(t){"use strict";var e,r=Object.prototype,n=r.hasOwnProperty,o="function"==typeof Symbol?Symbol:{},a=o.iterator||"@@iterator",i=o.asyncIterator||"@@asyncIterator",c=o.toStringTag||"@@toStringTag";function s(t,e,r){return Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}),t[e]}try{s({},"")}catch(t){s=function(t,e,r){return t[e]=r}}function u(t,e,r,n){var o=e&&e.prototype instanceof v?e:v,a=Object.create(o.prototype),i=new j(n||[]);return a._invoke=function(t,e,r){var n=f;return function(o,a){if(n===p)throw new Error("Generator is already running");if(n===d){if("throw"===o)throw a;return T()}for(r.method=o,r.arg=a;;){var i=r.delegate;if(i){var c=_(i,r);if(c){if(c===m)continue;return c}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if(n===f)throw n=d,r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);n=p;var s=h(t,e,r);if("normal"===s.type){if(n=r.done?d:l,s.arg===m)continue;return{value:s.arg,done:r.done}}"throw"===s.type&&(n=d,r.method="throw",r.arg=s.arg)}}}(t,r,i),a}function h(t,e,r){try{return{type:"normal",arg:t.call(e,r)}}catch(t){return{type:"throw",arg:t}}}t.wrap=u;var f="suspendedStart",l="suspendedYield",p="executing",d="completed",m={};function v(){}function y(){}function g(){}var w={};s(w,a,(function(){return this}));var x=Object.getPrototypeOf,b=x&&x(x(N([])));b&&b!==r&&n.call(b,a)&&(w=b);var L=g.prototype=v.prototype=Object.create(w);function P(t){["next","throw","return"].forEach((function(e){s(t,e,(function(t){return this._invoke(e,t)}))}))}function k(t,e){function r(o,a,i,c){var s=h(t[o],t,a);if("throw"!==s.type){var u=s.arg,f=u.value;return f&&"object"==typeof f&&n.call(f,"__await")?e.resolve(f.__await).then((function(t){r("next",t,i,c)}),(function(t){r("throw",t,i,c)})):e.resolve(f).then((function(t){u.value=t,i(u)}),(function(t){return r("throw",t,i,c)}))}c(s.arg)}var o;this._invoke=function(t,n){function a(){return new e((function(e,o){r(t,n,e,o)}))}return o=o?o.then(a,a):a()}}function _(t,r){var n=t.iterator[r.method];if(n===e){if(r.delegate=null,"throw"===r.method){if(t.iterator.return&&(r.method="return",r.arg=e,_(t,r),"throw"===r.method))return m;r.method="throw",r.arg=new TypeError("The iterator does not provide a 'throw' method")}return m}var o=h(n,t.iterator,r.arg);if("throw"===o.type)return r.method="throw",r.arg=o.arg,r.delegate=null,m;var a=o.arg;return a?a.done?(r[t.resultName]=a.value,r.next=t.nextLoc,"return"!==r.method&&(r.method="next",r.arg=e),r.delegate=null,m):a:(r.method="throw",r.arg=new TypeError("iterator result is not an object"),r.delegate=null,m)}function E(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function O(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function j(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(E,this),this.reset(!0)}function N(t){if(t){var r=t[a];if(r)return r.call(t);if("function"==typeof t.next)return t;if(!isNaN(t.length)){var o=-1,i=function r(){for(;++o<t.length;)if(n.call(t,o))return r.value=t[o],r.done=!1,r;return r.value=e,r.done=!0,r};return i.next=i}}return{next:T}}function T(){return{value:e,done:!0}}return y.prototype=g,s(L,"constructor",g),s(g,"constructor",y),y.displayName=s(g,c,"GeneratorFunction"),t.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===y||"GeneratorFunction"===(e.displayName||e.name))},t.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,g):(t.__proto__=g,s(t,c,"GeneratorFunction")),t.prototype=Object.create(L),t},t.awrap=function(t){return{__await:t}},P(k.prototype),s(k.prototype,i,(function(){return this})),t.AsyncIterator=k,t.async=function(e,r,n,o,a){void 0===a&&(a=Promise);var i=new k(u(e,r,n,o),a);return t.isGeneratorFunction(r)?i:i.next().then((function(t){return t.done?t.value:i.next()}))},P(L),s(L,c,"Generator"),s(L,a,(function(){return this})),s(L,"toString",(function(){return"[object Generator]"})),t.keys=function(t){var e=[];for(var r in t)e.push(r);return e.reverse(),function r(){for(;e.length;){var n=e.pop();if(n in t)return r.value=n,r.done=!1,r}return r.done=!0,r}},t.values=N,j.prototype={constructor:j,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=e,this.done=!1,this.delegate=null,this.method="next",this.arg=e,this.tryEntries.forEach(O),!t)for(var r in this)"t"===r.charAt(0)&&n.call(this,r)&&!isNaN(+r.slice(1))&&(this[r]=e)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var r=this;function o(n,o){return c.type="throw",c.arg=t,r.next=n,o&&(r.method="next",r.arg=e),!!o}for(var a=this.tryEntries.length-1;a>=0;--a){var i=this.tryEntries[a],c=i.completion;if("root"===i.tryLoc)return o("end");if(i.tryLoc<=this.prev){var s=n.call(i,"catchLoc"),u=n.call(i,"finallyLoc");if(s&&u){if(this.prev<i.catchLoc)return o(i.catchLoc,!0);if(this.prev<i.finallyLoc)return o(i.finallyLoc)}else if(s){if(this.prev<i.catchLoc)return o(i.catchLoc,!0)}else{if(!u)throw new Error("try statement without catch or finally");if(this.prev<i.finallyLoc)return o(i.finallyLoc)}}}},abrupt:function(t,e){for(var r=this.tryEntries.length-1;r>=0;--r){var o=this.tryEntries[r];if(o.tryLoc<=this.prev&&n.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var a=o;break}}a&&("break"===t||"continue"===t)&&a.tryLoc<=e&&e<=a.finallyLoc&&(a=null);var i=a?a.completion:{};return i.type=t,i.arg=e,a?(this.method="next",this.next=a.finallyLoc,m):this.complete(i)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),m},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.finallyLoc===t)return this.complete(r.completion,r.afterLoc),O(r),m}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.tryLoc===t){var n=r.completion;if("throw"===n.type){var o=n.arg;O(r)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(t,r,n){return this.delegate={iterator:N(t),resultName:r,nextLoc:n},"next"===this.method&&(this.arg=e),m}},t}(t.exports);try{regeneratorRuntime=e}catch(t){"object"==typeof globalThis?globalThis.regeneratorRuntime=e:Function("r","regeneratorRuntime = r")(e)}}},e={};function r(n){var o=e[n];if(void 0!==o)return o.exports;var a=e[n]={exports:{}};return t[n](a,a.exports,r),a.exports}r.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return r.d(e,{a:e}),e},r.d=function(t,e){for(var n in e)r.o(e,n)&&!r.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:e[n]})},r.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},function(){"use strict";function t(t,e,r,n,o,a,i){try{var c=t[a](i),s=c.value}catch(t){return void r(t)}c.done?e(s):Promise.resolve(s).then(n,o)}function e(e){return function(){var r=this,n=arguments;return new Promise((function(o,a){var i=e.apply(r,n);function c(e){t(i,o,a,c,s,"next",e)}function s(e){t(i,o,a,c,s,"throw",e)}c(void 0)}))}}var n=r(960),o=r.n(n),a=function(){var t=e(o().mark((function t(){return o().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,fetch(cardanoPress.ajaxUrl,{method:"POST",body:new URLSearchParams({_wpnonce:cardanoPress._nonce,action:"cp-ispo_delegation_data"})}).then((function(t){return t.json()}));case 2:return t.abrupt("return",t.sent);case 3:case"end":return t.stop()}}),t)})));return function(){return t.apply(this,arguments)}}(),i=function(){var t=e(o().mark((function t(){var e,r,n;return o().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,a();case 2:if((e=t.sent).success){t.next=5;break}return t.abrupt("return",e);case 5:return r=e.data,t.next=8,cardanoPress.wallet.delegationTx(r);case 8:if(!(n=t.sent).success){t.next=13;break}return t.next=12,cardanoPress.api.saveWalletTx(n.data.network,"delegation",n.data.transaction);case 12:return t.abrupt("return",t.sent);case 13:return t.abrupt("return",n);case 14:case"end":return t.stop()}}),t)})));return function(){return t.apply(this,arguments)}}(),c=function(){var t=e(o().mark((function t(e){return o().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,fetch(cardanoPress.ajaxUrl,{method:"POST",body:new URLSearchParams({_wpnonce:cardanoPress._nonce,action:"cp-ispo_track_rewards",stakeAddress:e})}).then((function(t){return t.json()}));case 2:return t.abrupt("return",t.sent);case 3:case"end":return t.stop()}}),t)})));return function(e){return t.apply(this,arguments)}}();window.addEventListener("alpine:init",(function(){var t=window.Alpine||{},r=window.cardanoPress||{};t.data("cardanoPressISPO",(function(){return{isProcessing:!1,ration:1,minimum:1,maximum:2,commence:1,conclude:2,control:1,limit:2,delegate:1,epochs:1,address:"",trackedReward:"",transactionHash:"",extraReward:null,init:function(){var t=this;return e(o().mark((function e(){return o().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:t.ration=parseFloat(t.$root.dataset.ration),t.minimum=parseInt(t.$root.dataset.minimum),t.maximum=parseInt(t.$root.dataset.maximum),t.commence=parseInt(t.$root.dataset.commence),t.conclude=parseInt(t.$root.dataset.conclude),t.delegate=t.minimum,t.limit=t.control+t.conclude-t.commence,console.log("CardanoPress ISPO ready!");case 8:case"end":return e.stop()}}),e)})))()},getRewards:function(){return this.delegate<this.minimum&&(this.delegate=this.minimum),this.delegate>this.maximum&&(this.delegate=this.maximum),this.epochs<this.control&&(this.epochs=this.control),this.epochs>this.limit&&(this.epochs=this.limit),(this.ration/100*this.delegate*this.epochs).toFixed(6)},handleDelegation:function(){var t=this;return e(o().mark((function e(){var n;return o().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t.transactionHash="",r.api.addNotice({id:"ispo-delegation",type:"info",text:"Processing..."}),t.isProcessing=!0,e.next=5,i();case 5:n=e.sent,r.api.removeNotice("ispo-delegation"),n.success?(t.transactionHash=n.data.hash,r.api.addNotice({type:"info",text:n.data.message})):r.api.addNotice({type:"warning",text:n.data}),t.isProcessing=!1;case 9:case"end":return e.stop()}}),e)})))()},handleTracking:function(){var t=this;return e(o().mark((function e(){var n;return o().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t.trackedReward="",t.extraReward=null,r.api.addNotice({id:"ispo-tracking",type:"info",text:"Tracking..."}),t.isProcessing=!0,e.next=6,c(t.address);case 6:n=e.sent,r.api.removeNotice("ispo-tracking"),n.success?(t.trackedReward=n.data.amount.toFixed(6),t.extraReward=n.data.extra,r.api.addNotice({type:"info",text:n.data.message})):r.api.addNotice({type:"warning",text:n.data}),t.isProcessing=!1;case 10:case"end":return e.stop()}}),e)})))()}}}))}))}()}();