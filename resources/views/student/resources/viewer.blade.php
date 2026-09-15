<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $resource->title }} · Document Viewer</title>

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

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
                    },
                    colors: {
                        vault: {
                            bg: '#0c0d12',
                            panel: '#151720',
                            card: '#1b1e2a',
                            border: '#282c3f',
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
        /* Disable print */
        @media print {
            body { display: none !important; }
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0c0d12; }
        ::-webkit-scrollbar-thumb { background: #282c3f; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #3b425b; }

        .pdf-page-canvas {
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            background-color: #ffffff;
            margin-bottom: 24px;
        }

        /* Prevent text selection and drag on canvas */
        .prevent-select {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
</head>
<body class="bg-vault-bg text-zinc-100 min-h-screen flex flex-col font-sans select-none overflow-hidden" oncontextmenu="return false;">

    @php
        $ext = strtolower(pathinfo($resource->file_path, PATHINFO_EXTENSION));
        $isPdf = ($ext === 'pdf' || str_contains($resource->file_type ?? '', 'pdf'));
        $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif']);
    @endphp

    <!-- Top Navigation Toolbar -->
    <header class="h-16 bg-vault-panel/95 backdrop-blur-md border-b border-vault-border flex items-center justify-between px-4 sm:px-6 z-30 flex-shrink-0 shadow-lg">
        <!-- Left: Back & Title -->
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('student.resources.index') }}"
               class="px-3 py-2 rounded-xl bg-vault-card hover:bg-zinc-800 text-zinc-300 hover:text-white border border-vault-border text-xs font-bold transition flex items-center gap-1.5 flex-shrink-0">
                <i class="fas fa-arrow-left text-[11px]"></i>
                <span class="hidden sm:inline">Back</span>
            </a>

            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="text-sm font-bold text-white truncate max-w-xs sm:max-w-md md:max-w-lg">
                        {{ $resource->title }}
                    </h1>
                    @if($resource->subject)
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 flex-shrink-0">
                            {{ $resource->subject->code }}
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-zinc-400 truncate hidden md:block">
                    {{ $resource->file_name }} &bull; Protected Student Vault
                </p>
            </div>
        </div>

        <!-- Center: PDF Controls (Zoom & Pages) -->
        @if($isPdf)
        <div class="hidden sm:flex items-center gap-2 bg-vault-card/80 px-3 py-1.5 rounded-xl border border-vault-border font-mono text-xs">
            <button id="prevPageBtn" class="px-2 py-1 rounded hover:bg-zinc-700/60 text-zinc-300 disabled:opacity-30 transition" title="Previous Page">
                <i class="fas fa-chevron-left text-[10px]"></i>
            </button>

            <span class="text-zinc-300">
                Page <span id="pageNumDisplay" class="font-bold text-white">1</span> / <span id="pageCountDisplay" class="text-zinc-400">--</span>
            </span>

            <button id="nextPageBtn" class="px-2 py-1 rounded hover:bg-zinc-700/60 text-zinc-300 disabled:opacity-30 transition" title="Next Page">
                <i class="fas fa-chevron-right text-[10px]"></i>
            </button>

            <div class="h-3 w-px bg-vault-border mx-1"></div>

            <button id="zoomOutBtn" class="px-2 py-1 rounded hover:bg-zinc-700/60 text-zinc-300 transition" title="Zoom Out">
                <i class="fas fa-minus text-[10px]"></i>
            </button>

            <span id="zoomLevelDisplay" class="text-zinc-300 min-w-[45px] text-center font-bold">100%</span>

            <button id="zoomInBtn" class="px-2 py-1 rounded hover:bg-zinc-700/60 text-zinc-300 transition" title="Zoom In">
                <i class="fas fa-plus text-[10px]"></i>
            </button>

            <button id="fitWidthBtn" class="px-2 py-1 rounded hover:bg-zinc-700/60 text-zinc-300 text-[11px] font-sans font-semibold transition" title="Fit to Width">
                Fit
            </button>
        </div>
        @endif

        <!-- Right: Actions & Fee Lock Status -->
        <div class="flex items-center gap-2 flex-shrink-0">
            @if($canDownload)
                <a href="{{ route('student.resources.download', $resource) }}"
                   class="px-3.5 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-zinc-950 font-black text-xs transition flex items-center gap-1.5 shadow-md shadow-cyan-500/10"
                   title="Download Original Document">
                    <i class="fas fa-download text-[11px]"></i>
                    <span>Download</span>
                </a>
            @else
                <div class="px-3 py-1.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 font-bold text-xs flex items-center gap-1.5"
                     title="Pay at least 50% of term fees to unlock downloads">
                    <i class="fas fa-lock text-[10px]"></i>
                    <span class="hidden sm:inline">Downloads Locked (&lt;50% Paid)</span>
                    <span class="sm:hidden">Locked</span>
                </div>
            @endif
        </div>
    </header>

    <!-- Main Reader Viewport -->
    <main class="flex-1 relative overflow-auto flex justify-center p-4 sm:p-8 bg-vault-bg prevent-select" id="viewerContainer">

        <!-- Loading State -->
        <div id="loadingIndicator" class="absolute inset-0 flex flex-col items-center justify-center bg-vault-bg/90 z-20 transition-opacity duration-300">
            <div class="w-12 h-12 border-3 border-cyan-500/20 border-t-cyan-500 rounded-full animate-spin mb-4"></div>
            <p class="text-xs font-mono font-bold tracking-wider text-zinc-400 uppercase">Loading Document...</p>
        </div>

        @if($isPdf)
            <!-- PDF Canvas Container -->
            <div id="pdfPagesContainer" class="flex flex-col items-center w-full transition-transform origin-top"></div>
        @elseif($isImage)
            <!-- Image Viewer -->
            <div class="flex flex-col items-center justify-center max-w-4xl w-full my-auto">
                <img src="{{ route('student.resources.stream', $resource) }}"
                     alt="{{ $resource->title }}"
                     class="max-h-[80vh] max-w-full rounded-2xl shadow-2xl border border-vault-border object-contain pointer-events-none"
                     onload="document.getElementById('loadingIndicator').classList.add('hidden');" />
            </div>
        @else
            <!-- Other Documents Fallback -->
            <div class="my-auto max-w-md p-8 text-center bg-vault-card rounded-3xl border border-vault-border space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-2xl mx-auto">
                    <i class="fas fa-file-lines"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">{{ $resource->title }}</h3>
                    <p class="text-xs text-zinc-400 mt-1">{{ $resource->file_name }}</p>
                </div>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    This file format ({{ strtoupper($ext) }}) is best reviewed via specialized software.
                </p>
                @if($canDownload)
                    <a href="{{ route('student.resources.download', $resource) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-500 text-zinc-950 font-black text-xs hover:bg-cyan-400 transition">
                        <i class="fas fa-download"></i> Download File
                    </a>
                @else
                    <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold">
                        <i class="fas fa-lock mr-1"></i> Pay at least 50% fees to download this file.
                    </div>
                @endif
            </div>
            <script>document.getElementById('loadingIndicator').classList.add('hidden');</script>
        @endif

    </main>

    @if($isPdf)
    <script>
        // PDF.js Worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const streamUrl = "{{ route('student.resources.stream', $resource) }}";
        let pdfDoc = null;
        let scale = 1.25;
        const scaleStep = 0.25;
        const minScale = 0.5;
        const maxScale = 3.0;

        const container = document.getElementById('pdfPagesContainer');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const pageNumDisplay = document.getElementById('pageNumDisplay');
        const pageCountDisplay = document.getElementById('pageCountDisplay');
        const zoomLevelDisplay = document.getElementById('zoomLevelDisplay');

        // Fetch and load PDF document
        const loadingTask = pdfjsLib.getDocument({
            url: streamUrl,
            withCredentials: true,
        });

        loadingTask.promise.then(pdf => {
            pdfDoc = pdf;
            pageCountDisplay.textContent = pdf.numPages;
            loadingIndicator.classList.add('hidden');
            renderAllPages();
        }).catch(err => {
            console.error('Error loading PDF:', err);
            loadingIndicator.innerHTML = `
                <div class="text-center p-6 max-w-sm">
                    <i class="fas fa-triangle-exclamation text-amber-400 text-3xl mb-3"></i>
                    <p class="text-sm font-bold text-white">Unable to render document preview</p>
                    <p class="text-xs text-zinc-400 mt-1">${err.message || 'Please contact administration.'}</p>
                </div>
            `;
        });

        function renderAllPages() {
            if (!pdfDoc) return;
            container.innerHTML = '';

            for (let num = 1; num <= pdfDoc.numPages; num++) {
                const pageWrapper = document.createElement('div');
                pageWrapper.className = 'flex flex-col items-center w-full';
                pageWrapper.dataset.pageNum = num;

                const canvas = document.createElement('canvas');
                canvas.className = 'pdf-page-canvas max-w-full';
                canvas.id = `page-canvas-${num}`;

                pageWrapper.appendChild(canvas);
                container.appendChild(pageWrapper);

                renderPage(num, canvas);
            }
            zoomLevelDisplay.textContent = Math.round(scale * 100) + '%';
        }

        function renderPage(num, canvas) {
            pdfDoc.getPage(num).then(page => {
                const viewport = page.getViewport({ scale: scale });
                const ctx = canvas.getContext('2d');

                // High DPI support
                const outputScale = window.devicePixelRatio || 1;
                canvas.width = Math.floor(viewport.width * outputScale);
                canvas.height = Math.floor(viewport.height * outputScale);
                canvas.style.width = Math.floor(viewport.width) + 'px';
                canvas.style.height = Math.floor(viewport.height) + 'px';

                const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;

                const renderContext = {
                    canvasContext: ctx,
                    transform: transform,
                    viewport: viewport
                };

                page.render(renderContext);
            });
        }

        // Zoom In
        document.getElementById('zoomInBtn').addEventListener('click', () => {
            if (scale < maxScale) {
                scale = Math.min(maxScale, scale + scaleStep);
                renderAllPages();
            }
        });

        // Zoom Out
        document.getElementById('zoomOutBtn').addEventListener('click', () => {
            if (scale > minScale) {
                scale = Math.max(minScale, scale - scaleStep);
                renderAllPages();
            }
        });

        // Fit Width
        document.getElementById('fitWidthBtn').addEventListener('click', () => {
            if (!pdfDoc) return;
            pdfDoc.getPage(1).then(page => {
                const viewport = page.getViewport({ scale: 1.0 });
                const containerWidth = document.getElementById('viewerContainer').clientWidth - 64;
                scale = Math.max(minScale, Math.min(maxScale, containerWidth / viewport.width));
                renderAllPages();
            });
        });

        // Track Current Visible Page on Scroll
        const viewerContainer = document.getElementById('viewerContainer');
        viewerContainer.addEventListener('scroll', () => {
            const wrappers = container.children;
            const containerTop = viewerContainer.scrollTop;
            const containerHeight = viewerContainer.clientHeight;

            for (let i = 0; i < wrappers.length; i++) {
                const wrapper = wrappers[i];
                const top = wrapper.offsetTop - viewerContainer.offsetTop;
                const bottom = top + wrapper.clientHeight;

                if (top <= containerTop + containerHeight / 2 && bottom >= containerTop + containerHeight / 2) {
                    pageNumDisplay.textContent = wrapper.dataset.pageNum;
                    break;
                }
            }
        });

        // Page Navigation Jump Buttons
        document.getElementById('prevPageBtn').addEventListener('click', () => {
            const current = parseInt(pageNumDisplay.textContent) || 1;
            if (current > 1) {
                const target = document.querySelector(`[data-page-num="${current - 1}"]`);
                if (target) target.scrollIntoView({ behavior: 'smooth' });
            }
        });

        document.getElementById('nextPageBtn').addEventListener('click', () => {
            const current = parseInt(pageNumDisplay.textContent) || 1;
            if (pdfDoc && current < pdfDoc.numPages) {
                const target = document.querySelector(`[data-page-num="${current + 1}"]`);
                if (target) target.scrollIntoView({ behavior: 'smooth' });
            }
        });

        // Prevent keyboard shortcut saving (Ctrl+S, Ctrl+P)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'p')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
    @endif

</body>
</html>
