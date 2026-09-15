@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Edit Learning Resource
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Update file details, metadata, and visibility</p>
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

        @media (max-width: 1024px) {
            .split-create-container {
                grid-template-columns: 1fr;
                border-radius: 2rem;
            }
        }

        .brand-preview-panel {
            background: linear-gradient(145deg, rgba(59, 130, 246, 0.06), rgba(37, 99, 235, 0.03));
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-right: 1px solid var(--border-color);
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
        <!-- ========== LEFT PANEL: RESOURCE PREVIEW ========== -->
        <div class="brand-preview-panel">
            <div class="w-full">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl shadow-xl mx-auto mb-4">
                    <i class="fas fa-edit"></i>
                </div>
                <h3 class="font-extrabold text-xl text-[var(--text-primary)]">Edit Material</h3>
                <p class="text-xs text-[var(--text-secondary)] mt-1">{{ $resource->file_name }}</p>

                <div class="mt-6 p-4 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] text-left shadow-sm space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-lg flex-shrink-0" id="previewFileIcon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-[var(--text-primary)] truncate" id="previewResourceTitle">
                                {{ $resource->title }}
                            </h4>
                            <p class="text-xs text-[var(--text-muted)] truncate" id="previewFileName">
                                {{ $resource->file_name }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-[var(--border-color)] text-[10px] text-[var(--text-secondary)]">
                        <span class="px-2 py-0.5 rounded-full font-bold uppercase" id="previewVisibilityBadge"
                              style="background: {{ $resource->for_students ? 'rgba(16, 185, 129, 0.1)' : 'rgba(139, 92, 246, 0.1)' }}; color: {{ $resource->for_students ? '#10b981' : '#8b5cf6' }};">
                            {{ $resource->for_students ? 'Students' : 'Admin Only' }}
                        </span>
                        <span id="previewFileSize">{{ number_format($resource->file_size / (1024 * 1024), 2) }} MB</span>
                    </div>
                </div>
            </div>

            <div class="w-full mt-6 pt-4 border-t border-[var(--border-color)] text-left text-xs text-[var(--text-muted)] flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                <span>Uploaded {{ $resource->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        <!-- ========== RIGHT PANEL: EDIT FORM ========== -->
        <div class="form-panel">
            <form action="{{ route('admin.resources.update', $resource) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div class="form-group">
                    <label for="title" class="form-label">
                        Resource Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title', $resource->title) }}" required
                        class="glass-input font-semibold"
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
                            <option value="{{ $subject->id }}" {{ old('subject_id', $resource->subject_id) == $subject->id ? 'selected' : '' }}>
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
                        class="glass-textarea leading-relaxed">{{ old('description', $resource->description) }}</textarea>
                </div>

                <!-- Replace File (Optional) -->
                <div class="form-group">
                    <label class="form-label">
                        Replace File (Optional)
                    </label>
                    <div class="dropzone-box" onclick="document.getElementById('file').click()">
                        <input type="file" name="file" id="file" class="hidden" onchange="handleFileChange(this)">
                        <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center mx-auto mb-2 text-base">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <p class="text-xs font-bold text-[var(--text-primary)]" id="dropzoneLabel">
                            Current file: {{ $resource->file_name }} (Click to replace)
                        </p>
                        <p class="text-[11px] text-[var(--text-muted)] mt-1">
                            Leave empty to keep current file
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
                        <span class="text-[11px] text-[var(--text-secondary)]">Students can access and download this file</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="for_students" value="1" {{ old('for_students', $resource->for_students) ? 'checked' : '' }}
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
                        <i class="fas fa-save"></i>
                        Save Changes
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
                document.getElementById('dropzoneLabel').textContent = `New file: ${file.name} (${(file.size / (1024 * 1024)).toFixed(2)} MB)`;
                document.getElementById('previewFileName').textContent = file.name;
                document.getElementById('previewFileSize').textContent = `${(file.size / (1024 * 1024)).toFixed(2)} MB`;
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
