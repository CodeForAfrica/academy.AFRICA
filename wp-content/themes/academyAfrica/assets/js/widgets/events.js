let filters = {};

function removeFilter(key, value) {
  filters[key] = filters[key].filter((v) => v !== value);
  applyFilters();
}

function addSelectedChips() {
  let innerHtml = ``;
  Object.keys(filters).forEach((key) => {
    const values = filters[key];
    values.forEach((value) => {
      if (value) {
        innerHtml += `<div class="chip">${value} <div style="cursor: pointer;" onclick="removeFilter('${key}', '${value}')"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
            <g clip-path="url(#clip0_11179_27069)">
                <path d="M8.0026 14.6693C11.6845 14.6693 14.6693 11.6845 14.6693 8.0026C14.6693 4.32071 11.6845 1.33594 8.0026 1.33594C4.32071 1.33594 1.33594 4.32071 1.33594 8.0026C1.33594 11.6845 4.32071 14.6693 8.0026 14.6693Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M10 6L6 10" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M6 6L10 10" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
            </g>
            <defs>
                <clipPath id="clip0_11179_27069">
                    <rect width="16" height="16" fill="white" />
                </clipPath>
            </defs>
        </svg></div></div>`;
      }
    });
  });
  const el = document.getElementById("selected-filters");
  if (el) {
    el.innerHTML = innerHtml;
  }
}

function applyFilters() {
  const query = buildQueryString(filters);
  const currentUrl = window.location.pathname;
  const newUrl = `${currentUrl}?${query}`;
  window.location.href = newUrl;
}

function parseQueryString(queryString) {
  const params = {};
  queryString = queryString.replace(/^\?/, "");
  const pairs = queryString.split("&");
  pairs.forEach((pair) => {
    const [key, value] = pair.split("=");
    const decodedKey = decodeURIComponent(key);
    const decodedValue = decodeURIComponent(value || "");
    if (!["previous_events_page", "upcoming_page"].includes(key)) {
      if (decodedValue.includes(",")) {
        params[decodedKey] = decodedValue.split(",");
      } else {
        params[decodedKey] = [decodedValue];
      }
    }
  });
  return params;
}

function buildQueryString(object) {
  const queryParts = [];

  Object.keys(object).forEach((key) => {
    const value = object[key];
    if (Array.isArray(value)) {
      const encodedValues = value.map(encodeURIComponent);
      queryParts.push(`${encodeURIComponent(key)}=${encodedValues.join(",")}`);
    } else {
      queryParts.push(
        `${encodeURIComponent(key)}=${encodeURIComponent(value)}`
      );
    }
  });
  return queryParts.join("&");
}

function onChangeCheckBox(input, category, value, apply) {
  let selectedFilters = filters[category] || [];
  if (input.checked) {
    selectedFilters.push(value);
  } else {
    selectedFilters = selectedFilters.filter((item) => item !== value);
  }
  filters[category] = [...new Set(selectedFilters)];
  if (apply) applyFilters();
}

window.onload = function onLoad() {
  const { search } = window.location;
  filters = parseQueryString(search);
  Object.keys(filters).forEach((key) => {
    if (!["previous_events_page", "upcoming_page"].includes(key)) {
      filters[key].forEach((selected) => {
        const elements = document.getElementsByName(`${key}-${selected}`);
        [...elements].forEach((element) => {
          if (element) {
            element.checked = true;
          }
        });
      });
    }
  });
  addSelectedChips();
};

function clearFilters() {
  filters = {};
  applyFilters();
}

function closeFilters() {
  const filters = document.getElementById("filters");
  if (filters) {
    filters.style.width = 0;
    filters.style.overflowX = "hidden";
    filters.style.padding = 0;
  }
}

function openFilters() {
  const filters = document.getElementById("filters");
  if (filters) {
    filters.style.width = "100%";
  }
}
