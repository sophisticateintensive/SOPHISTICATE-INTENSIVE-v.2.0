@extends('layouts.student')

@section('content')
<!-- Include PDF.js for in-portal embedded canvas rendering -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<style>
    /* Prevent printing protected documents */
    @media print {
        body { display: none !important; }
    }
    .portal-pdf-canvas {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.08);
        border-radius: 4px;
        background-color: #ffffff;
        margin-bottom: 20px;
    }
</style>

<script>
    // Unproxied global storage for PDF.js document instances
    let _activePdfDoc = null;
    let _activeScale = 1.2;

    window.portalPdf = {
        load(url, onPageCount, onDone, onError) {
            _activePdfDoc = null;
            _activeScale = 1.2;
            const container = document.getElementById('modalPdfContainer');
            if (container) container.innerHTML = '';

            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            const loadingTask = pdfjsLib.getDocument({
                url: url,
                withCredentials: true
            });

            loadingTask.promise.then(pdf => {
                _activePdfDoc = pdf;
                onPageCount(pdf.numPages);
                onDone();
                this.renderAll();
            }).catch(err => {
                console.error('PDF error:', err);
                onError(err.message || 'Unable to load document.');
            });
        },

        renderAll() {
            if (!_activePdfDoc) return;
            const container = document.getElementById('modalPdfContainer');
            if (!container) return;
            container.innerHTML = '';

            for (let num = 1; num <= _activePdfDoc.numPages; num++) {
                const wrapper = document.createElement('div');
                wrapper.className = 'flex flex-col items-center w-full mb-6';
                wrapper.dataset.pageNum = num;

                const canvas = document.createElement('canvas');
                canvas.className = 'portal-pdf-canvas max-w-full';
                canvas.id = `modal-canvas-${num}`;

                wrapper.appendChild(canvas);
                container.appendChild(wrapper);

                this.renderPage(num, canvas);
            }
        },

        renderPage(num, canvas) {
            if (!_activePdfDoc) return;
            _activePdfDoc.getPage(num).then(page => {
                const viewport = page.getViewport({ scale: _activeScale });
                const ctx = canvas.getContext('2d');
                const outputScale = window.devicePixelRatio || 1;

                canvas.width = Math.floor(viewport.width * outputScale);
                canvas.height = Math.floor(viewport.height * outputScale);
                canvas.style.width = Math.floor(viewport.width) + 'px';
                canvas.style.height = Math.floor(viewport.height) + 'px';

                const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;

                page.render({
                    canvasContext: ctx,
                    transform: transform,
                    viewport: viewport
                });
            });
        },

        zoomIn(onScaleUpdate) {
            if (_activeScale < 2.5) {
                _activeScale = Math.min(2.5, _activeScale + 0.25);
                onScaleUpdate(_activeScale);
                this.renderAll();
            }
        },

        zoomOut(onScaleUpdate) {
            if (_activeScale > 0.6) {
                _activeScale = Math.max(0.6, _activeScale - 0.25);
                onScaleUpdate(_activeScale);
                this.renderAll();
            }
        },

        fitWidth(onScaleUpdate) {
            if (!_activePdfDoc) return;
            _activePdfDoc.getPage(1).then(page => {
                const viewport = page.getViewport({ scale: 1.0 });
                const modalBody = document.getElementById('modalReaderViewport');
                const containerWidth = (modalBody ? modalBody.clientWidth : 800) - 64;
                _activeScale = Math.max(0.6, Math.min(2.5, containerWidth / viewport.width));
                onScaleUpdate(_activeScale);
                portalPdf.renderAll();
            });
        },

        clear() {
            _activePdfDoc = null;
            const container = document.getElementById('modalPdfContainer');
            if (container) container.innerHTML = '';
        }
    };
</script>

<div class="space-y-6"
     x-data="{
        search: '',
        selectedSubject: '',
        viewerOpen: false,
        activeDoc: null,
        pageCount: 0,
        scale: 1.2,
        loading: false,
        error: null,

        openReader(doc) {
            this.activeDoc = doc;
            this.viewerOpen = true;
            this.loading = true;
            this.error = null;
            this.pageCount = 0;
            this.scale = 1.2;

            document.body.classList.add('overflow-hidden');

            this.$nextTick(() => {
                if (doc.isPdf) {
                    window.portalPdf.load(
                        doc.streamUrl,
                        (count) => { this.pageCount = count; },
                        () => { this.loading = false; },
                        (errMsg) => { this.loading = false; this.error = errMsg; }
                    );
                } else {
                    this.loading = false;
                }
            });
        },

        closeReader() {
            this.viewerOpen = false;
            this.activeDoc = null;
            document.body.classList.remove('overflow-hidden');
            window.portalPdf.clear();
        },

        zoomIn() {
            window.portalPdf.zoomIn((s) => { this.scale = s; });
        },

        zoomOut() {
            window.portalPdf.zoomOut((s) => { this.scale = s; });
        },

        fitWidth() {
            window.portalPdf.fitWidth((s) => { this.scale = s; });
        }
     }"
     @keydown.escape.window="if(viewerOpen) closeReader()">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-cyan-500/10 text-cyan-500 border border-cyan-500/20 font-mono">
                    <i class="fas fa-folder mr-1"></i> LEARNING VAULT
                </span>
                <span class="text-xs text-zinc-400 font-mono">{{ $resources->total() }} Documents</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
                Resource Library
            </h1>
        </div>
    </div>

    {{-- ===== FEE RESTRICTION BANNER (TIERED ACCESS) ===== --}}
    @if(! $canView)
        {{-- 0% Paid: View ❌, Download ❌ --}}
        <div class="flex items-start gap-4 p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400">
            <div class="w-9 h-9 rounded-xl bg-red-500/20 flex items-center justify-center flex-shrink-0 text-red-500 mt-0.5">
                <i class="fas fa-lock text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <p class="text-xs font-black uppercase tracking-wide">Resource Access Locked (0% Paid)</p>
                    <span class="px-2 py-0.5 text-[10px] font-mono font-bold bg-red-500/20 text-red-600 dark:text-red-400 rounded-full">
                        0.0% Paid
                    </span>
                </div>
                <p class="text-xs leading-relaxed opacity-80">
                    You have not made any fee payments for this term. Make a partial payment to unlock <strong>online viewing</strong>, or pay at least <strong>50%</strong> to unlock <strong>file downloads</strong>.
                </p>
                <a href="{{ route('student.fees.index') }}"
                   class="inline-flex items-center gap-1.5 mt-2 text-[11px] font-black text-red-600 dark:text-red-400 hover:underline">
                    <i class="fas fa-wallet text-[10px]"></i> View Fee Statement & Pay &rarr;
                </a>
            </div>
        </div>
    @elseif(! $canDownload)
        {{-- Below 50%: View ✅, Download ❌ --}}
        <div class="flex items-start gap-4 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400">
            <div class="w-9 h-9 rounded-xl bg-amber-500/20 flex items-center justify-center flex-shrink-0 text-amber-500 mt-0.5">
                <i class="fas fa-eye text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <p class="text-xs font-black uppercase tracking-wide">Viewing Unlocked · Downloads Locked</p>
                    <span class="px-2 py-0.5 text-[10px] font-mono font-bold bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-full">
                        {{ number_format($feePercentage, 1) }}% Paid
                    </span>
                </div>
                <p class="text-xs leading-relaxed opacity-80">
                    You can <strong>view all resources online</strong> inside the student portal. Pay at least <strong>50%</strong> of your required term fees to unlock direct file downloads.
                </p>
                <a href="{{ route('student.fees.index') }}"
                   class="inline-flex items-center gap-1.5 mt-2 text-[11px] font-black text-amber-600 dark:text-amber-400 hover:underline">
                    <i class="fas fa-wallet text-[10px]"></i> Pay to Reach 50% &rarr;
                </a>
            </div>
        </div>
    @elseif(! $isFullyPaid)
        {{-- Exactly 50% or Above 50%: View ✅, Download ✅ --}}
        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-unlock-alt text-sm"></i>
                <p class="text-xs font-bold">Full Access Unlocked — {{ number_format($feePercentage, 1) }}% Paid. You can view online and download all study resources.</p>
            </div>
            <a href="{{ route('student.fees.index') }}" class="text-[11px] font-mono font-bold hover:underline">Fee Balance &rarr;</a>
        </div>
    @else
        {{-- 100% Paid: View ✅, Download ✅ --}}
        <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400">
            <i class="fas fa-check-circle text-sm"></i>
            <p class="text-xs font-bold">Full Access — Fees 100% settled. All resources available for viewing and download.</p>
        </div>
    @endif

    <!-- Search and Subject Filter -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="sm:col-span-2 relative">
            <input type="text" x-model="search" placeholder="Filter by topic, subject code or title..."
                class="w-full pl-11 pr-4 py-3.5 bg-zinc-100 dark:bg-zinc-800/90 border border-zinc-200 dark:border-zinc-700/60 rounded-2xl text-xs font-mono text-zinc-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:outline-none shadow-sm transition">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-zinc-400">
                <i class="fas fa-search text-xs"></i>
            </div>
        </div>

        <div>
            <select x-model="selectedSubject"
                class="w-full px-4 py-3.5 bg-zinc-100 dark:bg-zinc-800/90 border border-zinc-200 dark:border-zinc-700/60 rounded-2xl text-xs font-mono text-zinc-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:outline-none shadow-sm transition">
                <option value="">All Courses / Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Resources Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($resources as $resource)
            @php
                $ext = strtolower(pathinfo($resource->file_path, PATHINFO_EXTENSION));
                $subjectId = $resource->subject_id ? (string)$resource->subject_id : '';
                $isPdf = ($ext === 'pdf' || str_contains($resource->file_type ?? '', 'pdf'));
                $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif']);
            @endphp

            <div x-show="(selectedSubject === '' || selectedSubject === '{{ $subjectId }}') && (search === '' || '{{ strtolower($resource->title) }}'.includes(search.toLowerCase()) || '{{ strtolower($resource->description ?? '') }}'.includes(search.toLowerCase()) || '{{ strtolower($resource->subject->code ?? '') }}'.includes(search.toLowerCase()))"
                class="cyber-card p-5 flex flex-col justify-between space-y-4 group">

                <div class="space-y-3">
                    <!-- File type & Course badge + date -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="px-2.5 py-1 rounded-xl text-xs font-black font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700 uppercase">
                                {{ $ext ?: 'DOC' }}
                            </span>
                            @if($resource->subject)
                                <span class="px-2.5 py-1 rounded-xl text-xs font-black font-mono bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                    {{ $resource->subject->code }}
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-xl text-[10px] font-bold font-mono bg-zinc-100 dark:bg-zinc-800/60 text-zinc-400">
                                    General
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] text-zinc-400 font-mono">
                            {{ $resource->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <!-- Title + description -->
                    <div>
                        <h4 class="text-sm font-black text-zinc-900 dark:text-white group-hover:text-cyan-500 transition-colors leading-tight">
                            {{ $resource->title }}
                        </h4>
                        <p class="text-xs text-zinc-400 mt-1.5 line-clamp-2 leading-relaxed">
                            {{ $resource->description ?: 'Official lecture handout and revision document.' }}
                        </p>
                    </div>
                </div>

                <!-- Action footer -->
                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-2 text-xs font-mono">
                    <span class="text-zinc-500 truncate">
                        {{ $resource->uploadedBy->name ?? 'Faculty' }}
                    </span>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- VIEW BUTTON (Opens inside portal modal) --}}
                        @if($canView)
                            <button type="button"
                                    @click="openReader({
                                        id: {{ $resource->id }},
                                        title: '{{ addslashes($resource->title) }}',
                                        fileName: '{{ addslashes($resource->file_name) }}',
                                        ext: '{{ $ext }}',
                                        isPdf: {{ $isPdf ? 'true' : 'false' }},
                                        isImage: {{ $isImage ? 'true' : 'false' }},
                                        subjectCode: '{{ $resource->subject->code ?? '' }}',
                                        streamUrl: '{{ route('student.resources.stream', $resource) }}',
                                        downloadUrl: '{{ route('student.resources.download', $resource) }}'
                                    })"
                                    class="px-3 py-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 font-black hover:scale-105 hover:border-cyan-500 hover:text-cyan-600 dark:hover:text-cyan-400 transition flex items-center gap-1.5 text-xs"
                                    title="View inside portal">
                                <i class="fas fa-eye text-[10px]"></i>
                                <span>View</span>
                            </button>
                        @else
                            <span class="px-2.5 py-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-red-500/70 border border-zinc-200 dark:border-zinc-700 font-bold flex items-center gap-1 text-[11px] cursor-not-allowed opacity-60"
                                  title="Make an initial fee payment to view online">
                                <i class="fas fa-lock text-[9px] text-red-500"></i>
                                <span>View Locked</span>
                            </span>
                        @endif

                        {{-- DOWNLOAD BUTTON --}}
                        @if($canDownload)
                            <a href="{{ route('student.resources.download', $resource) }}"
                               class="px-3 py-1.5 rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-black hover:scale-105 transition flex items-center gap-1.5 text-xs"
                               title="Download file">
                                <i class="fas fa-download text-[10px]"></i>
                                <span>Download</span>
                            </a>
                        @else
                            <span class="px-2.5 py-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 border border-zinc-200 dark:border-zinc-700 font-bold flex items-center gap-1 text-[11px] cursor-not-allowed opacity-60"
                                  title="Pay at least 50% of term fees to unlock download">
                                <i class="fas fa-lock text-[9px] text-amber-500"></i>
                                <span>Locked (&lt;50%)</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-16 text-center cyber-card text-zinc-400 space-y-2">
                <i class="fas fa-folder-open text-3xl opacity-40"></i>
                <p class="text-xs font-bold font-mono">No study resources uploaded yet.</p>
            </div>
        @endforelse
    </div>

    @if($resources->hasPages())
        <div class="pt-2">
            {{ $resources->links() }}
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- SECURE IN-PORTAL DOCUMENT VIEWER MODAL (NO CHROME TOOLBAR / NO DL ICON)   --}}
    {{-- ========================================================================= --}}
    <div x-show="viewerOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex flex-col bg-zinc-950/95 backdrop-blur-md select-none"
         style="display: none;"
         @contextmenu.prevent="false">

        <!-- Modal Toolbar Header -->
        <div class="h-16 bg-zinc-900 border-b border-zinc-800 px-4 sm:px-6 flex items-center justify-between flex-shrink-0 z-10 shadow-lg">
            <!-- Left: Document Details -->
            <div class="flex items-center gap-3 min-w-0">
                <button type="button"
                        @click="closeReader()"
                        class="px-3 py-1.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white border border-zinc-700 text-xs font-bold transition flex items-center gap-1.5 flex-shrink-0">
                    <i class="fas fa-times text-[11px]"></i>
                    <span>Close</span>
                </button>

                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-bold text-white truncate max-w-xs sm:max-w-md md:max-w-lg" x-text="activeDoc?.title"></h2>
                        <template x-if="activeDoc?.subjectCode">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20" x-text="activeDoc?.subjectCode"></span>
                        </template>
                    </div>
                    <p class="text-[11px] text-zinc-400 truncate hidden sm:block" x-text="activeDoc?.fileName"></p>
                </div>
            </div>

            <!-- Center: Zoom Controls (for PDFs) -->
            <template x-if="activeDoc?.isPdf && !loading && !error">
                <div class="hidden sm:flex items-center gap-2 bg-zinc-800/90 px-3 py-1.5 rounded-xl border border-zinc-700/80 font-mono text-xs text-zinc-300">
                    <span>Pages: <strong class="text-white" x-text="pageCount"></strong></span>
                    <div class="h-3 w-px bg-zinc-700 mx-1"></div>
                    <button type="button" @click="zoomOut()" class="px-2 py-0.5 rounded hover:bg-zinc-700 text-zinc-300" title="Zoom Out">
                        <i class="fas fa-minus text-[10px]"></i>
                    </button>
                    <span class="text-white font-bold min-w-[42px] text-center" x-text="Math.round(scale * 100) + '%'"></span>
                    <button type="button" @click="zoomIn()" class="px-2 py-0.5 rounded hover:bg-zinc-700 text-zinc-300" title="Zoom In">
                        <i class="fas fa-plus text-[10px]"></i>
                    </button>
                    <button type="button" @click="fitWidth()" class="px-2 py-0.5 rounded hover:bg-zinc-700 text-zinc-300 text-[11px] font-sans font-semibold ml-1" title="Fit to Width">
                        Fit
                    </button>
                </div>
            </template>

            <!-- Right: Fee Lock Status / Optional Download -->
            <div class="flex items-center gap-2 flex-shrink-0">
                @if($canDownload)
                    <a :href="activeDoc?.downloadUrl"
                       class="px-3.5 py-1.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-zinc-950 font-black text-xs transition flex items-center gap-1.5 shadow-md">
                        <i class="fas fa-download text-[11px]"></i>
                        <span>Download</span>
                    </a>
                @else
                    <div class="px-3 py-1.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 font-bold text-xs flex items-center gap-1.5">
                        <i class="fas fa-lock text-[10px]"></i>
                        <span class="hidden sm:inline">Downloads Locked (&lt;50% Paid)</span>
                        <span class="sm:hidden">Locked</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal Viewport -->
        <div id="modalReaderViewport"
             class="flex-1 overflow-auto p-4 sm:p-8 flex justify-center bg-zinc-950/80 relative"
             oncontextmenu="return false;">

            <!-- Loading Spinner -->
            <div x-show="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-zinc-950/90 z-20">
                <div class="w-12 h-12 border-3 border-cyan-500/20 border-t-cyan-500 rounded-full animate-spin mb-4"></div>
                <p class="text-xs font-mono font-bold tracking-wider text-zinc-400 uppercase">Rendering Document...</p>
            </div>

            <!-- Error State -->
            <div x-show="error" class="my-auto max-w-sm p-6 text-center bg-zinc-900 rounded-2xl border border-zinc-800 space-y-3">
                <i class="fas fa-triangle-exclamation text-amber-400 text-3xl"></i>
                <p class="text-sm font-bold text-white">Preview Notice</p>
                <p class="text-xs text-zinc-400" x-text="error"></p>
            </div>

            <!-- PDF Canvas Container -->
            <template x-if="activeDoc?.isPdf">
                <div id="modalPdfContainer" class="flex flex-col items-center w-full transition-transform origin-top"></div>
            </template>

            <!-- Image Viewer -->
            <template x-if="activeDoc?.isImage">
                <div class="flex flex-col items-center justify-center max-w-4xl w-full my-auto">
                    <img :src="activeDoc?.streamUrl"
                         :alt="activeDoc?.title"
                         class="max-h-[80vh] max-w-full rounded-2xl shadow-2xl border border-zinc-800 object-contain pointer-events-none" />
                </div>
            </template>

            <!-- Other Non-PDF Document Fallback -->
            <template x-if="!activeDoc?.isPdf && !activeDoc?.isImage && !loading">
                <div class="my-auto max-w-md p-8 text-center bg-zinc-900 rounded-3xl border border-zinc-800 space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-2xl mx-auto">
                        <i class="fas fa-file-lines"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white" x-text="activeDoc?.title"></h3>
                        <p class="text-xs text-zinc-400 mt-1" x-text="activeDoc?.fileName"></p>
                    </div>
                    <p class="text-xs text-zinc-400 leading-relaxed">
                        This file format (<span x-text="activeDoc?.ext?.toUpperCase()"></span>) is protected in your student vault.
                    </p>
                    @if($canDownload)
                        <a :href="activeDoc?.downloadUrl"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-500 text-zinc-950 font-black text-xs hover:bg-cyan-400 transition">
                            <i class="fas fa-download"></i> Download File
                        </a>
                    @else
                        <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold">
                            <i class="fas fa-lock mr-1"></i> Pay at least 50% fees to download this file.
                        </div>
                    @endif
                </div>
            </template>
        </div>
    </div>

</div>
@endsection

