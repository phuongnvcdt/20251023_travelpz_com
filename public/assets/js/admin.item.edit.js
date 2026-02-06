// Update select options
function updateSelect(select, data) {
  select.innerHTML = `<option value="">${select.getAttribute('data-empty-value') || ''}</option>`;
  data.forEach(item => {
    const opt = document.createElement('option');
    opt.value = item.slug;
    opt.textContent = item.trans_name || item.en_name || '';
    select.appendChild(opt);
  });
}

// Helper POST JSON
function postJSON(url, data, callback) {
  fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  })
    .then(res => res.json())
    .then(callback)
    .catch(err => console.error(err));
}


$(document).ready(function () {
  $('.subcategories-select').select2({
    placeholder: "  -- Select --",
    allowClear: true,
    width: '100%'
  });

  // Country → City
  const slCountry = document.getElementById('sl_country');
  const slCity = document.getElementById('sl_city');
  if (slCountry && slCity) {
    slCountry.addEventListener('change', function () {
      const selectedOption = this.options[this.selectedIndex];
      const countrySlug = selectedOption.getAttribute('data-slug');
      if (countrySlug) {
        postJSON(this.getAttribute('data-url'), { parent: countrySlug }, function (data) {
          updateSelect(slCity, data);
        });
      } else {
        slCity.innerHTML = `<option value="">${slCity.getAttribute('data-empty-value')}</option>`;
      }
    });
  }
});