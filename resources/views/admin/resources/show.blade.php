<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon & Icons -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <title>{{ $resource->title }} · Admin Document Inspector</title>

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"Space Grotesk"', 'monospace'],
                        code: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        vault: {
                            bg: '#0c0d12',
                            panel: '#151720',
                            card: '#1b1e2a',
                            border: '#282c3f',
                            accent: '#3b82f6',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0c0d12; }
        ::-webkit-scrollbar-thumb { background: #282c3f; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #3b425b; }

        .pdf-page-wrapper {
            position: relative;
            margin-bottom: 20px;
            box-shadow: 0 12px 35px -5px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.06);
            border-radius: 6px;
            background-color: #ffffff;
        }

        .pdf-page-canvas {
            display: block;
            border-radius: 6px;
            max-width: 100%;
            height: auto;
        }

        .glass-btn {
            background: rgba(27, 30, 42, 0.85);
            border: 1px solid rgba(40, 44, 63, 0.9);
            color: #d4d4d8;
            transition: all 0.2s;
        }
        .glass-btn:hover {
            background: rgba(45, 50, 70, 0.95);
            color: #ffffff;
            border-color: rgba(59, 130, 246, 0.5);
        }
        .glass-btn.active {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border-color: rgba(59, 130, 246, 0.6);
        }
    </style>
</head>
<body class="bg-vault-bg text-zinc-100 min-h-screen flex flex-col font-sans overflow-hidden">

    @php
        $ext = strtolower(pathinfo($resource->file_path, PATHINFO_EXTENSION));
        $isPdf = ($ext === 'pdf' || str_contains($resource->file_type ?? '', 'pdf'));
        $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg']);
        $isOffice = in_array($ext, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']);
        $streamUrl = route('resources.stream', $resource);
    @endphp

    <!-- Top Navigation Toolbar -->
    <header class="h-16 bg-vault-panel/95 backdrop-blur-md border-b border-vault-border flex items-center justify-between px-3 sm:px-6 z-30 flex-shrink-0 shadow-xl">
        <!-- Left: Back & Title -->
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.resources.index') }}"
               class="px-3 py-2 rounded-xl glass-btn text-xs font-bold flex items-center gap-1.5 flex-shrink-0">
                <i class="fas fa-arrow-left text-[11px]"></i>
                <span class="hidden sm:inline">Admin Resources</span>
            </a>

            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="text-xs sm:text-sm font-bold text-white truncate max-w-xs sm:max-w-md" title="{{ $resource->title }}">
                        {{ $resource->title }}
                    </h1>
                    @if($resource->subject)
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex-shrink-0">
                            {{ $resource->subject->code }}
                        </span>
                    @endif
                </div>
                <p class="text-[10px] text-zinc-400 truncate hidden md:block">
                    {{ $resource->file_name }} &bull; {{ number_format($resource->file_size / (1024 * 1024), 2) }} MB &bull; Uploaded by {{ $resource->uploadedBy->name ?? 'Admin' }}
                </p>
            </div>
        </div>

        <!-- Center: Controls (PDF) -->
        @if($isPdf)
        <div class="hidden lg:flex items-center gap-1.5 bg-vault-card/90 px-3 py-1.5 rounded-2xl border border-vault-border font-mono text-xs shadow-inner" id="interactiveToolbar">
            <button id="prevPageBtn" class="p-1.5 px-2 rounded-lg hover:bg-zinc-700/60 text-zinc-300 disabled:opacity-30 transition" title="Previous Page">
                <i class="fas fa-chevron-left text-[11px]"></i>
            </button>

            <div class="flex items-center gap-1 px-1">
                <input type="number" id="pageNumberInput" min="1" value="1"
                       class="w-12 text-center py-0.5 text-xs font-bold bg-zinc-900 border border-vault-border rounded text-white focus:outline-none focus:border-blue-500">
                <span class="text-zinc-400">/ <span id="pageCountDisplay">--</span></span>
            </div>

            <button id="nextPageBtn" class="p-1.5 px-2 rounded-lg hover:bg-zinc-700/60 text-zinc-300 disabled:opacity-30 transition" title="Next Page">
                <i class="fas fa-chevron-right text-[11px]"></i>
            </button>

            <div class="h-4 w-px bg-vault-border mx-1"></div>

            <button id="zoomOutBtn" class="p-1.5 px-2 rounded-lg hover:bg-zinc-700/60 text-zinc-300 transition" title="Zoom Out">
                <i class="fas fa-minus text-[11px]"></i>
            </button>

            <span id="zoomLevelDisplay" class="text-zinc-200 min-w-[50px] text-center font-bold text-[11px]">100%</span>

            <button id="zoomInBtn" class="p-1.5 px-2 rounded-lg hover:bg-zinc-700/60 text-zinc-300 transition" title="Zoom In">
                <i class="fas fa-plus text-[11px]"></i>
            </button>

            <button id="fitWidthBtn" class="px-2.5 py-1 rounded-lg hover:bg-zinc-700/60 text-zinc-300 text-[11px] font-sans font-semibold transition" title="Fit to Width">
                Fit Width
            </button>

            <div class="h-4 w-px bg-vault-border mx-1"></div>

            <button id="scrollModeBtn" class="p-1.5 px-2.5 rounded-lg glass-btn text-[11px] font-sans font-semibold active flex items-center gap-1" title="Continuous Scroll">
                <i class="fas fa-scroll text-[10px]"></i>
                <span>Scroll</span>
            </button>

            <button id="singlePageModeBtn" class="p-1.5 px-2.5 rounded-lg glass-btn text-[11px] font-sans font-semibold flex items-center gap-1" title="Single Page View">
                <i class="fas fa-file text-[10px]"></i>
                <span>Page</span>
            </button>

            <button id="rotateBtn" class="p-1.5 px-2 rounded-lg hover:bg-zinc-700/60 text-zinc-300 transition" title="Rotate Clockwise">
                <i class="fas fa-rotate-right text-[11px]"></i>
            </button>
        </div>
        @endif

        <!-- Right: Engine & Download Actions -->
        <div class="flex items-center gap-2 flex-shrink-0">
            @if($isPdf)
                <button id="engineToggleBtn"
                        class="px-3 py-1.5 rounded-xl glass-btn text-xs font-bold flex items-center gap-1.5"
                        title="Toggle between Canvas and Native Browser Viewer">
                    <i class="fas fa-sliders text-[11px] text-blue-400"></i>
                    <span id="engineLabel" class="hidden sm:inline">Native Mode</span>
                </button>

                <button id="fullscreenBtn" class="p-2 rounded-xl glass-btn text-xs" title="Toggle Fullscreen">
                    <i class="fas fa-expand"></i>
                </button>
            @endif

            <a href="{{ route('resources.edit', $resource) }}"
               class="px-3 py-1.5 rounded-xl glass-btn text-xs font-bold flex items-center gap-1.5 text-blue-400">
                <i class="fas fa-edit text-[11px]"></i>
                <span class="hidden sm:inline">Edit</span>
            </a>

            <a href="{{ route('resources.download', $resource) }}"
               class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-blue-500/20">
                <i class="fas fa-download text-[11px]"></i>
                <span class="hidden sm:inline">Download</span>
            </a>
        </div>
    </header>

    <!-- Main Viewport -->
    <main class="flex-1 relative overflow-auto flex justify-center bg-vault-bg" id="viewerContainer">
        <div id="loadingIndicator" class="absolute inset-0 flex flex-col items-center justify-center bg-vault-bg/95 z-20 transition-opacity duration-300">
            <div class="w-12 h-12 border-3 border-blue-500/20 border-t-blue-500 rounded-full animate-spin mb-4"></div>
            <p class="text-xs font-mono font-bold tracking-wider text-zinc-300 uppercase">Rendering Document...</p>
        </div>

        @if($isPdf)
            <div id="interactiveViewerWrapper" class="w-full flex flex-col items-center py-6 px-2 sm:px-6">
                <div id="pdfPagesContainer" class="flex flex-col items-center w-full max-w-5xl transition-transform origin-top"></div>
            </div>

            <div id="nativeViewerWrapper" class="hidden w-full h-full p-2 sm:p-4">
                <iframe id="nativePdfIframe" src="" class="w-full h-full rounded-2xl border border-vault-border shadow-2xl bg-zinc-900" frameborder="0"></iframe>
            </div>

        @elseif($isImage)
            <div class="flex flex-col items-center justify-center max-w-5xl w-full p-4 my-auto">
                <img src="{{ $streamUrl }}"
                     alt="{{ $resource->title }}"
                     class="max-h-[82vh] max-w-full rounded-2xl shadow-2xl border border-vault-border object-contain"
                     onload="document.getElementById('loadingIndicator').classList.add('hidden');" />
            </div>

        @elseif($isOffice)
            <div class="w-full h-full max-w-5xl p-4 sm:p-6 my-auto flex flex-col items-center justify-center">
                <div class="w-full max-w-2xl bg-vault-card rounded-3xl border border-vault-border p-6 sm:p-8 text-center space-y-6 shadow-2xl">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500/20 to-indigo-500/20 border border-blue-500/30 flex items-center justify-center text-3xl text-blue-400 mx-auto">
                        @if(in_array($ext, ['doc', 'docx']))
                            <i class="fas fa-file-word text-blue-500"></i>
                        @elseif(in_array($ext, ['ppt', 'pptx']))
                            <i class="fas fa-file-powerpoint text-amber-500"></i>
                        @elseif(in_array($ext, ['xls', 'xlsx']))
                            <i class="fas fa-file-excel text-emerald-500"></i>
                        @else
                            <i class="fas fa-file-lines text-indigo-400"></i>
                        @endif
                    </div>

                    <div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-mono font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 uppercase tracking-wider">
                            {{ strtoupper($ext) }} Document
                        </span>
                        <h2 class="text-xl font-bold text-white mt-3">{{ $resource->title }}</h2>
                        <p class="text-xs text-zinc-400 mt-1 font-mono">{{ $resource->file_name }} &bull; {{ number_format($resource->file_size / (1024*1024), 2) }} MB</p>
                    </div>

                    @if($resource->description)
                        <div class="p-4 rounded-xl bg-vault-panel/80 border border-vault-border text-left text-xs text-zinc-300 leading-relaxed">
                            <span class="font-bold text-zinc-400 uppercase tracking-wider text-[10px] block mb-1">Description:</span>
                            {{ $resource->description }}
                        </div>
                    @endif

                    <div class="flex items-center justify-center gap-3 pt-2">
                        <a href="{{ route('resources.download', $resource) }}"
                           class="px-6 py-3 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white font-bold text-sm transition flex items-center gap-2 shadow-lg">
                            <i class="fas fa-download"></i>
                            <span>Download {{ strtoupper($ext) }} File</span>
                        </a>
                    </div>
                </div>
            </div>
            <script>document.getElementById('loadingIndicator').classList.add('hidden');</script>

        @else
            <div class="my-auto max-w-md p-8 text-center bg-vault-card rounded-3xl border border-vault-border space-y-5 shadow-2xl">
                <div class="w-16 h-16 rounded-2xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-2xl mx-auto">
                    <i class="fas fa-file-lines"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">{{ $resource->title }}</h3>
                    <p class="text-xs text-zinc-400 mt-1 font-mono">{{ $resource->file_name }}</p>
                </div>
                <a href="{{ route('resources.download', $resource) }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-500 text-white font-bold text-xs transition">
                    <i class="fas fa-download"></i> Download File
                </a>
            </div>
            <script>document.getElementById('loadingIndicator').classList.add('hidden');</script>
        @endif
    </main>

    @if($isPdf)
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const streamUrl = "{{ $streamUrl }}";
        let pdfDoc = null;
        let currentScale = 1.25;
        let currentRotation = 0;
        let currentPageNum = 1;
        let viewMode = 'scroll';
        let currentEngine = 'interactive';
        const renderedPages = new Set();
        const pageViewports = new Map();

        const container = document.getElementById('pdfPagesContainer');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const pageNumInput = document.getElementById('pageNumberInput');
        const pageCountDisplay = document.getElementById('pageCountDisplay');
        const zoomLevelDisplay = document.getElementById('zoomLevelDisplay');
        const viewerContainer = document.getElementById('viewerContainer');

        const loadingTask = pdfjsLib.getDocument({
            url: streamUrl,
            withCredentials: true,
            cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
            cMapPacked: true,
        });

        loadingTask.promise.then(pdf => {
            pdfDoc = pdf;
            pageCountDisplay.textContent = pdf.numPages;
            loadingIndicator.classList.add('hidden');

            if (window.innerWidth < 768) {
                fitToWidth();
            } else {
                initPagePlaceholders();
            }
        }).catch(err => {
            console.error('PDF.js loading failed, switching to native viewer:', err);
            switchToNativeEngine();
        });

        function initPagePlaceholders() {
            if (!pdfDoc) return;
            container.innerHTML = '';
            renderedPages.clear();

            pdfDoc.getPage(1).then(firstPage => {
                const unscaledViewport = firstPage.getViewport({ scale: 1.0, rotation: currentRotation });
                const baseWidth = unscaledViewport.width;
                const baseHeight = unscaledViewport.height;

                for (let num = 1; num <= pdfDoc.numPages; num++) {
                    const pageWrapper = document.createElement('div');
                    pageWrapper.className = 'pdf-page-wrapper';
                    pageWrapper.id = `page-wrapper-${num}`;
                    pageWrapper.dataset.pageNum = num;

                    const width = Math.floor(baseWidth * currentScale);
                    const height = Math.floor(baseHeight * currentScale);

                    pageWrapper.style.width = width + 'px';
                    pageWrapper.style.height = height + 'px';

                    if (viewMode === 'single' && num !== currentPageNum) {
                        pageWrapper.style.display = 'none';
                    }

                    container.appendChild(pageWrapper);
                }

                zoomLevelDisplay.textContent = Math.round(currentScale * 100) + '%';

                if (viewMode === 'scroll') {
                    setupIntersectionObserver();
                } else {
                    renderPageCanvas(currentPageNum);
                }
            });
        }

        let observer = null;
        function setupIntersectionObserver() {
            if (observer) observer.disconnect();

            observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const pageNum = parseInt(entry.target.dataset.pageNum);
                        renderPageCanvas(pageNum);
                        updateCurrentPageDisplay(pageNum);
                    }
                });
            }, {
                root: viewerContainer,
                rootMargin: '200px 0px',
                threshold: 0.05
            });

            document.querySelectorAll('.pdf-page-wrapper').forEach(wrapper => {
                observer.observe(wrapper);
            });
        }

        function renderPageCanvas(pageNum) {
            if (!pdfDoc || renderedPages.has(pageNum)) return;

            const wrapper = document.getElementById(`page-wrapper-${pageNum}`);
            if (!wrapper) return;

            renderedPages.add(pageNum);

            pdfDoc.getPage(pageNum).then(page => {
                const viewport = page.getViewport({ scale: currentScale, rotation: currentRotation });
                pageViewports.set(pageNum, viewport);

                const canvas = document.createElement('canvas');
                canvas.className = 'pdf-page-canvas';
                canvas.id = `canvas-page-${pageNum}`;

                const outputScale = window.devicePixelRatio || 1;
                canvas.width = Math.floor(viewport.width * outputScale);
                canvas.height = Math.floor(viewport.height * outputScale);
                canvas.style.width = Math.floor(viewport.width) + 'px';
                canvas.style.height = Math.floor(viewport.height) + 'px';

                wrapper.style.width = Math.floor(viewport.width) + 'px';
                wrapper.style.height = Math.floor(viewport.height) + 'px';

                wrapper.innerHTML = '';
                wrapper.appendChild(canvas);

                const ctx = canvas.getContext('2d');
                const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;

                const renderContext = {
                    canvasContext: ctx,
                    transform: transform,
                    viewport: viewport,
                };

                page.render(renderContext);
            }).catch(err => {
                renderedPages.delete(pageNum);
                console.error(`Page ${pageNum} render error:`, err);
            });
        }

        function updateCurrentPageDisplay(num) {
            currentPageNum = num;
            if (pageNumInput) pageNumInput.value = num;
        }

        function fitToWidth() {
            if (!pdfDoc) return;
            pdfDoc.getPage(1).then(page => {
                const unscaled = page.getViewport({ scale: 1.0, rotation: currentRotation });
                const availableWidth = viewerContainer.clientWidth - (window.innerWidth < 768 ? 20 : 60);
                currentScale = Math.max(0.4, Math.min(3.0, availableWidth / unscaled.width));
                initPagePlaceholders();
            });
        }

        document.getElementById('zoomInBtn')?.addEventListener('click', () => {
            if (currentScale < 3.0) {
                currentScale = Math.min(3.0, currentScale + 0.25);
                initPagePlaceholders();
            }
        });

        document.getElementById('zoomOutBtn')?.addEventListener('click', () => {
            if (currentScale > 0.4) {
                currentScale = Math.max(0.4, currentScale - 0.25);
                initPagePlaceholders();
            }
        });

        document.getElementById('fitWidthBtn')?.addEventListener('click', fitToWidth);

        document.getElementById('rotateBtn')?.addEventListener('click', () => {
            currentRotation = (currentRotation + 90) % 360;
            initPagePlaceholders();
        });

        function goToPage(targetNum) {
            if (!pdfDoc) return;
            targetNum = Math.max(1, Math.min(pdfDoc.numPages, targetNum));
            updateCurrentPageDisplay(targetNum);

            if (viewMode === 'scroll') {
                const targetWrapper = document.getElementById(`page-wrapper-${targetNum}`);
                if (targetWrapper) targetWrapper.scrollIntoView({ behavior: 'smooth' });
            } else {
                document.querySelectorAll('.pdf-page-wrapper').forEach(wrapper => {
                    const num = parseInt(wrapper.dataset.pageNum);
                    if (num === targetNum) {
                        wrapper.style.display = 'block';
                        renderPageCanvas(num);
                    } else {
                        wrapper.style.display = 'none';
                    }
                });
            }
        }

        document.getElementById('prevPageBtn')?.addEventListener('click', () => goToPage(currentPageNum - 1));
        document.getElementById('nextPageBtn')?.addEventListener('click', () => goToPage(currentPageNum + 1));

        pageNumInput?.addEventListener('change', (e) => {
            const val = parseInt(e.target.value);
            if (!isNaN(val)) goToPage(val);
        });

        const scrollBtn = document.getElementById('scrollModeBtn');
        const singleBtn = document.getElementById('singlePageModeBtn');

        scrollBtn?.addEventListener('click', () => {
            viewMode = 'scroll';
            scrollBtn.classList.add('active');
            singleBtn.classList.remove('active');
            initPagePlaceholders();
        });

        singleBtn?.addEventListener('click', () => {
            viewMode = 'single';
            singleBtn.classList.add('active');
            scrollBtn.classList.remove('active');
            initPagePlaceholders();
        });

        const engineToggleBtn = document.getElementById('engineToggleBtn');
        const engineLabel = document.getElementById('engineLabel');
        const interactiveWrapper = document.getElementById('interactiveViewerWrapper');
        const nativeWrapper = document.getElementById('nativeViewerWrapper');
        const nativeIframe = document.getElementById('nativePdfIframe');
        const interactiveToolbar = document.getElementById('interactiveToolbar');

        function switchToNativeEngine() {
            currentEngine = 'native';
            interactiveWrapper.classList.add('hidden');
            if (interactiveToolbar) interactiveToolbar.classList.add('opacity-40', 'pointer-events-none');
            nativeWrapper.classList.remove('hidden');
            nativeIframe.src = streamUrl + '#toolbar=1&navpanes=1';
            if (engineLabel) engineLabel.textContent = 'Interactive Mode';
            loadingIndicator.classList.add('hidden');
        }

        function switchToInteractiveEngine() {
            currentEngine = 'interactive';
            nativeWrapper.classList.add('hidden');
            nativeIframe.src = '';
            interactiveWrapper.classList.remove('hidden');
            if (interactiveToolbar) interactiveToolbar.classList.remove('opacity-40', 'pointer-events-none');
            if (engineLabel) engineLabel.textContent = 'Native Mode';
            initPagePlaceholders();
        }

        engineToggleBtn?.addEventListener('click', () => {
            if (currentEngine === 'interactive') {
                switchToNativeEngine();
            } else {
                switchToInteractiveEngine();
            }
        });

        document.getElementById('fullscreenBtn')?.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen().catch(() => {});
            }
        });
    </script>
    @endif

</body>
</html>
