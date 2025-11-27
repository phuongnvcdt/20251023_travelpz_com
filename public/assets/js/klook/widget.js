document.addEventListener("DOMContentLoaded", function () {
  // Tìm tất cả widget chưa load
  document.querySelectorAll('ins.klk-aff-widget.lazy-iframe').forEach(ins => {
    // Tạo script mới từ file local
    var s = document.createElement('script');
    s.type = 'text/javascript';
    s.async = true;
    s.src = '/assets/js/klook/fetch-iframe-init.js';
    ins.appendChild(s); // hoặc append vào head/body nếu cần
    ins.classList.remove('lazy-iframe'); // đánh dấu đã load
  });
});