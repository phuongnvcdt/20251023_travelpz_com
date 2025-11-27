
// script for fetch iframe-init.js from cdn

(function () {
  // if (document.querySelector('script[data-klk-aff-script]')) return;
  window.onerror = function (e) {
    console.log('全局错误', e);
  }
  var script = document.createElement("script");
  script.src = "https://cdn.klook.com/s/dist_web/klook-affiliate-front/static/widget/iframe/iframe-init-v1.0.10.js";
  script.async = true;
  // script.setAttribute("data-klk-aff-script", "true");
  script.crossorigin = "anonymous";
  script.onload = function () {
    // DO STH
  };
  document.head.appendChild(script);
})();
    