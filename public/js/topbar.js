let lastScrollTop = 0;
const topbar = document.getElementById("topbar");
const navbar = document.getElementById("navbar");

function adjustNavbarPosition() {
    const topbarHeight = topbar ? topbar.offsetHeight : 0;
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

    if (window.innerWidth < 768) {
        if (topbar) topbar.style.top = `-${topbarHeight}px`;
        navbar.style.top = "0";
        lastScrollTop = currentScroll;
        return;
    }

    if (currentScroll > lastScrollTop && currentScroll > topbarHeight) {
        topbar.style.top = `-${topbarHeight}px`;
        navbar.style.top = "0";
    } else {
        topbar.style.top = "0";
        navbar.style.top = `${topbarHeight}px`;
    }

    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
}

window.addEventListener("scroll", adjustNavbarPosition);

window.addEventListener("DOMContentLoaded", () => {
    const topbarHeight = topbar ? topbar.offsetHeight : 0;
    if (window.innerWidth < 768) {
        navbar.style.top = "0";
    } else {
        navbar.style.top = `${topbarHeight}px`;
    }
});