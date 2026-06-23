{{-- Scroll to Top Button (Floating & Confined to Card) --}}
<div class="w-100 d-flex justify-content-center scroll-top-wrapper" id="scrollTopBtn">
    <button type="button" onclick="document.querySelector('main').scrollTo({top: 0, behavior: 'smooth'})" 
            class="btn bg-sipsr-primary text-white btn-sm rounded-circle shadow d-flex align-items-center justify-content-center p-0" 
            title="Kembali ke atas" 
            style="width: 32px; height: 32px; pointer-events: auto; transition: all 0.3s ease;" 
            onmouseover="this.style.transform='translateY(-3px)';" 
            onmouseout="this.style.transform='translateY(0)';">
        <i class="bi bi-arrow-up fs-6"></i>
    </button>
</div>
