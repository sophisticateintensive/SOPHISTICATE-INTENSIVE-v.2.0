<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon & Icons -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <title>{{ $resource->title }} · Resource Vault</title>

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

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
        @media print { body { display: none !important; } }
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
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            max-width: 100% !important;
            box-sizing: border-box;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pdf-page-canvas {
            display: block;
            border-radius: 6px;
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
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

        /* Responsive image container */
        .zoomable-image {
            transition: transform 0.2s ease-out;
            transform-origin: center center;
        }
    </style>
</head>
<body class="bg-vault-bg text-zinc-100 min-h-screen flex flex-col font-sans overflow-hidden select-none" oncontextmenu="return false;">

    @php
        $ext = strtolower(pathinfo($resource->file_path, PATHINFO_EXTENSION));
        $isPdf = ($ext === 'pdf' || str_contains($resource->file_type ?? '', 'pdf'));
        $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg']);
        $isOffice = in_array($ext, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']);
        $isText = in_array($ext, ['txt', 'csv', 'log', 'json', 'md']);
        $streamUrl = route('student.resources.stream', $resource);
    @endphp

    <!-- ========== TOP UNIVERSAL NAVIGATION BAR ========== -->
    <header class="h-14 sm:h-16 bg-vault-panel/95 backdrop-blur-md border-b border-vault-border flex items-center justify-between px-2.5 sm:px-6 z-30 flex-shrink-0 shadow-xl">
        <!-- Left: Back Button & Document Info -->
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <a href="{{ route('student.resources.index') }}"
               class="p-2 sm:px-3 sm:py-2 rounded-xl glass-btn text-xs font-bold flex items-center gap-1.5 flex-shrink-0"
               title="Back to Resources">
                <i class="fas fa-arrow-left text-[12px]"></i>
                <span class="hidden md:inline">Back</span>
            </a>

            <div class="min-w-0">
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <h1 class="text-xs sm:text-sm font-bold text-white truncate max-w-[120px] xs:max-w-[160px] sm:max-w-xs md:max-w-md" title="{{ $resource->title }}">
                        {{ $resource->title }}
                    </h1>
                    @if($resource->subject)
                        <span class="px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] font-mono font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 flex-shrink-0">
                            {{ $resource->subject->code }}
                        </span>
                    @endif
                    <span class="hidden sm:inline-block px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-zinc-800 text-zinc-400 border border-zinc-700 uppercase">
                        {{ $ext }}
                    </span>
                </div>
                <p class="text-[10px] text-zinc-400 truncate hidden sm:block">
                    {{ $resource->file_name }} &bull; {{ number_format($resource->file_size / (1024 * 1024), 2) }} MB
                </p>
            </div>
        </div>

        <!-- Center: Desktop Controls Bar (Adaptive per document type) -->
        <div class="hidden lg:flex items-center gap-1.5 bg-vault-card/90 px-3 py-1.5 rounded-2xl border border-vault-border font-mono text-xs shadow-inner">
            @if($isPdf)
                <!-- PDF Page Nav -->
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

                <!-- PDF Zoom Controls -->
                <button id="zoomOutBtn" class="p-1.5 px-2 rounded-lg hover:bg-zinc-700/60 text-zinc-300 transition" title="Zoom Out (-)">
                    <i class="fas fa-minus text-[11px]"></i>
                </button>

                <span id="zoomLevelDisplay" class="text-zinc-200 min-w-[50px] text-center font-bold text-[11px]">100%</span>

                <button id="zoomInBtn" class="p-1.5 px-2 rounded-lg hover:bg-zinc-700/60 text-zinc-300 transition" title="Zoom In (+)">
                    <i class="fas fa-plus text-[11px]"></i>
                </button>

                <button id="fitWidthBtn" class="px-2.5 py-1 rounded-lg hover:bg-zinc-700/60 text-zinc-300 text-[11px] font-sans font-semibold transition" title="Fit to Width">
                    Fit Width
                </button>

                <div class="h-4 w-px bg-vault-border mx-1"></div>

                <!-- PDF View Mode Toggle -->
                <button id="scrollModeBtn" class="p-1.5 px-2.5 rounded-lg glass-btn text-[11px] font-sans font-semibold active flex items-center gap-1" title="Continuous Scroll">
                    <i class="fas fa-scroll text-[10px]"></i>
                    <span>Scroll</span>
                </button>

                <button id="singlePageModeBtn" class="p-1.5 px-2.5 rounded-lg glass-btn text-[11px] font-sans font-semibold flex items-center gap-1" title="Single Page View">
                    <i class="fas fa-file text-[10px]"></i>
                    <span>Page</span>
                </button>

                <button id="rotateBtn" class="p-1.5 px-2 rounded-lg hover:bg-zinc-700/60 text-zinc-300 transition" title="Rotate Clockwise (R)">
                    <i class="fas fa-rotate-right text-[11px]"></i>
                </button>

            @elseif($isImage)
                <!-- Image Controls -->
                <button onclick="zoomImage(-0.25)" class="p-1.5 px-2 rounded-lg hover:bg-zinc-700 text-zinc-300" title="Zoom Out">
                    <i class="fas fa-minus text-[11px]"></i>
                </button>
                <span id="imgZoomDisplay" class="text-zinc-200 min-w-[50px] text-center font-bold text-[11px]">100%</span>
                <button onclick="zoomImage(0.25)" class="p-1.5 px-2 rounded-lg hover:bg-zinc-700 text-zinc-300" title="Zoom In">
                    <i class="fas fa-plus text-[11px]"></i>
                </button>
                <button onclick="resetImageZoom()" class="px-2.5 py-1 rounded-lg hover:bg-zinc-700 text-zinc-300 text-[11px] font-sans font-semibold">
                    Reset
                </button>
                <button onclick="rotateImage()" class="p-1.5 px-2 rounded-lg hover:bg-zinc-700 text-zinc-300" title="Rotate 90°">
                    <i class="fas fa-rotate-right text-[11px]"></i>
                </button>

            @elseif($isOffice)
                <!-- Office Document View Modes -->
                <span class="text-zinc-400 text-xs flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-400"></i>
                    <span>Office Presentation & Document Viewer</span>
                </span>
            @endif
        </div>

        <!-- Right: Actions (Engine Toggle, Fullscreen, Download) -->
        <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
            @if($isPdf)
                <!-- Engine switcher: Canvas vs Native Browser PDF -->
                <button id="engineToggleBtn"
                        class="px-2.5 py-1.5 rounded-xl glass-btn text-xs font-bold flex items-center gap-1.5"
                        title="Switch between Canvas Reader and Native PDF Viewer">
                    <i class="fas fa-sliders text-[11px] text-blue-400"></i>
                    <span id="engineLabel" class="hidden sm:inline">Native Mode</span>
                </button>
            @endif

            <!-- Universal Fullscreen Toggle -->
            <button id="fullscreenBtn" class="p-2 rounded-xl glass-btn text-xs" title="Toggle Fullscreen">
                <i class="fas fa-expand text-[11px]"></i>
            </button>

            <!-- Download Button / Fee Lock Status -->
            @if($canDownload)
                <a href="{{ route('student.resources.download', $resource) }}"
                   class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-blue-500/20"
                   title="Download Original Document">
                    <i class="fas fa-download text-[11px]"></i>
                    <span class="hidden sm:inline">Download</span>
                </a>
            @else
                <div class="px-2.5 py-1 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 font-bold text-[11px] flex items-center gap-1"
                     title="Pay at least 50% fees to unlock downloads">
                    <i class="fas fa-lock text-[9px]"></i>
                    <span class="hidden md:inline">Locked (&lt;50%)</span>
                </div>
            @endif
        </div>
    </header>

    <!-- ========== MAIN DOCUMENT VIEWPORT ========== -->
    <main class="flex-1 relative overflow-auto flex justify-center bg-vault-bg" id="viewerContainer">

        <!-- Universal Loading Spinner -->
        <div id="loadingIndicator" class="absolute inset-0 flex flex-col items-center justify-center bg-vault-bg/95 z-20 transition-opacity duration-300">
            <div class="w-12 h-12 border-3 border-blue-500/20 border-t-blue-500 rounded-full animate-spin mb-4"></div>
            <p class="text-xs font-mono font-bold tracking-wider text-zinc-300 uppercase">Loading Document...</p>
        </div>

        @if($isPdf)
            <!-- PDF Canvas Interactive Reader -->
            <div id="interactiveViewerWrapper" class="w-full flex flex-col items-center py-4 sm:py-6 px-2 sm:px-6 pb-20 lg:pb-6">
                <div id="pdfPagesContainer" class="flex flex-col items-center w-full max-w-5xl transition-transform origin-top"></div>
            </div>

            <!-- Native Browser Engine (Fallback / Switch) -->
            <div id="nativeViewerWrapper" class="hidden w-full h-full p-2 sm:p-4 pb-20 lg:pb-4">
                <iframe id="nativePdfIframe" src="" class="w-full h-full rounded-2xl border border-vault-border shadow-2xl bg-zinc-900" frameborder="0"></iframe>
            </div>

        @elseif($isImage)
            <!-- Image Viewer with Interactive Pan & Zoom -->
            <div class="w-full h-full flex flex-col items-center justify-center p-4 overflow-auto pb-20 lg:pb-4">
                <div class="overflow-hidden flex items-center justify-center max-w-full max-h-full">
                    <img id="mainImageElement"
                         src="{{ $streamUrl }}"
                         alt="{{ $resource->title }}"
                         class="zoomable-image max-h-[80vh] max-w-full rounded-xl shadow-2xl border border-vault-border object-contain"
                         onload="document.getElementById('loadingIndicator').classList.add('hidden');" />
                </div>
            </div>

        @elseif($isOffice)
            <!-- Office Document Reader: Dual View (Cloud Preview + Summary) -->
            <div class="w-full h-full max-w-5xl p-3 sm:p-6 flex flex-col my-auto pb-20 lg:pb-6">
                <!-- Preview / Info Card -->
                <div class="w-full bg-vault-card rounded-2xl border border-vault-border p-5 sm:p-8 space-y-6 shadow-2xl">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6 text-center sm:text-left">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-blue-500/20 to-indigo-500/20 border border-blue-500/30 flex items-center justify-center text-3xl sm:text-4xl text-blue-400 flex-shrink-0">
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

                        <div class="flex-1 min-w-0">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 uppercase">
                                {{ strtoupper($ext) }} Document
                            </span>
                            <h2 class="text-base sm:text-xl font-bold text-white mt-2">{{ $resource->title }}</h2>
                            <p class="text-xs text-zinc-400 mt-1 font-mono">{{ $resource->file_name }} &bull; {{ number_format($resource->file_size / (1024*1024), 2) }} MB</p>

                            @if($resource->description)
                                <p class="text-xs text-zinc-300 mt-3 p-3 bg-zinc-900/60 rounded-xl border border-vault-border leading-relaxed text-left">
                                    {{ $resource->description }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Online Viewer Embed Container (Google Docs Viewer Fallback) -->
                    <div class="w-full h-[60vh] rounded-xl overflow-hidden border border-vault-border bg-zinc-900 relative">
                        <iframe src="https://docs.google.com/viewer?url={{ urlencode($streamUrl) }}&embedded=true"
                                class="w-full h-full border-0"
                                onload="document.getElementById('loadingIndicator').classList.add('hidden');"
                                onerror="this.style.display='none';">
                        </iframe>
                        <div class="absolute inset-x-0 bottom-0 p-3 bg-zinc-900/90 backdrop-blur-sm border-t border-vault-border flex items-center justify-between text-xs">
                            <span class="text-zinc-400 text-[11px] truncate">Document preview provided via cloud renderer</span>
                            @if($canDownload)
                                <a href="{{ route('student.resources.download', $resource) }}"
                                   class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-bold text-xs flex items-center gap-1.5 flex-shrink-0">
                                    <i class="fas fa-download text-[10px]"></i> Download {{ strtoupper($ext) }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <script>document.getElementById('loadingIndicator').classList.add('hidden');</script>

        @elseif($isText)
            <!-- Plain Text / Code File Viewer -->
            <div class="w-full max-w-4xl p-4 sm:p-6 my-auto pb-20 lg:pb-6">
                <div class="bg-vault-card rounded-2xl border border-vault-border p-4 sm:p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-vault-border">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-code text-blue-400"></i>
                            <span class="font-mono text-xs font-bold text-white">{{ $resource->file_name }}</span>
                        </div>
                        <button onclick="copyTextContent()" class="px-2.5 py-1 rounded-lg glass-btn text-[11px] flex items-center gap-1" id="copyBtn">
                            <i class="fas fa-copy"></i>
                            <span>Copy</span>
                        </button>
                    </div>
                    <pre id="textContentArea" class="font-code text-xs text-zinc-300 leading-relaxed overflow-x-auto max-h-[70vh] p-4 bg-zinc-950 rounded-xl border border-zinc-800 whitespace-pre-wrap"></pre>
                </div>
            </div>
            <script>
                fetch("{{ $streamUrl }}")
                    .then(r => r.text())
                    .then(text => {
                        document.getElementById('textContentArea').textContent = text;
                        document.getElementById('loadingIndicator').classList.add('hidden');
                    })
                    .catch(() => {
                        document.getElementById('textContentArea').textContent = "Unable to preview text content.";
                        document.getElementById('loadingIndicator').classList.add('hidden');
                    });

                function copyTextContent() {
                    const text = document.getElementById('textContentArea').textContent;
                    navigator.clipboard.writeText(text);
                    const btn = document.getElementById('copyBtn');
                    btn.innerHTML = '<i class="fas fa-check text-emerald-400"></i> Copied!';
                    setTimeout(() => { btn.innerHTML = '<i class="fas fa-copy"></i> Copy'; }, 2000);
                }
            </script>

        @else
            <!-- Generic File View -->
            <div class="my-auto max-w-md p-8 text-center bg-vault-card rounded-3xl border border-vault-border space-y-5 shadow-2xl">
                <div class="w-16 h-16 rounded-2xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-2xl mx-auto border border-blue-500/20">
                    <i class="fas fa-file-lines"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">{{ $resource->title }}</h3>
                    <p class="text-xs text-zinc-400 mt-1 font-mono">{{ $resource->file_name }}</p>
                </div>
                @if($canDownload)
                    <a href="{{ route('student.resources.download', $resource) }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-500 hover:bg-blue-400 text-white font-bold text-xs transition">
                        <i class="fas fa-download"></i> Download File
                    </a>
                @endif
            </div>
            <script>document.getElementById('loadingIndicator').classList.add('hidden');</script>
        @endif

    </main>

    <!-- ========== UNIVERSAL MOBILE BOTTOM NAVIGATION DOCK (< 1024px) ========== -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 h-14 bg-vault-panel/95 backdrop-blur-md border-t border-vault-border flex items-center justify-between px-3 z-40 flex-shrink-0 font-mono text-xs shadow-2xl">
        @if($isPdf)
            <!-- PDF Mobile Controls -->
            <button id="mobilePrevBtn" class="p-2 px-3 rounded-xl glass-btn font-bold flex items-center gap-1" title="Previous Page">
                <i class="fas fa-chevron-left text-[11px]"></i>
                <span class="text-[11px]">Prev</span>
            </button>

            <!-- Page indicator with touch jump -->
            <div class="flex items-center gap-1 bg-zinc-900/90 px-3 py-1.5 rounded-xl border border-vault-border text-xs">
                <span class="text-zinc-400 text-[10px]">Page</span>
                <span id="mobilePageNum" class="font-bold text-white">1</span>
                <span class="text-zinc-500">/</span>
                <span id="mobilePageTotal" class="text-zinc-400">--</span>
            </div>

            <!-- Quick Zoom / Fit Toggle -->
            <button onclick="fitToWidth()" class="p-2 px-2.5 rounded-xl glass-btn text-[11px] font-sans font-semibold" title="Fit to Screen Width">
                Fit
            </button>

            <button id="mobileNextBtn" class="p-2 px-3 rounded-xl glass-btn font-bold flex items-center gap-1" title="Next Page">
                <span class="text-[11px]">Next</span>
                <i class="fas fa-chevron-right text-[11px]"></i>
            </button>

        @elseif($isImage)
            <!-- Image Mobile Controls -->
            <button onclick="zoomImage(-0.25)" class="p-2 px-3 rounded-xl glass-btn font-bold">
                <i class="fas fa-minus"></i>
            </button>
            <button onclick="resetImageZoom()" class="p-2 px-3 rounded-xl glass-btn font-bold text-[11px]">
                Reset
            </button>
            <button onclick="rotateImage()" class="p-2 px-3 rounded-xl glass-btn font-bold">
                <i class="fas fa-rotate-right"></i>
            </button>
            <button onclick="zoomImage(0.25)" class="p-2 px-3 rounded-xl glass-btn font-bold">
                <i class="fas fa-plus"></i>
            </button>

        @else
            <!-- Other Documents Quick Bar -->
            <span class="text-zinc-400 text-xs truncate">{{ $resource->file_name }}</span>
            @if($canDownload)
                <a href="{{ route('student.resources.download', $resource) }}"
                   class="px-3 py-1.5 bg-blue-600 text-white rounded-lg font-bold text-xs flex items-center gap-1">
                    <i class="fas fa-download"></i> Download
                </a>
            @endif
        @endif
    </nav>

    <!-- ========== SCRIPTS ========== -->
    @if($isPdf)
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const streamUrl = "{{ $streamUrl }}";
        let pdfDoc = null;
        let currentScale = 1.0;
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
            if (document.getElementById('mobilePageTotal')) {
                document.getElementById('mobilePageTotal').textContent = pdf.numPages;
            }
            loadingIndicator.classList.add('hidden');

            // Auto fit to container width to guarantee natural aspect ratio
            fitToWidth();
        }).catch(err => {
            console.error('PDF.js loading failed, switching to native browser viewer:', err);
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
                    pageWrapper.style.maxWidth = '100%';
                    pageWrapper.style.height = 'auto';

                    if (viewMode === 'single' && num !== currentPageNum) {
                        pageWrapper.style.display = 'none';
                    }

                    container.appendChild(pageWrapper);
                }

                if (zoomLevelDisplay) {
                    zoomLevelDisplay.textContent = Math.round(currentScale * 100) + '%';
                }

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
                rootMargin: '250px 0px',
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
                canvas.style.width = '100%';
                canvas.style.height = 'auto';

                wrapper.style.width = Math.floor(viewport.width) + 'px';
                wrapper.style.maxWidth = '100%';
                wrapper.style.height = 'auto';

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
            if (document.getElementById('mobilePageNum')) {
                document.getElementById('mobilePageNum').textContent = num;
            }
        }

        function fitToWidth() {
            if (!pdfDoc) return;
            pdfDoc.getPage(1).then(page => {
                const unscaled = page.getViewport({ scale: 1.0, rotation: currentRotation });
                const containerWidth = (viewerContainer && viewerContainer.clientWidth > 0)
                    ? viewerContainer.clientWidth
                    : window.innerWidth;
                const padding = window.innerWidth < 768 ? 20 : 64;
                const availableWidth = Math.max(260, containerWidth - padding);
                currentScale = Math.max(0.3, Math.min(3.0, availableWidth / unscaled.width));
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
            if (currentScale > 0.3) {
                currentScale = Math.max(0.3, currentScale - 0.25);
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
        document.getElementById('mobilePrevBtn')?.addEventListener('click', () => goToPage(currentPageNum - 1));
        document.getElementById('mobileNextBtn')?.addEventListener('click', () => goToPage(currentPageNum + 1));

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

        function switchToNativeEngine() {
            currentEngine = 'native';
            interactiveWrapper.classList.add('hidden');
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
    </script>
    @endif

    @if($isImage)
    <script>
        let imgScale = 1.0;
        let imgRotation = 0;
        const imgEl = document.getElementById('mainImageElement');

        function applyImgTransform() {
            if (!imgEl) return;
            imgEl.style.transform = `scale(${imgScale}) rotate(${imgRotation}deg)`;
            const display = document.getElementById('imgZoomDisplay');
            if (display) display.textContent = Math.round(imgScale * 100) + '%';
        }

        function zoomImage(delta) {
            imgScale = Math.max(0.5, Math.min(3.0, imgScale + delta));
            applyImgTransform();
        }

        function rotateImage() {
            imgRotation = (imgRotation + 90) % 360;
            applyImgTransform();
        }

        function resetImageZoom() {
            imgScale = 1.0;
            imgRotation = 0;
            applyImgTransform();
        }
    </script>
    @endif

    <script>
        // Universal Fullscreen Toggle
        document.getElementById('fullscreenBtn')?.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen().catch(() => {});
            }
        });
    </script>

</body>
</html>
