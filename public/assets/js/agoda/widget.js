document.addEventListener("DOMContentLoaded", function () {
  function initAgodaWidget() {
    const searchBox = document.querySelector('.agoda-search-box');
    if (!searchBox) return;

    const wid = searchBox.dataset.wid;
    const aid = searchBox.dataset.aid;

    const stg = {
      crt: "5075562175139",
      version: "1.04",
      id: wid,
      name: wid,
      width: "407px",
      height: "340px",
      ReferenceKey: "dJ/imULN4iOEr4QraV+1/w==",
      Layout: "Square",
      Language: "en-us",
      Cid: aid,
      DestinationName: "",
      OverideConf: false
    };

    // Nếu AgdSherpa chưa load, load từ local
    if (typeof AgdSherpa === 'undefined') {
      const s = document.createElement('script');
      s.src = '/assets/js/agoda/sherpa_init1_08.min.js'; // file JS local
      s.async = true;
      s.onload = () => new AgdSherpa(stg).initialize();
      document.body.appendChild(s);
    } else {
      new AgdSherpa(stg).initialize();
    }
  }

  function scaleAgodaWidget() {
    const searchBox = document.querySelector('.agoda-search-box');
    if (!searchBox) return;

    const agdWidget = document.getElementById(searchBox.dataset.wid);
    if (!agdWidget) return;

    const containerWidth = searchBox.offsetWidth;
    const widgetWidth = agdWidget.offsetWidth || 407; // fallback width
    const scale = containerWidth / widgetWidth;

    agdWidget.style.transform = `scale(${scale})`;
    agdWidget.style.transformOrigin = 'top left';
  }

  function setAgodaIframeTitle() {
    const container = document.getElementById("adgshp-1853849391");
    if (!container) return;

    const iframe = container.querySelector("iframe");
    if (iframe) {
      iframe.setAttribute("title", "Agoda search widget");
    } else {
      // thử lại sau 100ms nếu iframe chưa load
      setTimeout(setAgodaIframeTitle, 100);
    }
  }

  // Lazy load: gọi sau DOM sẵn sàng và delay 100ms
  setTimeout(() => {
    initAgodaWidget();
    scaleAgodaWidget();
    setAgodaIframeTitle();
  }, 100);

  // Re-scale khi resize
  let resizeTimeout;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(scaleAgodaWidget, 100);
  });
});