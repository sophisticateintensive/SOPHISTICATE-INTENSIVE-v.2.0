@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Upload Learning Resource
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Upload syllabus, practice exams, notes, and study files</p>
        </div>

        <a href="{{ route('admin.resources.index') }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--glass-bg)] backdrop-blur-sm text-[var(--text-primary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-[var(--bg-card)] hover:scale-105 transition-all duration-200 group">
            <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Resources
        </a>
    </div>
@endsection

@section('content')
    <style>
        .split-create-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 2.5rem;
            box-shadow: 0 25px 80px var(--shadow-color);
            overflow: hidden;
            transition: background 0.4s, border-color 0.4s, box-shadow 0.4s;
            display: grid;
            grid-template-columns: 1fr 2fr;
            min-height: 620px;
            position: relative;
        }

        .split-create-container::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 2.5rem;
            padding: 2px;
            background: linear-gradient(135deg, rgba(59,130,246,0.3), rgba(139,92,246,0.3));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            z-index: 0;
        }

        @media (max-width: 1024px) {
            .split-create-container {
                grid-template-columns: 1fr;
                border-radius: 2rem;
            }
            .split-create-container::before { display: none; }
        }

        .brand-preview-panel {
            background: linear-gradient(145deg, rgba(59, 130, 246, 0.06), rgba(37, 99, 235, 0.03));
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-right: 1px solid var(--border-color);
            transition: border-color 0.4s;
            position: relative;
            z-index: 1;
            justify-content: space-between;
        }

        @media (max-width: 1024px) {
            .brand-preview-panel {
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                padding: 2rem 1.5rem;
            }
        }

        .form-panel {
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 640px) {
            .form-panel { padding: 1.5rem; }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .glass-input, .glass-select, .glass-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.85rem;
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .glass-input:focus, .glass-select:focus, .glass-textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
            outline: none;
        }

        .dropzone-box {
            border: 2px dashed var(--border-color);
            border-radius: 1.5rem;
            padding: 2rem 1.5rem;
            text-align: center;
            background: var(--glass-bg);
            transition: all 0.3s;
            cursor: pointer;
        }
        .dropzone-box:hover {
            border-color: #3b82f6;
            background: rgba(59,130,246,0.04);
        }
    </style>

    <div class="split-create-container">
        <!-- ========== LEFT PANEL: RESOURCE LIVE CARD PREVIEW ========== -->
        <div class="brand-preview-panel">
            <div class="w-full">
                <!-- Glowing Upload Icon -->
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl shadow-xl mx-auto mb-4">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h3 class="font-extrabold text-xl text-[var(--text-primary)]">Material Dispatch</h3>
                <p class="text-xs text-[var(--text-secondary)] mt-1">Study file & repository manager</p>

                <!-- Live Resource Card Simulation -->
                <div class="mt-6 p-4 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] text-left shadow-sm space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-lg flex-shrink-0" id="previewFileIcon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-[var(--text-primary)] truncate" id="previewResourceTitle">
                                Resource Title...
                            </h4>
                            <p class="text-xs text-[var(--text-muted)] truncate" id="previewFileName">
                                No file attached
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-[var(--border-color)] text-[10px] text-[var(--text-secondary)]">
                        <span class="px-2 py-0.5 rounded-full font-bold uppercase" id="previewVisibilityBadge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            Students
                        </span>
                        <span id="previewFileSize">0.00 MB</span>
                    </div>
                </div>
            </div>

            <!-- Upload specs -->
            <div class="w-full mt-6 pt-4 border-t border-[var(--border-color)] text-left text-xs text-[var(--text-muted)] flex items-center gap-2">
                <i class="fas fa-shield-alt text-blue-500"></i>
                <span>Max 10MB &bull; Automatic mime-type detection.</span>
            </div>
        </div>

        <!-- ========== RIGHT PANEL: UPLOAD FORM ========== -->
        <div class="form-panel">
            <form action="{{ route('admin.resources.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Title -->
                <div class="form-group">
                    <label for="title" class="form-label">
                        Resource Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="glass-input font-semibold"
                        placeholder="e.g. Physics Formula Sheet & Practice Calculations"
                        oninput="document.getElementById('previewResourceTitle').textContent = this.value || 'Resource Title...'">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject / Course -->
                <div class="form-group">
                    <label for="subject_id" class="form-label">
                        Course / Subject (Optional)
                    </label>
                    <select name="subject_id" id="subject_id" class="glass-select">
                        <option value="">-- General / All Courses --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->code }} - {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="form-label">
                        Description (Optional)
                    </label>
                    <textarea name="description" id="description" rows="3"
                        class="glass-textarea leading-relaxed"
                        placeholder="Brief summary of lessons, topics, or instructions for students..."></textarea>
                </div>

                <!-- File Dropzone -->
                <div class="form-group">
                    <label class="form-label">
                        Attach File Document <span class="text-red-500">*</span>
                    </label>
                    <div class="dropzone-box" onclick="document.getElementById('file').click()">
                        <input type="file" name="file" id="file" class="hidden" required onchange="handleFileChange(this)">
                        <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center mx-auto mb-2 text-base">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <p class="text-xs font-bold text-[var(--text-primary)]" id="dropzoneLabel">
                            Click to select a file from your computer
                        </p>
                        <p class="text-[11px] text-[var(--text-muted)] mt-1">
                            PDF, Word (.docx), Excel (.xlsx), PowerPoint (.pptx), Zip, Images (Max: 10MB)
                        </p>
                    </div>
                    @error('file')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Student Visibility Switch -->
                <div class="p-4 bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-2xl flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-[var(--text-primary)] block">Available to Students</span>
                        <span class="text-[11px] text-[var(--text-secondary)]">Students can search and download this file from their student portal</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="for_students" value="1" {{ old('for_students', true) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300"
                            onchange="updateVisibilityPreview(this)">
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-[var(--border-color)]">
                    <a href="{{ route('admin.resources.index') }}"
                        class="px-5 py-2.5 bg-[var(--bg-card)] text-[var(--text-secondary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition flex items-center gap-2">
                        <i class="fas fa-cloud-upload-alt"></i>
                        Upload Resource
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function handleFileChange(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                document.getElementById('dropzoneLabel').textContent = `${file.name} (${(file.size / (1024 * 1024)).toFixed(2)} MB)`;
                document.getElementById('previewFileName').textContent = file.name;
                document.getElementById('previewFileSize').textContent = `${(file.size / (1024 * 1024)).toFixed(2)} MB`;

                const ext = file.name.split('.').pop().toLowerCase();
                const iconContainer = document.getElementById('previewFileIcon');
                if (ext === 'pdf') {
                    iconContainer.innerHTML = '<i class="fas fa-file-pdf text-red-500"></i>';
                } else if (['doc', 'docx'].includes(ext)) {
                    iconContainer.innerHTML = '<i class="fas fa-file-word text-blue-500"></i>';
                } else if (['xls', 'xlsx'].includes(ext)) {
                    iconContainer.innerHTML = '<i class="fas fa-file-excel text-emerald-500"></i>';
                } else if (['ppt', 'pptx'].includes(ext)) {
                    iconContainer.innerHTML = '<i class="fas fa-file-powerpoint text-amber-500"></i>';
                } else if (['zip', 'rar'].includes(ext)) {
                    iconContainer.innerHTML = '<i class="fas fa-file-archive text-purple-500"></i>';
                } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                    iconContainer.innerHTML = '<i class="fas fa-file-image text-cyan-500"></i>';
                } else {
                    iconContainer.innerHTML = '<i class="fas fa-file text-gray-500"></i>';
                }
            }
        }

        function updateVisibilityPreview(checkbox) {
            const badge = document.getElementById('previewVisibilityBadge');
            if (checkbox.checked) {
                badge.textContent = 'Students';
                badge.style.background = 'rgba(16, 185, 129, 0.1)';
                badge.style.color = '#10b981';
            } else {
                badge.textContent = 'Admin Only';
                badge.style.background = 'rgba(139, 92, 246, 0.1)';
                badge.style.color = '#8b5cf6';
            }
        }
    </script>
    @endpush
@endsection
