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
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    document.addEventListener('DOMContentLoaded', onScroll);
})();


