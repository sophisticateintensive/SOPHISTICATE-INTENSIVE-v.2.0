<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sophisticate Intensive</title>
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet" />
  <!-- Tailwind via CDN (used for layout only) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <!-- Theme detection (before CSS to avoid flash) -->
  <script>
    (function() {
      const savedTheme = localStorage.getItem('theme');
      const prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;
      const initialTheme = savedTheme || (prefersLight ? 'light' : 'dark');
      document.documentElement.setAttribute('data-theme', initialTheme);
    })();
  </script>
  <style>
    /* ----- CSS VARIABLES FOR THEMING (dark/light) ----- */
    :root {
      /* Base */
      --bg-primary: #0a0e1a;
      --bg-secondary: #111827;
      --bg-card: rgba(255, 255, 255, 0.04);
      --bg-card-hover: rgba(255, 255, 255, 0.08);
      --text-primary: #e2e8f0;
      --text-secondary: #94a3b8;
      --text-muted: #64748b;
      --border-color: rgba(255, 255, 255, 0.06);
      --border-hover: rgba(59, 130, 246, 0.4);
      --glass-bg: rgba(10, 14, 26, 0.7);
      --glass-border: rgba(255, 255, 255, 0.06);
      --shadow-color: rgba(59, 130, 246, 0.15);

      /* Accent */
      --accent: #3b82f6;
      --accent-hover: #2563eb;
      --accent-soft: rgba(59, 130, 246, 0.15);
      --accent-glow: rgba(59, 130, 246, 0.4);
      --accent-gradient: linear-gradient(135deg, #3b82f6, #2563eb);

      /* Overlays */
      --overlay-light: rgba(255, 255, 255, 0.05);
      --overlay-strong: rgba(255, 255, 255, 0.1);

      /* Shadows */
      --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.2);
      --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.3);
      --shadow-accent: 0 0 40px rgba(59, 130, 246, 0.1);

      /* Background effects */
      --mesh-color1: #1e3a8a;
      --mesh-color2: #1e40af;
      --blob-color1: #2563eb;
      --blob-color2: #3b82f6;
      --blob-color3: #60a5fa;

      /* Form */
      --input-bg: rgba(255,255,255,0.04);

      /* CTA */
      --cta-bg: linear-gradient(135deg, #1e3a8a, #1e40af);
    }

    /* Light theme overrides */
    html[data-theme="light"] {
      --bg-primary: #f0f7ff;
      --bg-secondary: #e6f0fa;
      --bg-card: rgba(255, 255, 255, 0.7);
      --bg-card-hover: rgba(255, 255, 255, 0.9);
      --text-primary: #0f172a;
      --text-secondary: #334155;
      --text-muted: #475569;
      --border-color: rgba(0, 0, 0, 0.1);
      --border-hover: rgba(29, 78, 216, 0.5);
      --glass-bg: rgba(255, 255, 255, 0.8);
      --glass-border: rgba(255, 255, 255, 0.3);
      --shadow-color: rgba(29, 78, 216, 0.2);

      --accent: #1d4ed8;
      --accent-hover: #1e40af;
      --accent-soft: rgba(29, 78, 216, 0.1);
      --accent-glow: rgba(29, 78, 216, 0.4);
      --accent-gradient: linear-gradient(135deg, #1e40af, #1e3a8a);

      --overlay-light: rgba(0, 0, 0, 0.04);
      --overlay-strong: rgba(0, 0, 0, 0.08);

      --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
      --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.1);
      --shadow-accent: 0 0 40px rgba(29, 78, 216, 0.15);

      --mesh-color1: #bfdbfe;
      --mesh-color2: #93c5fd;
      --blob-color1: #93c5fd;
      --blob-color2: #60a5fa;
      --blob-color3: #3b82f6;

      --input-bg: rgba(0,0,0,0.03);

      --cta-bg: linear-gradient(135deg, #1e3a8a, #1e40af);
    }

    /* ----- GLOBAL RESET & BASE ----- */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg-primary);
      color: var(--text-primary);
      overflow-x: hidden;
      cursor: default;
      transition: background 0.4s, color 0.4s;
    }

    /* ----- CUSTOM CURSOR (comet) with blue glow ----- */
    .cursor-comet {
      position: fixed;
      width: 12px;
      height: 12px;
      background: radial-gradient(circle, #60a5fa, transparent);
      border-radius: 50%;
      pointer-events: none;
      transform: translate(-50%, -50%);
      filter: blur(2px);
      z-index: 9999;
      transition: width 0.2s, height 0.2s, opacity 0.3s;
      mix-blend-mode: screen;
    }
    html[data-theme="light"] .cursor-comet {
      opacity: 0.6;
    }
    .cursor-comet::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 40px;
      height: 40px;
      background: radial-gradient(circle, rgba(96, 165, 250, 0.2), transparent 70%);
      border-radius: 50%;
      transform: translate(-50%, -50%);
      filter: blur(10px);
      animation: cometPulse 2s infinite alternate;
    }
    @keyframes cometPulse {
      0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0.4; }
      100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0.7; }
    }

    /* ----- ANIMATED BACKGROUND MESH (blue gradient) ----- */
    .mesh-bg {
      position: fixed;
      inset: 0;
      background: radial-gradient(circle at 20% 30%, var(--mesh-color1), var(--bg-primary) 80%);
      z-index: -2;
      animation: meshShift 20s ease-in-out infinite alternate;
      transition: background 0.6s;
    }
    @keyframes meshShift {
      0% { background: radial-gradient(circle at 20% 30%, var(--mesh-color1), var(--bg-primary) 80%); }
      50% { background: radial-gradient(circle at 80% 70%, var(--mesh-color2), var(--bg-primary) 80%); }
      100% { background: radial-gradient(circle at 40% 60%, var(--blob-color1), var(--bg-primary) 80%); }
    }

    /* ----- MORPHING BLOBS (blue palette) ----- */
    .blob {
      position: fixed;
      border-radius: 50%;
      filter: blur(80px);
      opacity: 0.25;
      z-index: -1;
      animation: blobMorph 15s ease-in-out infinite alternate;
      transition: background 0.6s;
    }
    .blob-1 { width: 50vw; height: 50vw; background: var(--blob-color1); top: -20%; right: -10%; }
    .blob-2 { width: 40vw; height: 40vw; background: var(--blob-color2); bottom: -20%; left: -10%; animation-delay: 5s; }
    .blob-3 { width: 30vw; height: 30vw; background: var(--blob-color3); top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: 10s; }
    @keyframes blobMorph {
      0% { border-radius: 50% 50% 50% 50%; transform: translate(0, 0) scale(1); }
      25% { border-radius: 60% 40% 50% 50%; transform: translate(30px, -20px) scale(1.1); }
      50% { border-radius: 40% 60% 60% 40%; transform: translate(-20px, 30px) scale(0.9); }
      75% { border-radius: 50% 30% 70% 50%; transform: translate(40px, 10px) scale(1.05); }
      100% { border-radius: 50% 50% 50% 50%; transform: translate(0, 0) scale(1); }
    }

    /* ----- GLASS NAVIGATION (blue tint) ----- */
    .glass-nav {
      background: var(--glass-bg);
      backdrop-filter: blur(20px) saturate(180%);
      border-bottom: 1px solid var(--glass-border);
      box-shadow: 0 8px 32px rgba(0,0,0,0.2);
      transition: background 0.4s, border-color 0.4s;
    }

    /* ----- SCROLL PROGRESS (blue) ----- */
    #scrollProgress {
      position: fixed;
      top: 0;
      left: 0;
      height: 3px;
      background: var(--accent-gradient);
      z-index: 10000;
      width: 0%;
      transition: width 0.1s;
    }

    /* ----- 3D CUBE (glass logo) with blue glow ----- */
    .scene-3d {
      perspective: 800px;
      width: 200px;
      height: 200px;
      margin: 0 auto;
    }
    .cube {
      width: 100%;
      height: 100%;
      position: relative;
      transform-style: preserve-3d;
      animation: cubeSpin 20s linear infinite;
    }
    .cube-face {
      position: absolute;
      width: 100%;
      height: 100%;
      border: 2px solid rgba(59, 130, 246, 0.2);
      border-radius: 20px;
      background: rgba(59, 130, 246, 0.05);
      backdrop-filter: blur(6px);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      color: white;
      box-shadow: inset 0 0 30px rgba(59, 130, 246, 0.05);
    }
    .cube-face img { width: 80%; height: 80%; object-fit: contain; filter: drop-shadow(0 0 20px rgba(59, 130, 246, 0.3)); }
    .front  { transform: translateZ(100px); }
    .back   { transform: rotateY(180deg) translateZ(100px); }
    .right  { transform: rotateY(90deg) translateZ(100px); }
    .left   { transform: rotateY(-90deg) translateZ(100px); }
    .top    { transform: rotateX(90deg) translateZ(100px); }
    .bottom { transform: rotateX(-90deg) translateZ(100px); }
    @keyframes cubeSpin {
      0% { transform: rotateX(0deg) rotateY(0deg); }
      100% { transform: rotateX(360deg) rotateY(360deg); }
    }

    /* ----- HORIZONTAL SCROLL (programs) ----- */
    .horizontal-scroll {
      display: flex;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      gap: 2rem;
      padding: 1rem 0;
      scrollbar-width: none;
    }
    .horizontal-scroll::-webkit-scrollbar { display: none; }
    .horizontal-scroll .program-card {
      flex: 0 0 300px;
      scroll-snap-align: start;
      background: var(--bg-card);
      backdrop-filter: blur(10px);
      border: 1px solid var(--border-color);
      border-radius: 2rem;
      padding: 2rem;
      transition: all 0.4s;
      transform: scale(0.95);
      opacity: 0.6;
    }
    .horizontal-scroll .program-card.active {
      transform: scale(1);
      opacity: 1;
      border-color: rgba(59, 130, 246, 0.4);
      box-shadow: 0 0 40px rgba(59, 130, 246, 0.1);
    }

    /* ----- 3D TILT CARDS (features) ----- */
    .tilt-card {
      transform-style: preserve-3d;
      transition: transform 0.1s;
    }

    /* ----- EXPLODING CARDS (blue glow) ----- */
    .explode-card {
      position: relative;
      overflow: hidden;
      transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
      background: var(--bg-card);
      border: 1px solid var(--border-color);
    }
    .explode-card:hover {
      transform: scale(1.02) rotate(0.5deg);
      border-color: var(--border-hover);
    }
    .explode-card::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at var(--mx, 50%) var(--my, 50%), rgba(59, 130, 246, 0.15), transparent 70%);
      opacity: 0;
      transition: opacity 0.4s;
      pointer-events: none;
    }
    .explode-card:hover::before {
      opacity: 1;
    }

    /* ----- FLOATING LABELS (contact) ----- */
    .floating-label-group {
      position: relative;
      margin-bottom: 1.5rem;
    }
    .floating-label-group input,
    .floating-label-group textarea {
      width: 100%;
      padding: 1rem 1rem 0.5rem;
      background: var(--input-bg);
      border: none;
      border-bottom: 2px solid var(--border-color);
      color: var(--text-primary);
      font-size: 1rem;
      outline: none;
      transition: border-color 0.3s, background 0.3s;
    }
    .floating-label-group input:focus,
    .floating-label-group textarea:focus {
      border-color: var(--accent);
    }
    .floating-label-group label {
      position: absolute;
      left: 1rem;
      top: 1rem;
      color: var(--text-muted);
      pointer-events: none;
      transition: all 0.3s;
      font-size: 1rem;
    }
    .floating-label-group input:focus ~ label,
    .floating-label-group input:not(:placeholder-shown) ~ label,
    .floating-label-group textarea:focus ~ label,
    .floating-label-group textarea:not(:placeholder-shown) ~ label {
      top: 0;
      font-size: 0.75rem;
      color: var(--accent);
    }

    /* ----- SCROLL REVEAL (elastic) ----- */
    .reveal {
      opacity: 0;
      transform: translateY(60px) scale(0.95);
      transition: all 1.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .reveal.visible {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
    .reveal-left {
      opacity: 0;
      transform: translateX(-80px);
      transition: all 1.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .reveal-left.visible {
      opacity: 1;
      transform: translateX(0);
    }
    .reveal-right {
      opacity: 0;
      transform: translateX(80px);
      transition: all 1.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .reveal-right.visible {
      opacity: 1;
      transform: translateX(0);
    }

    /* ----- PARTICLE CANVAS (galaxy) ----- */
    #galaxy-canvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -3;
      pointer-events: none;
      transition: opacity 0.5s;
    }
    html[data-theme="light"] #galaxy-canvas {
      opacity: 0.3;
    }

    /* ----- SCROLL TO TOP (blue) ----- */
    #scrollTop {
      position: fixed;
      bottom: 2rem;
      right: 2rem;
      width: 50px;
      height: 50px;
      background: var(--accent-soft);
      backdrop-filter: blur(10px);
      border: 1px solid var(--border-hover);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-primary);
      font-size: 1.2rem;
      cursor: pointer;
      z-index: 999;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.4s;
      box-shadow: var(--shadow-accent);
    }
    #scrollTop.visible {
      opacity: 1;
      transform: translateY(0);
    }
    #scrollTop:hover {
      background: var(--accent-soft);
      transform: scale(1.1);
      background: rgba(59, 130, 246, 0.3);
    }

    /* ----- THEME TOGGLE (smooth) ----- */
    .theme-toggle {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s;
      color: var(--text-primary);
    }
    .theme-toggle:hover {
      background: var(--bg-card-hover);
      transform: scale(1.1);
    }

    /* ----- TEXT GRADIENT ACCENT (replaces Tailwind gradient classes) ----- */
    .text-gradient-accent {
      background: var(--accent-gradient);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    /* ----- RESPONSIVE TWEAKS ----- */
    @media (max-width: 768px) {
      .scene-3d { width: 150px; height: 150px; }
      .cube-face { font-size: 2rem; }
    }
  </style>
</head>
<body>

  <!-- Scroll progress -->
  <div id="scrollProgress"></div>

  <!-- Custom cursor -->
  <div class="cursor-comet" id="cursorComet"></div>

  <!-- Particle canvas -->
  <canvas id="galaxy-canvas"></canvas>

  <!-- Background mesh & blobs -->
  <div class="mesh-bg"></div>
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>

  <!-- Navigation -->
  <nav class="glass-nav fixed w-full z-50 top-0 left-0">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center space-x-3">
          <img src="{{ asset('images/logo.png') }}" alt="Sophisticate" class="w-10 h-10 rounded-xl shadow-lg object-cover" />
          <div>
            <span class="text-xl font-extrabold tracking-tight text-current">Sophisticate</span>
            <span class="text-[10px] font-medium uppercase tracking-[0.2em] text-blue-400 block -mt-0.5">intensive classes</span>
          </div>
        </div>
        <div class="hidden md:flex items-center space-x-8 text-sm font-medium">
          <a href="#home" class="text-blue-400 border-b-2 border-blue-500 pb-1 transition hover:text-blue-300">Home</a>
          <a href="#about" class="text-current/50 hover:text-current transition">About</a>
          <a href="#programs" class="text-current/50 hover:text-current transition">Programs</a>
          <a href="#features" class="text-current/50 hover:text-current transition">Features</a>
          <a href="#contact" class="text-current/50 hover:text-current transition">Contact</a>
        </div>
        <div class="flex items-center gap-3">
          <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
            <i class="fas fa-moon"></i>
          </button>
          <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-current/80 hover:text-current transition px-4 py-2 rounded-xl border whitespace-nowrap" style="background: var(--overlay-light); border-color: var(--border-color);">
            <i class="fas fa-sign-in-alt text-blue-400 text-xs"></i>
            <span>Log in</span>
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <section id="home" class="relative min-h-screen flex items-center overflow-hidden pt-20">
    <div class="relative max-w-7xl mx-auto px-6 sm:px-8 lg:px-10 z-10 w-full py-20">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-6 reveal" style="animation-delay:0.1s;">
          <div class="inline-flex items-center gap-2 backdrop-blur-sm px-4 py-1.5 rounded-full border text-sm font-medium" style="background: var(--overlay-light); border-color: var(--border-color);">
            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
            since 2021 · empowering excellence
          </div>
          <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-[1.1] tracking-tight">
            <span class="text-current/90">Welcome to</span><br />
            <span class="text-gradient-accent">Sophisticate</span>
            <span class="block text-current/95">Intensive Classes</span>
          </h1>
          <p class="text-lg text-current/60 max-w-lg leading-relaxed">
            Empowering academic excellence through specialised instruction in Mathematics, General Physics, and General Chemistry.
          </p>
          <div class="flex flex-wrap gap-4">
            <a href="#programs" class="group bg-white text-blue-700 px-8 py-4 rounded-2xl font-semibold shadow-2xl shadow-blue-500/20 hover:shadow-blue-500/40 transition hover:scale-105 flex items-center gap-2">
              Explore programs <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition"></i>
            </a>
          </div>
          <div class="flex gap-8 text-sm font-medium text-current/50">
            <div><span class="text-current font-bold text-2xl">5+</span> years</div>
            <div><span class="text-current font-bold text-2xl">500+</span> students</div>
            <div><span class="text-current font-bold text-2xl">95%</span> success</div>
          </div>
        </div>
        <div class="flex justify-center lg:justify-end reveal" style="animation-delay:0.3s;">
          <div class="scene-3d">
            <div class="cube">
              <div class="cube-face front"><img src="{{ asset('images/logo.png') }}" alt="logo" /></div>
              <div class="cube-face back"><img src="{{ asset('images/logo.png') }}" alt="logo" /></div>
              <div class="cube-face right"><img src="{{ asset('images/logo.png') }}" alt="logo" /></div>
              <div class="cube-face left"><img src="{{ asset('images/logo.png') }}" alt="logo" /></div>
              <div class="cube-face top"><img src="{{ asset('images/logo.png') }}" alt="logo" /></div>
              <div class="cube-face bottom"><img src="{{ asset('images/logo.png') }}" alt="logo" /></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="absolute bottom-0 left-0 w-full">
      <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
        <path d="M0 80L60 70C120 60 240 40 360 30C480 20 600 20 720 25C840 30 960 40 1080 45C1200 50 1320 50 1380 50L1440 50V80H1380C1320 80 1200 80 1080 80C960 80 840 80 720 80C600 80 480 80 360 80C240 80 120 80 60 80H0Z" fill="var(--bg-primary)" />
      </svg>
    </div>
  </section>

  <!-- ABOUT -->
  <section id="about" class="py-24 relative">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
      <div class="text-center mb-16 reveal">
        <span class="text-blue-400 font-semibold text-sm tracking-widest uppercase">about us</span>
        <h2 class="text-4xl md:text-5xl font-extrabold mt-2 text-current">Modern <span class="text-gradient-accent">excellence</span></h2>
        <p class="text-current/50 max-w-2xl mx-auto mt-3 text-lg">Dedicated to empowering academic excellence through specialized instruction.</p>
      </div>
      <div class="grid md:grid-cols-2 gap-12 items-center">
        <div class="reveal-left">
          <div class="relative">
            <div class="absolute -inset-4 bg-gradient-to-r from-blue-500/20 to-blue-700/20 rounded-3xl blur-2xl"></div>
            <img src="{{ asset('images/logo.png') }}" alt="Sophisticate" class="relative rounded-3xl shadow-2xl border border-white/10 w-full" />
          </div>
        </div>
        <div class="space-y-6 reveal-right">
          <div class="p-8 rounded-3xl border transition" style="background:var(--bg-card); border-color:var(--border-color);">
            <p class="text-current/80 leading-relaxed text-lg"><span class="font-bold text-blue-400">Founded in 2021</span>, Sophisticate Intensive Classes has quickly established itself as a premier institution for specialized instruction in the sciences. Our mission is to provide students with focused, high-quality education in Mathematics, General Physics, and General Chemistry.</p>
          </div>
          <div class="p-8 rounded-3xl border transition" style="background:var(--bg-card); border-color:var(--border-color);">
            <p class="text-current/80 leading-relaxed text-lg"><span class="font-bold italic text-blue-400">“Empowering Academic Excellence”</span> reflects our commitment to helping students master challenging subjects and achieve their academic goals.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- PROGRAMS (horizontal scroll) -->
  <section id="programs" class="py-24" style="background:var(--bg-secondary);">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
      <div class="text-center mb-16 reveal">
        <div class="flex justify-center mb-4">
          <img src="{{ asset('images/logo.png') }}" alt="Sophisticate" class="w-14 h-14 rounded-2xl shadow-lg" />
        </div>
        <span class="text-blue-400 font-semibold text-sm tracking-widest uppercase">programs</span>
        <h2 class="text-4xl md:text-5xl font-extrabold text-current mt-2">Our <span class="text-gradient-accent">academic</span> tracks</h2>
        <p class="text-current/50 max-w-2xl mx-auto mt-3">Scroll horizontally to explore each subject.</p>
      </div>
      <div class="horizontal-scroll" id="programScroll">
        <div class="program-card active" data-index="0">
          <i class="fas fa-calculator text-5xl text-blue-400 mb-4"></i>
          <h3 class="text-2xl font-bold text-current">Mathematics</h3>
          <p class="text-current/60 text-sm mt-2">Master differential & integral calculus, limits, derivatives, and advanced topics.</p>
          <ul class="mt-4 space-y-2 text-current/70 text-sm">
            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i> Precalculus</li>
            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i> Calculus</li>
            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i> Multivariate Calculus </li>
          </ul>
        </div>
        <div class="program-card" data-index="1">
          <i class="fas fa-flask text-5xl text-blue-400 mb-4"></i>
          <h3 class="text-2xl font-bold text-current">General Physics</h3>
          <p class="text-current/60 text-sm mt-2">Explore mechanics, thermodynamics, electromagnetism with hands-on problem solving.</p>
          <ul class="mt-4 space-y-2 text-current/70 text-sm">
            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i> General Physics I</li>
            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i> General Physics II</li>
          </ul>
        </div>
        <div class="program-card" data-index="2">
          <i class="fas fa-vial text-5xl text-blue-400 mb-4"></i>
          <h3 class="text-2xl font-bold text-current">General Chemistry</h3>
          <p class="text-current/60 text-sm mt-2">Atomic structure, chemical reactions, stoichiometry, and solutions.</p>
          <ul class="mt-4 space-y-2 text-current/70 text-sm">
            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i> General Chemistry I</li>
            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i> General Chemistry II</li>
          </ul>
        </div>
        <div class="program-card" data-index="3">
          <i class="fas fa-star text-5xl text-blue-400 mb-4"></i>
          <h3 class="text-2xl font-bold text-current">Coming Soon</h3>
          <p class="text-current/60 text-sm mt-2">More advanced courses in preparation for university-level science.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FEATURES (3D tilt cards) -->
  <section id="features" class="py-24">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
      <div class="text-center mb-16 reveal">
        <div class="flex justify-center mb-4">
          <img src="{{ asset('images/logo.png') }}" alt="Sophisticate" class="w-14 h-14 rounded-2xl shadow-lg" />
        </div>
        <span class="text-blue-400 font-semibold text-sm tracking-widest uppercase">why us</span>
        <h2 class="text-4xl md:text-5xl font-extrabold text-current mt-2">Designed for <span class="text-gradient-accent">success</span></h2>
      </div>
      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="explode-card p-8 rounded-3xl text-center tilt-card reveal" style="animation-delay:0.1s;" data-tilt>
          <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5" style="background: var(--accent-soft);"><i class="fas fa-chart-line text-3xl text-blue-400"></i></div>
          <h4 class="text-xl font-bold text-current">Proven Results</h4>
          <p class="text-current/60 text-sm mt-1"><span class="stat-number text-4xl font-extrabold text-blue-400" data-target="95">0</span>% success rate</p>
        </div>
        <div class="explode-card p-8 rounded-3xl text-center tilt-card reveal" style="animation-delay:0.2s;" data-tilt>
          <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5" style="background: var(--accent-soft);"><i class="fas fa-users text-3xl text-blue-400"></i></div>
          <h4 class="text-xl font-bold text-current">Small Classes</h4>
          <p class="text-current/60 text-sm mt-1"><span class="stat-number text-4xl font-extrabold text-blue-400" data-target="12">0</span> students per class</p>
        </div>
        <div class="explode-card p-8 rounded-3xl text-center tilt-card reveal" style="animation-delay:0.3s;" data-tilt>
          <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5" style="background: var(--accent-soft);"><i class="fas fa-clock text-3xl text-blue-400"></i></div>
          <h4 class="text-xl font-bold text-current">Flexible Schedule</h4>
          <p class="text-current/60 text-sm mt-1">Weekend & evening classes</p>
        </div>
        <div class="explode-card p-8 rounded-3xl text-center tilt-card reveal" style="animation-delay:0.4s;" data-tilt>
          <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5" style="background: var(--accent-soft);"><i class="fas fa-book-open text-3xl text-blue-400"></i></div>
          <h4 class="text-xl font-bold text-current">Quality Materials</h4>
          <p class="text-current/60 text-sm mt-1">Comprehensive guides & resources</p>
        </div>
      </div>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section class="py-24" style="background:var(--bg-secondary);">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
      <div class="text-center mb-16 reveal">
        <div class="flex justify-center mb-4">
          <img src="{{ asset('images/logo.png') }}" alt="Sophisticate" class="w-14 h-14 rounded-2xl shadow-lg" />
        </div>
        <span class="text-blue-400 font-semibold text-sm tracking-widest uppercase">testimonials</span>
        <h2 class="text-4xl md:text-5xl font-extrabold text-current mt-2">What our <span class="text-gradient-accent">students</span> say</h2>
      </div>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="p-8 rounded-3xl border transition tilt-card reveal" style="background:var(--bg-card); border-color:var(--border-color); animation-delay:0.1s;" data-tilt>
          <div class="flex text-yellow-400 text-sm mb-3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
          <p class="text-current/80 italic">“The Mathematics program was exactly what I needed. I aced my entrance exam!”</p>
          <div class="mt-4 font-semibold text-blue-400">— Michael Banda</div>
        </div>
        <div class="p-8 rounded-3xl border transition tilt-card reveal" style="background:var(--bg-card); border-color:var(--border-color); animation-delay:0.2s;" data-tilt>
          <div class="flex text-yellow-400 text-sm mb-3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
          <p class="text-current/80 italic">“Physics was challenging until Sophisticate. Practical approach changed everything.”</p>
          <div class="mt-4 font-semibold text-blue-400">— Chisomo Phiri</div>
        </div>
        <div class="p-8 rounded-3xl border transition tilt-card reveal" style="background:var(--bg-card); border-color:var(--border-color); animation-delay:0.3s;" data-tilt>
          <div class="flex text-yellow-400 text-sm mb-3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
          <p class="text-current/80 italic">“Small class sizes, individual attention. Highly recommend to any science student.”</p>
          <div class="mt-4 font-semibold text-blue-400">— Tapiwa Mwale</div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="relative overflow-hidden py-24" style="background: var(--cta-bg);">
    <div class="absolute inset-0 opacity-20">
      <div class="w-96 h-96 bg-white/10 rounded-full blur-3xl -top-20 -left-20 absolute animate-pulse"></div>
      <div class="w-80 h-80 bg-white/10 rounded-full blur-3xl -bottom-20 -right-20 absolute animate-pulse" style="animation-delay:2s;"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-6 text-center reveal">
      <div class="flex justify-center mb-5">
        <img src="{{ asset('images/logo.png') }}" alt="Sophisticate" class="w-16 h-16 rounded-2xl shadow-2xl border border-white/20" />
      </div>
      <h2 class="text-4xl md:text-5xl font-extrabold text-white">Ready to excel in sciences?</h2>
      <p class="text-blue-100/70 text-lg mt-3 max-w-2xl mx-auto">Join Sophisticate Intensive Classes today and experience academic excellence.</p>
      <div class="flex flex-wrap justify-center gap-4 mt-8">
        <a href="#contact" class="bg-transparent border-2 border-white/30 text-white px-8 py-4 rounded-2xl font-semibold hover:bg-white hover:text-blue-900 transition hover:scale-105 flex items-center gap-2">Contact <i class="fas fa-phone-alt"></i></a>
      </div>
    </div>
  </section>

  <!-- CONTACT -->
  <section id="contact" class="py-24">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
      <div class="grid md:grid-cols-2 gap-14">
        <div class="reveal-left">
          <div class="flex items-center gap-3 mb-6">
            <img src="{{ asset('images/logo.png') }}" alt="Sophisticate" class="w-12 h-12 rounded-xl shadow-lg" />
            <h2 class="text-3xl font-bold text-gradient-accent">Get in touch</h2>
          </div>
          <p class="text-current/60 text-lg mb-8">Have questions about programs or admissions? We're here to help.</p>
          <div class="space-y-4">
            <div class="flex items-center gap-4 group hover:translate-x-2 transition"><span class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: var(--accent-soft);"><i class="fas fa-map-marker-alt text-blue-400"></i></span><span class="text-current/70">Viyere Primary School</span></div>
            <div class="flex items-center gap-4 group hover:translate-x-2 transition"><span class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: var(--accent-soft);"><i class="fas fa-phone text-blue-400"></i></span><span class="text-current/70">+265 888 25 76 36 <br /> +265 991 30 73 43</span></div>
            <div class="flex items-center gap-4 group hover:translate-x-2 transition"><span class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: var(--accent-soft);"><i class="fas fa-envelope text-blue-400"></i></span><span class="text-current/70">recchirwa@gmail.com</span></div>
            <div class="flex items-center gap-4 group hover:translate-x-2 transition"><span class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: var(--accent-soft);"><i class="fas fa-clock text-blue-400"></i></span><span class="text-current/70">Sat–Sun: 8:00 AM – 4:00 PM</span></div>
          </div>
          <div class="flex gap-3 mt-8">
            <a href="#" class="w-11 h-11 rounded-xl flex items-center justify-center transition hover:scale-110" style="background: var(--overlay-light);"><i class="fab fa-facebook-f text-current/70"></i></a>
            <a href="#" class="w-11 h-11 rounded-xl flex items-center justify-center transition hover:scale-110" style="background: var(--overlay-light);"><i class="fab fa-twitter text-current/70"></i></a>
            <a href="#" class="w-11 h-11 rounded-xl flex items-center justify-center transition hover:scale-110" style="background: var(--overlay-light);"><i class="fab fa-instagram text-current/70"></i></a>
            <a href="#" class="w-11 h-11 rounded-xl flex items-center justify-center transition hover:scale-110" style="background: var(--overlay-light);"><i class="fab fa-whatsapp text-current/70"></i></a>
          </div>
        </div>
        <div class="p-8 rounded-3xl border reveal-right" style="background:var(--bg-card); border-color:var(--border-color);">
          <h3 class="text-2xl font-bold text-current mb-6">Send us a message</h3>

          @if(session('contact_success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm font-bold flex items-center gap-2.5">
              <i class="fas fa-check-circle text-lg"></i>
              <span>{{ session('contact_success') }}</span>
            </div>
          @endif

          <form action="{{ route('contact.send') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
              <div class="floating-label-group">
                <input type="text" name="fname" placeholder=" " id="fname" required value="{{ old('fname') }}" />
                <label for="fname">First name *</label>
              </div>
              <div class="floating-label-group">
                <input type="text" name="lname" placeholder=" " id="lname" value="{{ old('lname') }}" />
                <label for="lname">Last name</label>
              </div>
            </div>
            <div class="floating-label-group">
              <input type="email" name="email" placeholder=" " id="email" required value="{{ old('email') }}" />
              <label for="email">Email address *</label>
            </div>
            <div class="floating-label-group">
              <input type="text" name="subject" placeholder=" " id="subject" value="{{ old('subject') }}" />
              <label for="subject">Subject</label>
            </div>
            <div class="floating-label-group">
              <textarea name="message" rows="4" placeholder=" " id="message" required>{{ old('message') }}</textarea>
              <label for="message">Your message *</label>
            </div>
            <button type="submit" class="w-full text-white font-semibold py-3.5 rounded-xl shadow-lg transition hover:scale-[1.02] flex items-center justify-center gap-2 group" style="background: var(--accent-gradient); box-shadow: 0 4px 20px var(--accent-glow);">
              Send message <i class="fas fa-paper-plane group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="text-current/50 py-10 border-t" style="background:var(--bg-primary); border-color: var(--border-color);">
    <div class="max-w-7xl mx-auto px-6 text-center">
      <img src="{{ asset('images/logo.png') }}" alt="Sophisticate" class="w-12 h-12 rounded-xl mx-auto mb-3 shadow-lg" />
      <p class="text-sm">&copy; 2026 Sophisticate Intensive Classes. All rights reserved.</p>
      <p class="text-xs text-blue-400/60 mt-1 tracking-widest">Empowering Academic Excellence</p>
    </div>
  </footer>

  <!-- Scroll to top -->
  <div id="scrollTop" class="hidden md:flex">
    <i class="fas fa-arrow-up"></i>
  </div>

  <!-- ========== SCRIPTS ========== -->
  <script>
    // ----- 0. Sync theme icon on load (based on initial theme) -----
    (function() {
      const theme = document.documentElement.getAttribute('data-theme');
      const icon = document.querySelector('#themeToggle i');
      if (icon) {
        icon.className = theme === 'light' ? 'fas fa-sun' : 'fas fa-moon';
      }
    })();

    // ----- 1. Scroll progress -----
    window.addEventListener('scroll', () => {
      const scrollTop = window.scrollY;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      const progress = (scrollTop / docHeight) * 100;
      document.getElementById('scrollProgress').style.width = progress + '%';
      const btn = document.getElementById('scrollTop');
      if (scrollTop > 300) btn.classList.add('visible');
      else btn.classList.remove('visible');
    });
    document.getElementById('scrollTop').addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // ----- 2. Custom cursor -----
    const cursor = document.getElementById('cursorComet');
    document.addEventListener('mousemove', (e) => {
      cursor.style.left = e.clientX + 'px';
      cursor.style.top = e.clientY + 'px';
    });

    // ----- 3. Particle galaxy (canvas) -----
    const canvas = document.getElementById('galaxy-canvas');
    const ctx = canvas.getContext('2d');
    let w, h, particles, mouseX = 0, mouseY = 0;

    function resize() {
      w = canvas.width = window.innerWidth;
      h = canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resize);
    resize();

    class GalaxyParticle {
      constructor() {
        this.x = Math.random() * w;
        this.y = Math.random() * h;
        this.size = Math.random() * 2 + 0.5;
        this.speedX = (Math.random() - 0.5) * 0.3;
        this.speedY = (Math.random() - 0.5) * 0.3;
        this.opacity = Math.random() * 0.5 + 0.1;
        this.color = `hsl(${Math.random() * 30 + 210}, 80%, 70%)`; // blue range
      }
      update() {
        const dx = mouseX - this.x;
        const dy = mouseY - this.y;
        const dist = Math.sqrt(dx*dx + dy*dy);
        if (dist < 200) {
          const force = (200 - dist) / 200 * 0.02;
          this.x += dx * force;
          this.y += dy * force;
        }
        this.x += this.speedX;
        this.y += this.speedY;
        if (this.x < 0 || this.x > w) this.speedX *= -1;
        if (this.y < 0 || this.y > h) this.speedY *= -1;
        if (this.x < 0) this.x = w;
        if (this.x > w) this.x = 0;
        if (this.y < 0) this.y = h;
        if (this.y > h) this.y = 0;
      }
      draw() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = this.color;
        ctx.shadowColor = 'rgba(59, 130, 246, 0.3)';
        ctx.shadowBlur = 10;
        ctx.fill();
      }
    }

    particles = [];
    for (let i = 0; i < 300; i++) particles.push(new GalaxyParticle());

    function animateGalaxy() {
      ctx.clearRect(0, 0, w, h);
      particles.forEach(p => { p.update(); p.draw(); });
      requestAnimationFrame(animateGalaxy);
    }
    animateGalaxy();

    document.addEventListener('mousemove', (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
    });

    // ----- 4. 3D tilt effect -----
    document.querySelectorAll('[data-tilt]').forEach(el => {
      el.addEventListener('mousemove', (e) => {
        const rect = el.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        const rotateX = (y - centerY) / centerY * -12;
        const rotateY = (x - centerX) / centerX * 12;
        el.style.transform = `perspective(600px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
      });
      el.addEventListener('mouseleave', () => {
        el.style.transform = 'perspective(600px) rotateX(0) rotateY(0) scale(1)';
      });
    });

    // ----- 5. Scroll reveal -----
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible');
      });
    }, { threshold: 0.15 });
    document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => revealObserver.observe(el));

    // ----- 6. Animated counters -----
    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseInt(el.getAttribute('data-target'));
          let current = 0;
          const increment = Math.ceil(target / 40);
          const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
              el.textContent = target + (target > 100 ? '%' : '');
              clearInterval(timer);
            } else {
              el.textContent = current;
            }
          }, 30);
          counterObserver.unobserve(el);
        }
      });
    }, { threshold: 0.5 });
    document.querySelectorAll('.stat-number').forEach(el => counterObserver.observe(el));

    // ----- 7. Horizontal scroll active highlight -----
    const scrollContainer = document.getElementById('programScroll');
    const cards = scrollContainer.querySelectorAll('.program-card');
    scrollContainer.addEventListener('scroll', () => {
      let maxVisible = 0, activeIndex = 0;
      cards.forEach((card, i) => {
        const rect = card.getBoundingClientRect();
        const containerRect = scrollContainer.getBoundingClientRect();
        const visible = Math.min(rect.right, containerRect.right) - Math.max(rect.left, containerRect.left);
        if (visible > maxVisible) { maxVisible = visible; activeIndex = i; }
      });
      cards.forEach((card, i) => card.classList.toggle('active', i === activeIndex));
    });

    // ----- 8. THEME TOGGLE (FULLY FUNCTIONAL) -----
    const toggle = document.getElementById('themeToggle');
    const icon = toggle.querySelector('i');
    toggle.addEventListener('click', () => {
      const currentTheme = document.documentElement.getAttribute('data-theme');
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      icon.className = newTheme === 'light' ? 'fas fa-sun' : 'fas fa-moon';
    });

    // ----- 9. Smooth scroll for nav -----
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });

    // ----- 10. Explode card mouse tracking -----
    document.querySelectorAll('.explode-card').forEach(card => {
      card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        card.style.setProperty('--mx', x / rect.width * 100 + '%');
        card.style.setProperty('--my', y / rect.height * 100 + '%');
      });
    });

    console.log('✨ Sophisticate · blue‑crystal edition – ready.');
  </script>
</body>
</html>
