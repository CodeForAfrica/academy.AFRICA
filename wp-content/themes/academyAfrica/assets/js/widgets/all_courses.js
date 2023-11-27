let filters = {};


document.addEventListener("DOMContentLoaded", () => {
   const mobileFilterBtn = document.querySelector("#courses-mobile-filter");
   const allCourses = document.querySelector("#all-courses");
   const filterModal = document.querySelector("#filter-modal");
   const closeFilterModal = document.querySelector("#close-filter-modal");
   
    mobileFilterBtn.addEventListener("click", () => {
        allCourses.classList.toggle("d-none"); 
        filterModal.classList.toggle("d-none");
        window.scrollTo(0, 0);
    });

    closeFilterModal.addEventListener("click", () => {
        allCourses.classList.toggle("d-none"); 
        filterModal.classList.toggle("d-none");
    });
});


 
function filterCourses(input, field, value){
    if(input.checked){
        if(filters[field]){
            filters[field].push(value);
        } else {
            filters[field] = [value]; 
        }
    } else {
        if(filters[field]){
            filters[field] = filters[field].filter(item => item !== value);
        }
    }
    const newParams = Object.keys(filters).map(key => {
        const values = filters[key];
        if(values.length){
            return `${key}=${values.join(",")}`;
        }
    }).filter(Boolean).map(item => encodeURI(item)).join("&");
    window.location.search = newParams;
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

function applyFilters(){
    const filterModal = document.querySelector("#filter-modal");

    // get all checked inputs
    const checkedInputs = filterModal.querySelectorAll("input:checked");
    checkedInputs.forEach(input => {
        const { name, value } = input;
        const [field, fieldValue] = name.split("-");
        filterCourses(input, field, fieldValue);
    });



}


window.addEventListener("DOMContentLoaded", () => {
    const { search } = window.location;
    const params = parseQueryString(search);
    Object.keys(params).forEach(key => {
        const values = params[key];
        values.forEach(value => {
            const input = document.getElementsByName(`${key}-${value}`);
            input.forEach(element => {
                if(element){
                    element.checked = true;
                }
            });
        });
        if(filters[key]){
            filters[key].push(...values);
        } else {
            filters[key] = values;
        }
    });

});

function clearFilters(){
    filters = {};
    window.location.search = "";
}