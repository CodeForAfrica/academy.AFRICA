document.addEventListener("DOMContentLoaded", () => {

    const course_grids = document.querySelectorAll(".learndash-course-grid");
    course_grids.forEach(grid => {
        const course_items = grid.querySelectorAll(".item");
        
        course_items.forEach(course => {
            const price_ribbon = course.querySelector(".ribbon");
            const bottom_meta = course.querySelector(".bottom-meta");
            const price = price_ribbon.textContent;
            add_price_tag(price, bottom_meta)
        });
    });

    function add_price_tag(price, course){
        const price_tag = document.createElement("div");
        price_tag.classList.add("price-tag");
        price_tag.textContent = price;
        course.appendChild(price_tag);
    }
});