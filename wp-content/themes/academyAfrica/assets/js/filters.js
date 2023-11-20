function addAccordion() {
  const accordions = Array.from(document.getElementsByClassName("accordion"));

  accordions.forEach((element) => {
    element.addEventListener("click", function () {
      this.classList.toggle("active");
      const panel = this.nextElementSibling;
      panel.classList.toggle("open");
      panel.style.maxHeight = panel.style.maxHeight
        ? null
        : panel.scrollHeight + "px";
    });
  });
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
    filters.style.padding = "80px 60px";
  }
}

document.addEventListener("DOMContentLoaded", () => {

  addAccordion();
});

