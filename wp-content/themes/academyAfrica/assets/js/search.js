let searchFilters = {};
let sortParams = {};


function filterSearchCourses(input, field, value){
    if(input.checked){
        if(searchFilters[field]){
            searchFilters[field].push(value);
        } else {
            searchFilters[field] = [value]; 
        }
    } else {
        if(searchFilters[field]){
            searchFilters[field] = searchFilters[field].filter(item => item !== value);
        }
    }
    const newParams = Object.keys(searchFilters).map(key => {
        const values = searchFilters[key];
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
    searchFilters = {};

    // get all checked inputs
    const checkedInputs = filterModal.querySelectorAll("input:checked");
    checkedInputs.forEach(input => {
        const { name, value } = input;
        const [field, fieldValue] = name.split("-");

        if(searchFilters[field]){
            searchFilters[field].push(fieldValue);
        } else {
            searchFilters[field] = [fieldValue];
        }
    });

    const newParams = Object.keys(searchFilters).map(key => {
        const values = searchFilters[key];
        if(values.length){
            return `${key}=${values.join(",")}`;
        }
    })
    .filter(Boolean).map(item => encodeURI(item)).join("&");

    openUrl(newParams);
}

function sortCourses(el){
    const value = el.value;

    if(value){
        if(value === 'date-desc'){
        sortParams = {};
        } else {
            sortParams["sort"] = value;
        }
    }

    const newParams = Object.keys(sortParams).map(key => {
        const value = sortParams[key];
        return `${key}=${value}`;
    }).map(item => encodeURI(item)).join("&");

    openUrl(newParams);
}

function openUrl(newParams){
    const url = window.location.href;
    const urlParts = url.split("?");
    const baseUrl = urlParts[0];
    const params = urlParts[1];
    if(params){
        const paramsParts = params.split("&");
        const newParamsParts = paramsParts.filter(item => !item.includes("sort"));
        newParamsParts.push(newParams);
        window.location.search  = newParamsParts.join("&");
    } else {
        window.location.search = newParams;
    }
}

function toggleFilterModal(){
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
        if(searchFilters[key]){
            searchFilters[key].push(...values);
        } else {
            searchFilters[key] = values;
        }
    });
}

function filterPagination(){
    const filters= document.querySelectorAll("#side-filter-bar");

    filters.forEach(filter => {
        const filterItems = filter.querySelectorAll(".filter-item");
        filterItems.forEach(item => {
            const filterList = item.querySelector(".filter-list");
            if(filterList){
                const filterListItems = filterList.querySelectorAll("li");
                const filterToggle = item.querySelector(".show-more-btn");
    
                if(filterToggle){
                    filterToggle.addEventListener("click", () => {
                        filterListItems.forEach(listItem => {
                            if(listItem.classList.contains("hidden")){
                                listItem.classList.replace("hidden", "show");
                                filterToggle.innerHTML = "Show Less";
                            } else {
                                listItem.classList.replace("show", "hidden");
                                filterToggle.innerHTML = "Show More";
                            }
                        });  
                    });

                    const checkedInputs = filterList.querySelectorAll("input:checked");
                    if(checkedInputs.length){
                        filterToggle.style.display = "none";
                        filterListItems.forEach(listItem => {
                            if(listItem.classList.contains("hidden")){
                                listItem.classList.replace("hidden", "show");
                                filterToggle.innerHTML = "Show Less";
                            } else {
                                listItem.classList.replace("show", "hidden");
                                filterToggle.innerHTML = "Show More";
                            }
                        }); 
                        
                    }
                }
            }
        });
    });
}

function clearFilters(){
    searchFilters = {};
    window.location.search = "";
}

function mobileFilter(){
    const mobileFilterBtn = document.querySelector("#courses-mobile-filter");
    const allCourses = document.querySelector("#all-courses");
    const filterModal = document.querySelector("#filter-modal");
    const closeFilterModal = document.querySelectorAll("#close-filter-modal");
    
    if(!mobileFilterBtn || !allCourses || !filterModal || !closeFilterModal){
        return;
    }

     mobileFilterBtn.addEventListener("click", () => {
         allCourses.classList.toggle("d-none"); 
         filterModal.classList.toggle("d-none");
         window.scrollTo(0, 0);
     });
 
     closeFilterModal.forEach(item => {
         item.addEventListener("click", () => {
             allCourses.classList.toggle("d-none"); 
             filterModal.classList.toggle("d-none");
         });
     });
}

function viewAll(){
    const encodeURIParams = encodeURI(`view=all`);
    openUrl(encodeURIParams);
}

function removeFilter(field, value){
    const filter = document.querySelector(`input[name="${field}-${value}"]`);
    if(filter){
        filter.checked = false;
        filterSearchCourses(filter, field, value);
    }

}

function paginateCourses(page) {
    const url = new URL(window.location.href);
    if (page && page !== 1) {
        url.searchParams.set('course_page', page);
    } else {
        url.searchParams.delete('course_page');
    }
    window.location.href = url.toString();
}

function paginateLearningPath(page) {
    const url = new URL(window.location.href);
    if (page && page !== 1) {
        url.searchParams.set('lp_page', page);
    } else {
        url.searchParams.delete('lp_page');
    }
    window.location.href = url.toString();
}

window.addEventListener("DOMContentLoaded", () => {
    toggleFilterModal();
    filterPagination();
     mobileFilter();
 });
