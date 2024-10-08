document.addEventListener("DOMContentLoaded", () => {
  const hamburger = document.querySelector(".hamburger");
  const openIcon = document.querySelector(".icon.open");
  const closeIcon = document.querySelector(".icon.close");
  const drawer = document.querySelector(".drawer");

  hamburger.addEventListener("click", () => {
    drawer.classList.toggle("open");
    openIcon.style.display =
      openIcon.style.display === "none" ? "block" : "none";
    closeIcon.style.display =
      closeIcon.style.display === "none" ? "block" : "none";
  });

  const parentNavs = document.querySelectorAll(".item.parent");

  parentNavs.forEach((parentNav) => {
    const childNav = parentNav.querySelector(".children");
    parentNav.addEventListener("click", () => {
      parentNav.classList.toggle("open");
      childNav.classList.toggle("open");

      // close other open navs
      parentNavs.forEach((nav) => {
        if (nav !== parentNav) {
          nav.classList.remove("open");
          nav.querySelector(".children").classList.remove("open");
        }
      });

      // close if clicked outside
      document.addEventListener("click", (e) => {
        if (!parentNav.contains(e.target)) {
          parentNav.classList.remove("open");
          childNav.classList.remove("open");
        }
      });
    });
  });

  const searchIcon = document.querySelector(".search-icon");
  const searchForm = document.querySelector(".mobile-search");
  const searchClose = document.querySelectorAll("#search-close-btn");
  const mobileNav = document.querySelector("#mobile-nav");


  searchIcon.addEventListener("click", () => {
    searchForm.classList.toggle("open");
    mobileNav.classList.toggle("d-none");
  });

  searchClose.forEach((element) => {
    element.addEventListener("click", () => {
    searchForm.classList.toggle("open");
    mobileNav.classList.toggle("d-none");
  })
  });


  const signInMenu = document.querySelectorAll("a[href='#sign-in']");
  Array.from(signInMenu).forEach((element) => {
    element.addEventListener("click", function () {
      openModal("login");
    });
  });


  const userProfileMenu = document.querySelectorAll(".item.user-profile");
  Array.from(userProfileMenu).forEach((element) => {
    if (element.classList.contains("mobile")) return;

    const navText = element.querySelector(".collapsible");

    const user_avatar = document.querySelector(".user-avatar");
    if (user_avatar) {
      navText.innerHTML = user_avatar.outerHTML;
    } 
  });
});
