document.addEventListener('DOMContentLoaded', () => {  
    const hamburger = document.querySelector('.hamburger')
    const openIcon = document.querySelector('.icon.open');
    const closeIcon = document.querySelector('.icon.close');
    const drawer = document.querySelector('.drawer')

    hamburger.addEventListener('click', () => {
        drawer.classList.toggle('open')
        openIcon.style.display = openIcon.style.display === 'none' ? 'block' : 'none';
        closeIcon.style.display = closeIcon.style.display === 'none' ? 'block' : 'none';
    })

    const parentNavs = document.querySelectorAll('.item.parent')

    parentNavs.forEach(parentNav => {
        const childNav = parentNav.querySelector('.children')
        const chevronOpen = parentNav.querySelector('.icon.open')
        const chevronClose = parentNav.querySelector('.icon.close')
        parentNav.addEventListener('click', () => {
            childNav.classList.toggle('open')
            chevronOpen.style.display = chevronOpen.style.display === 'none' ? 'block' : 'none';
            chevronClose.style.display = chevronClose.style.display === 'none' ? 'block' : 'none';
        })
    })

    const searchIcon = document.querySelector('.search-icon')
    const searchForm = document.querySelector('.mobile-search')
    const searchClose = document.querySelector('.search-close-btn')

    searchIcon.addEventListener('click', () => {
        searchForm.classList.toggle('open')
    })

    searchClose.addEventListener('click', () => {
        searchForm.classList.toggle('open')
    })

  });

