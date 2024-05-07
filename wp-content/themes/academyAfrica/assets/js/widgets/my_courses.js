let filters = {};

function removeFilter(key, value) {
  filters[key] = filters[key].filter((v) => v !== value);
  applyFilters();
}

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
    filters.style.padding = "40px";
  }
}

function changeSort(input) {
  filters.sort = input.value;
  applyFilters();
}
function filterCourses(input, field, value) {
  if (input.checked) {
    if (filters[field]) {
      filters[field].push(value);
    } else {
      filters[field] = [value];
    }
  } else {
    if (filters[field]) {
      filters[field] = filters[field].filter((item) => item !== value);
    }
  }
  const newParams = Object.keys(filters)
    .map((key) => {
      const values = filters[key];
      if (values.length) {
        return `${key}=${values.join(",")}`;
      }
    })
    .filter(Boolean)
    .map((item) => encodeURI(item))
    .join("&");
  window.location.search = newParams;
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
    if (!["certificates_page", "courses_page"].includes(key)) {
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
  // globalOnload();
  const { search } = window.location;
  filters = parseQueryString(search);
  Object.keys(filters).forEach((key) => {
    if (!["certificates_page", "courses_page", "sort"].includes(key)) {
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
};
