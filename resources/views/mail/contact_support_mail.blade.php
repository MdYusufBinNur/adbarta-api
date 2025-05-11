<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3>Contact Support - Message Details</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-bold">Name:</label>
                <p class="form-control-plaintext">{{ $name ?? '' }}</p>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Email:</label>
                <p class="form-control-plaintext">{{ $email ?? ''}}</p>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Mobile:</label>
                <p class="form-control-plaintext">{{ $mobile ?? ''}}</p>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Subject:</label>
                <p class="form-control-plaintext">{{ $subjects ?? ''}}</p>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Message:</label>
                <p class="form-control-plaintext">{{ $text ?? '' }}</p>
            </div>
        </div>
    </div>
</div>
