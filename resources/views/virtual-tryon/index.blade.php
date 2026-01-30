@extends('layouts.app')

@section('content')
<main class="pt-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold">Virtual Try-On</h1>
            <p class="lead text-muted">Upload your photo and a garment image to see how it looks on you!</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <form id="tryonForm" enctype="multipart/form-data">
                            @csrf
                            @if(isset($product))
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                            @endif
                            
                            <div class="row g-4 mb-4">
                                <!-- Model Image Upload -->
                                <div class="col-md-6">
                                    <div class="upload-area border-3 border-dashed rounded-3 p-4 text-center" id="modelUploadArea">
                                        <input type="file" name="model_image" id="modelImage" accept="image/*" class="d-none" required>
                                        <div class="upload-placeholder">
                                            <svg width="80" height="80" fill="currentColor" class="text-primary mb-3">
                                                <use href="#icon_user" />
                                            </svg>
                                            <h5>Upload Your Photo</h5>
                                            <p class="text-muted small mb-3">Click or drag & drop</p>
                                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('modelImage').click()">
                                                Choose File
                                            </button>
                                        </div>
                                        <div class="upload-preview d-none">
                                            <img id="modelPreview" class="img-fluid rounded" style="max-height: 300px;">
                                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImage('model')">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>Tips:</strong><br>
                                            ✓ Stand straight, arms at sides<br>
                                            ✓ Simple background<br>
                                            ✓ Good lighting<br>
                                            ✓ Full body visible
                                        </small>
                                    </div>
                                </div>

                                <!-- Garment Image Upload -->
                                <!-- Garment Image Upload -->
                                <div class="col-md-6">
                                    @if(isset($product))
                                        <div class="upload-area border-3 border-dashed rounded-3 p-4 text-center video-container" style="background-color: #e9ecef;">
                                            <div class="upload-preview">
                                                <img src="{{ asset('uploads/products/' . $product->image) }}" class="img-fluid rounded" style="max-height: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                                <h5 class="mt-3 text-primary">{{ $product->name }}</h5>
                                                <p class="text-muted small">Selected for Try-On</p>
                                                <a href="{{ route('virtual-tryon.index') }}" class="btn btn-sm btn-outline-danger mt-2">
                                                    Change Garment
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <div class="upload-area border-3 border-dashed rounded-3 p-4 text-center" id="garmentUploadArea">
                                            <input type="file" name="garment_image" id="garmentImage" accept="image/*" class="d-none" required>
                                            <div class="upload-placeholder">
                                                <svg width="80" height="80" fill="currentColor" class="text-success mb-3">
                                                    <use href="#icon_hanger" />
                                                </svg>
                                                <h5>Upload Garment Photo</h5>
                                                <p class="text-muted small mb-3">Click or drag & drop</p>
                                                <button type="button" class="btn btn-outline-success" onclick="document.getElementById('garmentImage').click()">
                                                    Choose File
                                                </button>
                                            </div>
                                            <div class="upload-preview d-none">
                                                <img id="garmentPreview" class="img-fluid rounded" style="max-height: 300px;">
                                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImage('garment')">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <strong>Tips:</strong><br>
                                                ✓ White or transparent background<br>
                                                ✓ Flat, no wrinkles<br>
                                                ✓ High quality image<br>
                                                ✓ Clear details
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Clothing Type Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Clothing Type</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="clothing_type" id="upper" value="upper" checked>
                                    <label class="btn btn-outline-primary" for="upper">Upper Body (Shirt, Jacket)</label>

                                    <input type="radio" class="btn-check" name="clothing_type" id="lower" value="lower">
                                    <label class="btn btn-outline-primary" for="lower">Lower Body (Pants, Skirt)</label>

                                    <input type="radio" class="btn-check" name="clothing_type" id="full" value="full">
                                    <label class="btn btn-outline-primary" for="full">Full Body (Dress)</label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm me-2 d-none" id="spinner"></span>
                                    Generate Virtual Try-On
                                </button>
                            </div>
                        </form>

                        <!-- Result Section -->
                        <div id="resultSection" class="mt-5 d-none">
                            <hr class="my-5">
                            <h3 class="text-center mb-4">Result</h3>
                            
                            <!-- Processing State -->
                            <div id="processingState" class="text-center py-5">
                                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
                                <h5>Processing your virtual try-on...</h5>
                                <p class="text-muted">This usually takes 10-30 seconds</p>
                                <div class="progress mx-auto" style="max-width: 400px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                                </div>
                            </div>

                            <!-- Result Display -->
                            <div id="resultDisplay" class="d-none">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <h6>Original</h6>
                                        <img id="originalImage" class="img-fluid rounded shadow">
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h6>Garment</h6>
                                        <img id="garmentResultImage" class="img-fluid rounded shadow">
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h6>Result</h6>
                                        <img id="resultImage" class="img-fluid rounded shadow">
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <a id="downloadBtn" class="btn btn-success me-2" download>
                                        <i class="fa fa-download"></i> Download Result
                                    </a>
                                    <button class="btn btn-outline-primary" onclick="location.reload()">
                                        Try Another
                                    </button>
                                </div>
                            </div>

                            <!-- Error State -->
                            <div id="errorState" class="alert alert-danger d-none">
                                <h5>Error</h5>
                                <p id="errorMessage"></p>
                                <button class="btn btn-outline-danger" onclick="location.reload()">Try Again</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.upload-area {
    min-height: 350px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s;
}

.upload-area:hover {
    background: #e9ecef;
    border-color: #0d6efd !important;
}

.upload-area.dragover {
    background: #e7f3ff;
    border-color: #0d6efd !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Virtual Try-On script loaded');
    
    // Image Preview Handlers
    const modelImageInput = document.getElementById('modelImage');
    console.log('Model image input found:', modelImageInput);
    
    if (modelImageInput) {
        modelImageInput.addEventListener('change', function(e) {
            console.log('Model image changed', e.target.files);
            previewImage(e.target, 'model');
        });
    }

    @if(!isset($product))
    const garmentImageInput = document.getElementById('garmentImage');
    console.log('Garment image input found:', garmentImageInput);
    
    if (garmentImageInput) {
        garmentImageInput.addEventListener('change', function(e) {
            console.log('Garment image changed', e.target.files);
            previewImage(e.target, 'garment');
        });
    }
    @endif

    function previewImage(input, type) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            console.log('Previewing file:', file.name, file.type, file.size);
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                alert('Vui lòng chọn file ảnh định dạng JPG, JPEG hoặc PNG!');
                input.value = '';
                return;
            }
            
            // Validate file size (max 5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if (file.size > maxSize) {
                alert('Kích thước file không được vượt quá 5MB!');
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(type + 'Preview');
                preview.src = e.target.result;
                document.querySelector(`#${type}UploadArea .upload-placeholder`).classList.add('d-none');
                document.querySelector(`#${type}UploadArea .upload-preview`).classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }
    }

    window.clearImage = function(type) {
        document.getElementById(type + 'Image').value = '';
        document.querySelector(`#${type}UploadArea .upload-placeholder`).classList.remove('d-none');
        document.querySelector(`#${type}UploadArea .upload-preview`).classList.add('d-none');
    }

    // Drag & Drop
    const uploadAreas = ['modelUploadArea'];
    @if(!isset($product))
        uploadAreas.push('garmentUploadArea');
    @endif

    uploadAreas.forEach(id => {
        const area = document.getElementById(id);
        console.log('Setting up upload area:', id, area);
        
        if(!area) return;
        
        const input = area.querySelector('input[type="file"]');
        const button = area.querySelector('button');
        
        console.log('Upload area elements:', {id, input, button});
        
        // Click on area (but not on button)
        area.addEventListener('click', (e) => {
            console.log('Upload area clicked', e.target);
            
            // Don't trigger if clicking on button or its children
            if (button && (e.target === button || button.contains(e.target))) {
                console.log('Clicked on button, skipping area handler');
                return;
            }
            
            console.log('Triggering file input click');
            input.click();
        });
        
        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.classList.add('dragover');
        });
        
        area.addEventListener('dragleave', () => area.classList.remove('dragover'));
        
        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');
            console.log('Files dropped:', e.dataTransfer.files);
            input.files = e.dataTransfer.files;
            input.dispatchEvent(new Event('change'));
        });
    });
});

// Form Submit
document.getElementById('tryonForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');
    
    // Disable submit button
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
    
    try {
        // Upload images
        const response = await fetch('{{ route("virtual-tryon.upload") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show result section
            document.getElementById('resultSection').classList.remove('d-none');
            document.getElementById('processingState').classList.remove('d-none');
            
            // Store preview images
            document.getElementById('originalImage').src = document.getElementById('modelPreview').src;
            // Store preview images
            document.getElementById('originalImage').src = document.getElementById('modelPreview').src;
            @if(isset($product))
                document.getElementById('garmentResultImage').src = "{{ asset('uploads/products/' . $product->image) }}";
            @else
                document.getElementById('garmentResultImage').src = document.getElementById('garmentPreview').src;
            @endif
            
            // Poll for result
            pollResult(data.tryon_id);
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Failed to upload images: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        spinner.classList.add('d-none');
    }
});

// Poll for result
function pollResult(tryonId) {
    const interval = setInterval(async () => {
        try {
            const response = await fetch(`/virtual-tryon/result/${tryonId}`);
            const data = await response.json();
            
            if (data.status === 'completed') {
                clearInterval(interval);
                showResult(data.result_image);
            } else if (data.status === 'failed') {
                clearInterval(interval);
                showError(data.message);
            }
        } catch (error) {
            clearInterval(interval);
            showError('Failed to get result: ' + error.message);
        }
    }, 3000); // Poll every 3 seconds
}

function showResult(imageUrl) {
    document.getElementById('processingState').classList.add('d-none');
    document.getElementById('resultDisplay').classList.remove('d-none');
    document.getElementById('resultImage').src = imageUrl;
    document.getElementById('downloadBtn').href = imageUrl;
}


function showError(message) {
    document.getElementById('processingState').classList.add('d-none');
    document.getElementById('errorState').classList.remove('d-none');
    document.getElementById('errorMessage').textContent = message;
}
</script>

@endsection
