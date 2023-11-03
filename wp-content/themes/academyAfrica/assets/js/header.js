document.addEventListener('DOMContentLoaded', () => {  
    const hamburger = document.querySelector('.hamburger')
    const openIcon = document.querySelector('.icon.open');
    const closeIcon = document.querySelector('.icon.close');
    const drawer = document.querySelector('.drawer')

    console.log(closeIcon)
    console.log(openIcon)

    hamburger.addEventListener('click', () => {
        console.log('clicked hamburger')
        drawer.classList.toggle('open')
        openIcon.style.display = openIcon.style.display === 'none' ? 'block' : 'none';
        closeIcon.style.display = closeIcon.style.display === 'none' ? 'block' : 'none';
    })
  });

