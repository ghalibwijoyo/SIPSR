{{-- Scroll to Top Button (Floating & Confined to Card) --}}
<div class="w-100 d-flex justify-content-end align-items-center pe-4 scroll-top-wrapper" id="scrollTopBtn">
    <button type="button" onclick="document.querySelector('main').scrollTo({top: 0, behavior: 'smooth'})" 
            class="btn bg-sipsr-primary text-white btn-sm rounded-circle shadow d-flex align-items-center justify-content-center p-0" 
            title="Kembali ke atas" 
            style="width: 32px; height: 32px; pointer-events: auto; transition: all 0.3s ease;" 
            onmouseover="this.style.transform='translateY(-3px)';" 
            onmouseout="this.style.transform='translateY(0)';">
        <i class="bi bi-arrow-up fs-6"></i>
    </button>
</div>

@push('scripts')
<style>
.scroll-top-wrapper {
    position: sticky; 
    bottom: 30px; /* Jarak melayang dari bawah viewport */
    z-index: 1050; 
    pointer-events: none;
    margin-top: -80px;
    height: 80px;
    margin-bottom: 0;
    opacity: 0;
    visibility: hidden;
    transform: translateY(30px);
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55); /* Efek memantul khas ArsiPSR */
}
.scroll-top-wrapper.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
</style>
<script>
// Scroll to top visibility
document.addEventListener('DOMContentLoaded', function() {
    const filterCard = document.getElementById('filter-card');
    const scrollTopBtn = document.getElementById('scrollTopBtn');

    if (filterCard && scrollTopBtn) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) {
                    scrollTopBtn.classList.add('show');
                } else {
                    scrollTopBtn.classList.remove('show');
                }
            });
        }, { root: document.querySelector('main'), threshold: 0 });
        
        observer.observe(filterCard);
    }
});
</script>
@endpush
