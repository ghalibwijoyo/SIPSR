{{-- Toast Container --}}
<div
    class="toast-container position-fixed top-0 end-0 p-3"
    style="z-index: 1090"
    id="toast-container"
>
    @if (session('success'))
        <div
            class="toast show align-items-center text-white bg-success border-0"
            role="alert"
            aria-live="assertive"
            aria-atomic="true"
            data-bs-delay="3000"
            id="toast-success"
        >
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle-fill me-1"></i
                    >{{ session('success') }}
                </div>
                <button
                    type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"
                    aria-label="Close"
                ></button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div
            class="toast show align-items-center text-white bg-danger border-0"
            role="alert"
            aria-live="assertive"
            aria-atomic="true"
            data-bs-delay="3000"
            id="toast-error"
        >
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i
                    >{{ session('error') }}
                </div>
                <button
                    type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"
                    aria-label="Close"
                ></button>
            </div>
        </div>
    @endif
</div>

@push ('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document
                .querySelectorAll("#toast-container .toast.show")
                .forEach(function (el) {
                    const toast = bootstrap.Toast.getOrCreateInstance(el, {
                        delay: 3000,
                    });
                    toast.show();
                });
        });
    </script>
@endpush
