
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

// Category → Subcategory
const slCat = document.getElementById('sl_cat');
const slSubCat = document.getElementById('sl_sub_cat');
if (slCat && slSubCat) {
  slCat.addEventListener('change', function () {
    const catSlug = this.value;
    if (catSlug) {
      postJSON(this.getAttribute('data-url'), { parent: catSlug }, function (data) {
        updateSelect(slSubCat, data);
      });
    } else {
      slSubCat.innerHTML = `<option value="">${slSubCat.getAttribute('data-empty-value')}</option>`;
    }
  });
}

// Country → City
const slCountry = document.getElementById('sl_country');
const slCity = document.getElementById('sl_city');
if (slCountry && slCity) {
  slCountry.addEventListener('change', function () {
    const countrySlug = this.value;
    if (countrySlug) {
      postJSON(this.getAttribute('data-url'), { parent: countrySlug }, function (data) {
        updateSelect(slCity, data);
      });
    } else {
      slCity.innerHTML = `<option value="">${slCity.getAttribute('data-empty-value')}</option>`;
    }
  });
}

// Button Search
const btnSearch = document.getElementById('btn_search');
const form = document.getElementById('form');
const tbKeyword = document.getElementById('tb_keyword');

if (btnSearch && form) {
  btnSearch.addEventListener('click', function () {
    const cat = (slSubCat?.value || slCat?.value || '').trim();
    const loc = (slCity?.value || slCountry?.value || '').trim();
    const q = tbKeyword?.value.trim() || '';

    const params = [];
    if (q) params.push(`q=${encodeURIComponent(q)}`);
    if (cat) params.push(`cat=${encodeURIComponent(cat)}`);
    if (loc) params.push(`loc=${encodeURIComponent(loc)}`);

    let url = form.getAttribute('data-url');
    if (params.length) url += '?' + params.join('&');

    window.location.href = url;
  });
}