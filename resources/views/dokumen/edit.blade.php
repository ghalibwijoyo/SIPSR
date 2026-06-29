@extends ('layouts.app')

@section ('title', 'Edit Dokumen — ArsiPSR')

@section ('content')
    {{-- Page Header --}}
    <div
        class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2"
    >
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Edit Metadata Dokumen</h1>
            <p class="text-muted mb-0">
                <a
                    href="{{ route('dokumen.index') }}"
                    class="text-decoration-none"
                    >Dokumen</a
                >
                <i class="bi bi-chevron-right mx-1 small"></i>
                <a
                    href="{{ route('dokumen.show', $dokumen) }}"
                    class="text-decoration-none"
                    >{{ Str::limit($dokumen->nama_dokumen, 30) }}</a
                >
                <i class="bi bi-chevron-right mx-1 small"></i>
                Edit
            </p>
        </div>
        <a
            href="{{ route('dokumen.show', $dokumen) }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div
                    class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
                >
                    <h5 class="card-title mb-0 fw-semibold">
                        <i
                            class="bi bi-pencil-square me-2 text-sipsr-primary"
                        ></i
                        >Edit Metadata
                    </h5>
                    <span class="badge bg-secondary">
                        <i class="bi bi-file-earmark me-1"></i
                        >{{ $dokumen->file_name }}
                    </span>
                </div>
                <div class="card-body p-4">
                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info border-0 mb-4" role="alert">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Hanya metadata yang dapat diedit. File dokumen tidak
                        dapat diganti.
                    </div>

                    <form
                        method="POST"
                        action="{{ route('dokumen.update', $dokumen) }}"
                        id="edit-form"
                    >
                        @csrf
                        @method ('PUT')

                        <div class="row g-3">
                            {{-- Nomor Dokumen --}}
                            <div class="col-md-6">
                                <label
                                    for="nomor_dokumen"
                                    class="form-label fw-semibold"
                                >
                                    Nomor Dokumen
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('nomor_dokumen') is-invalid @enderror"
                                    id="nomor_dokumen"
                                    name="nomor_dokumen"
                                    value="{{ old('nomor_dokumen', $dokumen->nomor_dokumen) }}"
                                    required
                                />
                                @error ('nomor_dokumen')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Nama Dokumen --}}
                            <div class="col-md-6">
                                <label
                                    for="nama_dokumen"
                                    class="form-label fw-semibold"
                                >
                                    Nama Dokumen
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('nama_dokumen') is-invalid @enderror"
                                    id="nama_dokumen"
                                    name="nama_dokumen"
                                    value="{{ old('nama_dokumen', $dokumen->nama_dokumen) }}"
                                    required
                                />
                                @error ('nama_dokumen')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Bank --}}
                            <div class="col-md-6">
                                <label
                                    for="bank_id"
                                    class="form-label fw-semibold"
                                >
                                    Nama Bank
                                </label>
                                <select
                                    class="form-select @error('bank_id') is-invalid @enderror"
                                    id="bank_id"
                                    name="bank_id"
                                >
                                    <option value="">— Opsional —</option>
                                    @foreach ($banks as $bank)
                                        <option
                                            value="{{ $bank->id }}"
                                            {{ old('bank_id', $dokumen->bank_id) == $bank->id ? 'selected' : '' }}
                                        >
                                            {{ $bank->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error ('bank_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Kategori --}}
                            <div class="col-md-6">
                                <label
                                    for="category_id"
                                    class="form-label fw-semibold"
                                >
                                    Kategori <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select @error('category_id') is-invalid @enderror"
                                    id="category_id"
                                    name="category_id"
                                    required
                                >
                                    <option value="">— Pilih Kategori —</option>
                                    @foreach ($categories as $cat)
                                        <option
                                            value="{{ $cat->id }}"
                                            {{ old('category_id', $dokumen->category_id) == $cat->id ? 'selected' : '' }}
                                        >
                                            {{ $cat->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error ('category_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Tanggal Dokumen --}}
                            <div class="col-md-6">
                                <label
                                    for="tanggal_dokumen"
                                    class="form-label fw-semibold"
                                >
                                    Tanggal Dokumen
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    class="form-control @error('tanggal_dokumen') is-invalid @enderror"
                                    id="tanggal_dokumen"
                                    name="tanggal_dokumen"
                                    value="{{ old('tanggal_dokumen', $dokumen->tanggal_dokumen?->format('Y-m-d')) }}"
                                    required
                                />
                                @error ('tanggal_dokumen')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="col-12">
                                <label
                                    for="deskripsi"
                                    class="form-label fw-semibold"
                                    >Deskripsi</label
                                >
                                <textarea
                                    class="form-control @error('deskripsi') is-invalid @enderror"
                                    id="deskripsi"
                                    name="deskripsi"
                                    rows="3"
                                    placeholder="Deskripsi dokumen (opsional)"
                                    >{{ old('deskripsi', $dokumen->deskripsi) }}</textarea
                                >
                                @error ('deskripsi')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div
                            class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top"
                        >
                            <a
                                href="{{ route('dokumen.show', $dokumen) }}"
                                class="btn btn-secondary"
                            >
                                <i class="bi bi-x-lg me-1"></i>Batal
                            </a>
                            <button
                                type="submit"
                                class="btn btn-success px-4"
                                id="btn-save-edit"
                            >
                                <i class="bi bi-check-lg me-1"></i>Simpan
                                Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
