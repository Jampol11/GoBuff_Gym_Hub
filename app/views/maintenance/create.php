<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Report Maintenance Issue</h2></div>
        <a href="<?= base_url('/maintenance') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white"><h6 class="mb-0"><i class="bi bi-wrench me-2"></i>Maintenance Report</h6></div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/maintenance') ?>" method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Equipment <span class="text-danger">*</span></label>
                                <select class="form-select" name="equipment_id" required>
                                    <option value="">Select equipment</option>
                                    <?php foreach ($equipment as $eq): ?>
                                        <option value="<?= $eq['id'] ?>"><?= e($eq['name']) ?> (<?= e($eq['condition_status']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Issue Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="issue_type" required>
                                    <option value="">Select issue type</option>
                                    <option value="Mechanical Failure">Mechanical Failure</option>
                                    <option value="Electrical Issue">Electrical Issue</option>
                                    <option value="Wear and Tear">Wear and Tear</option>
                                    <option value="Safety Hazard">Safety Hazard</option>
                                    <option value="Routine Maintenance">Routine Maintenance</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Priority</label>
                                <select class="form-select" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" rows="4" required
                                          placeholder="Describe the issue in detail..."></textarea>
                            </div>

                            <!-- Photo Evidence -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-camera me-1 text-danger"></i>Photo Evidence
                                    <span class="text-muted fw-normal small ms-1">(optional)</span>
                                </label>

                                <!-- Toggle buttons -->
                                <div class="btn-group w-100 mb-3" role="group">
                                    <input type="radio" class="btn-check" name="photo_source" id="srcUpload" value="upload" checked>
                                    <label class="btn btn-outline-secondary" for="srcUpload">
                                        <i class="bi bi-upload me-1"></i>Upload File
                                    </label>
                                    <input type="radio" class="btn-check" name="photo_source" id="srcCamera" value="camera">
                                    <label class="btn btn-outline-secondary" for="srcCamera">
                                        <i class="bi bi-camera-fill me-1"></i>Take Photo
                                    </label>
                                </div>

                                <!-- File upload panel -->
                                <div id="panelUpload">
                                    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 text-center position-relative"
                                         style="cursor:pointer; border-color:#dee2e6 !important; transition: border-color .2s;">
                                        <i class="bi bi-image fs-2 text-muted d-block mb-2"></i>
                                        <p class="mb-1 text-muted small">Drag &amp; drop an image here, or <span class="text-danger fw-semibold">browse</span></p>
                                        <p class="text-muted" style="font-size:.75rem;">JPG, PNG, GIF, WEBP — max 5 MB</p>
                                        <input type="file" id="photoFile" name="photo_evidence" accept="image/*"
                                               class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor:pointer;">
                                    </div>
                                    <div id="uploadPreview" class="mt-2 d-none text-center">
                                        <img id="uploadPreviewImg" src="" alt="Preview"
                                             class="img-fluid rounded shadow-sm" style="max-height:220px; object-fit:contain;">
                                        <div class="mt-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="clearUpload">
                                                <i class="bi bi-x-circle me-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Camera capture panel -->
                                <div id="panelCamera" class="d-none">
                                    <div class="rounded-3 overflow-hidden bg-dark position-relative" style="max-height:300px;">
                                        <video id="cameraStream" autoplay playsinline muted
                                               class="w-100 d-block" style="max-height:300px; object-fit:cover;"></video>
                                        <div class="position-absolute bottom-0 start-0 end-0 p-2 d-flex justify-content-center gap-2 bg-dark bg-opacity-50">
                                            <button type="button" class="btn btn-danger btn-sm px-4" id="captureBtn">
                                                <i class="bi bi-camera-fill me-1"></i>Capture
                                            </button>
                                            <button type="button" class="btn btn-outline-light btn-sm" id="switchCameraBtn" title="Switch camera">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <canvas id="captureCanvas" class="d-none"></canvas>
                                    <div id="cameraPreview" class="mt-2 d-none text-center">
                                        <img id="cameraPreviewImg" src="" alt="Captured photo"
                                             class="img-fluid rounded shadow-sm" style="max-height:220px; object-fit:contain;">
                                        <div class="mt-1">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="retakeBtn">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Retake
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Hidden file input that receives the captured image blob (no name — JS copies to photoFile on submit) -->
                                    <input type="file" id="cameraFile" class="d-none" accept="image/*">
                                    <div id="cameraError" class="alert alert-warning mt-2 d-none small py-2">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Camera access denied or unavailable. Please use the file upload option instead.
                                    </div>
                                </div>
                            </div>
                            <!-- /Photo Evidence -->

                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/maintenance') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-danger"><i class="bi bi-send me-1"></i>Submit Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ── Source toggle ─────────────────────────────────────────── */
    const radios      = document.querySelectorAll('input[name="photo_source"]');
    const panelUpload = document.getElementById('panelUpload');
    const panelCamera = document.getElementById('panelCamera');

    radios.forEach(r => r.addEventListener('change', () => {
        const isCamera = r.value === 'camera' && r.checked;
        panelUpload.classList.toggle('d-none', isCamera);
        panelCamera.classList.toggle('d-none', !isCamera);
        if (isCamera) startCamera(); else stopCamera();
    }));

    /* ── File upload / drag-drop ───────────────────────────────── */
    const dropZone       = document.getElementById('dropZone');
    const photoFile      = document.getElementById('photoFile');
    const uploadPreview  = document.getElementById('uploadPreview');
    const uploadPreviewImg = document.getElementById('uploadPreviewImg');
    const clearUpload    = document.getElementById('clearUpload');

    function previewFile(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
            uploadPreviewImg.src = e.target.result;
            uploadPreview.classList.remove('d-none');
            dropZone.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    }

    photoFile.addEventListener('change', () => previewFile(photoFile.files[0]));

    clearUpload.addEventListener('click', () => {
        photoFile.value = '';
        uploadPreviewImg.src = '';
        uploadPreview.classList.add('d-none');
        dropZone.classList.remove('d-none');
    });

    ['dragover', 'dragenter'].forEach(evt =>
        dropZone.addEventListener(evt, e => { e.preventDefault(); dropZone.style.borderColor = '#dc3545'; })
    );
    ['dragleave', 'drop'].forEach(evt =>
        dropZone.addEventListener(evt, e => { e.preventDefault(); dropZone.style.borderColor = ''; })
    );
    dropZone.addEventListener('drop', e => {
        const file = e.dataTransfer.files[0];
        if (file) {
            // Transfer to the real input via DataTransfer
            const dt = new DataTransfer();
            dt.items.add(file);
            photoFile.files = dt.files;
            previewFile(file);
        }
    });

    /* ── Camera capture ────────────────────────────────────────── */
    const video         = document.getElementById('cameraStream');
    const canvas        = document.getElementById('captureCanvas');
    const captureBtn    = document.getElementById('captureBtn');
    const switchBtn     = document.getElementById('switchCameraBtn');
    const cameraPreview = document.getElementById('cameraPreview');
    const cameraPreviewImg = document.getElementById('cameraPreviewImg');
    const retakeBtn     = document.getElementById('retakeBtn');
    const cameraFile    = document.getElementById('cameraFile');
    const cameraError   = document.getElementById('cameraError');

    let stream = null;
    let facingMode = 'environment'; // rear camera first

    async function startCamera() {
        cameraError.classList.add('d-none');
        cameraPreview.classList.add('d-none');
        video.classList.remove('d-none');
        captureBtn.disabled = false;

        try {
            if (stream) stopCamera();
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: facingMode } },
                audio: false
            });
            video.srcObject = stream;
        } catch (err) {
            cameraError.classList.remove('d-none');
            video.classList.add('d-none');
            captureBtn.disabled = true;
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(t => t.stop());
            stream = null;
        }
        video.srcObject = null;
    }

    captureBtn.addEventListener('click', () => {
        canvas.width  = video.videoWidth  || 1280;
        canvas.height = video.videoHeight || 720;
        canvas.getContext('2d').drawImage(video, 0, 0);

        canvas.toBlob(blob => {
            const file = new File([blob], 'capture.jpg', { type: 'image/jpeg' });
            const dt   = new DataTransfer();
            dt.items.add(file);
            cameraFile.files = dt.files;

            cameraPreviewImg.src = canvas.toDataURL('image/jpeg');
            cameraPreview.classList.remove('d-none');
            video.classList.add('d-none');
            captureBtn.disabled = true;
            stopCamera();
        }, 'image/jpeg', 0.92);
    });

    retakeBtn.addEventListener('click', () => {
        cameraFile.value = '';
        cameraPreviewImg.src = '';
        cameraPreview.classList.add('d-none');
        captureBtn.disabled = false;
        startCamera();
    });

    switchBtn.addEventListener('click', () => {
        facingMode = facingMode === 'environment' ? 'user' : 'environment';
        startCamera();
    });

    // Stop camera if user navigates away
    window.addEventListener('beforeunload', stopCamera);

    /* ── Ensure only the active input is submitted ─────────────── */
    document.querySelector('form').addEventListener('submit', () => {
        const isCamera = document.getElementById('srcCamera').checked;
        if (isCamera && cameraFile.files.length > 0) {
            // Copy the captured blob into the named photoFile input
            const dt = new DataTransfer();
            dt.items.add(cameraFile.files[0]);
            photoFile.files = dt.files;
        }
        // cameraFile has no name so it is never submitted — nothing else needed
    });
})();
</script>
