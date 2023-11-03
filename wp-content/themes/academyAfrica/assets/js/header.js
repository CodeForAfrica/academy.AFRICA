document.addEventListener('DOMContentLoaded', () => {  
    const hamburger = document.querySelector('.hamburger')
    const openIcon = document.querySelector('.icon.open');
    const closeIcon = document.querySelector('.icon.close');
    const drawer = document.querySelector('.drawer')

    hamburger.addEventListener('click', () => {
        console.log('clicked hamburger')
        drawer.classList.toggle('open')
        openIcon.style.display = openIcon.style.display === 'none' ? 'block' : 'none';
        closeIcon.style.display = closeIcon.style.display === 'none' ? 'block' : 'none';
    })

    const parentNavs = document.querySelectorAll('.item.parent')

    parentNavs.forEach(parentNav => {
        const childNav = parentNav.querySelector('.children')
        parentNav.addEventListener('click', () => {
            childNav.classList.toggle('open')
        })
    })
  });

