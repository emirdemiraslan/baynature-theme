(function(){
    function onScroll(){
        var scrolled = window.pageYOffset || document.documentElement.scrollTop || 0;
        var header = document.querySelector('.bn-header-bar');
        if(!header) return;
        if(scrolled > 100){
            header.classList.add('is-stuck');
            document.body.classList.add('header-stuck');
        } else {
            header.classList.remove('is-stuck');
            document.body.classList.remove('header-stuck');
        }

        // Handle scroll indicator visibility
        var hero = document.querySelector('.issue-hero');
        if(hero) {
            var heroHeight = hero.offsetHeight;
            var heroBottom = hero.getBoundingClientRect().bottom + window.pageYOffset;
            if(scrolled > heroBottom - heroHeight) {
                hero.classList.add('scrolled-past');
            } else {
                hero.classList.remove('scrolled-past');
            }
        }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    document.addEventListener('DOMContentLoaded', onScroll);

    // Handle scroll indicator click
    function initScrollIndicator() {
        var scrollIndicator = document.querySelector('.issue-hero__scroll-indicator');
        if(!scrollIndicator) return;

        scrollIndicator.addEventListener('click', function(e) {
            e.preventDefault();
            var hero = document.querySelector('.issue-hero');
            if(!hero) return;

            // Find the next sibling element after the hero
            var nextElement = hero.nextElementSibling;
            
            // If no direct sibling, try to find next content in site-content
            if(!nextElement) {
                var siteContent = hero.closest('.site-content');
                if(siteContent) {
                    var allChildren = Array.from(siteContent.children);
                    var heroIndex = allChildren.indexOf(hero);
                    if(heroIndex >= 0 && heroIndex < allChildren.length - 1) {
                        nextElement = allChildren[heroIndex + 1];
                    }
                }
            }

            // Fallback: scroll by viewport height
            if(!nextElement) {
                window.scrollTo({
                    top: window.innerHeight,
                    behavior: 'smooth'
                });
            } else {
                nextElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });

        // Keyboard accessibility
        scrollIndicator.addEventListener('keydown', function(e) {
            if(e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                scrollIndicator.click();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initScrollIndicator);
})();


