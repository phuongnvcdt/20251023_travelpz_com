(function() {
  let insClassName = 'kkday-product-media',
      iframeClassName = 'kkday-product-media-iframe'
  // 计算高度
  function computeHeight(w, num) {
    let h = 484
    if(num == 6) h = 808 // 6 => 3
    if(w < 960 && num == 4) h = 808 // 4 => 2
    if(w < 720 && num == 6) h = 1132 // 6 => 2
    if(w < 720 && num == 3) h = 1247 // 3 => 1
    if(w < 496 && num == 6) h = 2318 // 6 => 1
    if(w < 496 && num == 4) h = 1604 // 4 => 1
    return h + 'px'
  }
  // 获取iframes并调用计算高度
  function handleResize() {
    let iframes = document.getElementsByClassName(iframeClassName)
    for(let i = 0; i < iframes.length; i++){
      iframes[i].style.height = computeHeight(iframes[i].offsetWidth, parseInt(iframes[i].getAttribute('data-amount')))
    }
  }
  // 构建iframe
  let elements = document.getElementsByClassName(insClassName)
  if(elements.length > 0) { 
    for(let i = 0; i < elements.length; i++){
      if(elements[i].childNodes.length > 0) continue // 防止重复构建iframe
      let iframe = document.createElement('iframe'),
          oid = elements[i].getAttribute('data-oid'),
          origin = elements[i].getAttribute('data-origin'),
          refer = location.href
      iframe.className = iframeClassName
      iframe.setAttribute('data-amount', elements[i].getAttribute('data-amount'))
      iframe.setAttribute('scrolling', 'no')
      iframe.setAttribute('frameborder', '0')
      iframe.setAttribute('marginheight', '0')
      iframe.setAttribute('marginwidth', '0')
      iframe.setAttribute('allowtransparency', 'true')
      iframe.src = `${origin}/product-media?oid=${oid}&refer=${refer}`
      iframe.style = 'border: none;padding: 0px;margin: 0px;overflow: hidden;max-width: none;width: 100%;height: 484px;'
      elements[i].append(iframe)
      handleResize()
    }
  }
  // 视图大小变化时，重新计算高度
  window.addEventListener('resize', handleResize);
})()
