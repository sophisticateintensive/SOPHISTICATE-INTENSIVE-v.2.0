/**
 * Sophisticate Intensive Classes - PWA Client Experience Controller
 * Handles Service Worker registration, install prompts, iOS safari guides,
 * standalone mode detection, and network status notifications.
 */

(function () {
    'use strict';

    let deferredPrompt = null;
    let isAppInstalled = false;

    // Detect if running in standalone mode (installed app)
    const checkStandalone = () => {
        const isStandaloneMatch = window.matchMedia('(display-mode: standalone)').matches;
        const isIOSStandalone = window.navigator.standalone === true;
        const isAndroidInstalled = document.referrer.includes('android-app://');
        return isStandaloneMatch || isIOSStandalone || isAndroidInstalled;
    };

    // Detect iOS device
    const isIOSDevice = () => {
        const userAgent = window.navigator.userAgent.toLowerCase();
        return /iphone|ipad|ipod/.test(userAgent) && !window.MSStream;
    };

    // Detect iOS Safari specifically
    const isIOSSafari = () => {
        const userAgent = window.navigator.userAgent.toLowerCase();
        return isIOSDevice() && /safari/.test(userAgent) && !/crios|fxios|edgios/.test(userAgent);
    };

    // PWA State Object
    window.SophisticatePWA = {
        deferredPrompt: null,
        isInstalled: checkStandalone(),
        isIOS: isIOSDevice(),
        isIOSSafari: isIOSSafari(),
        isOnline: navigator.onLine,

        // Trigger installation
        install() {
            if (this.deferredPrompt) {
                this.deferredPrompt.prompt();
                this.deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('[PWA] Student accepted the install prompt');
                        this.hideInstallButtons();
                    } else {
                        console.log('[PWA] Student dismissed the install prompt');
                    }
                    this.deferredPrompt = null;
                });
            } else if (this.isIOS) {
                this.openIOSGuide();
            } else {
                console.log('[PWA] Native installation prompt unavailable in this context.');
            }
        },

        // Show iOS Install Instructions Modal
        openIOSGuide() {
            const modal = document.getElementById('pwaIOSModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        },

        // Close iOS Install Instructions Modal
        closeIOSGuide() {
            const modal = document.getElementById('pwaIOSModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        },

        // Show install button elements across portal
        showInstallButtons() {
            if (this.isInstalled) return;
            const buttons = document.querySelectorAll('.pwa-install-trigger');
            buttons.forEach((el) => {
                el.classList.remove('hidden');
            });
            const banner = document.getElementById('pwaInstallBanner');
            if (banner && !sessionStorage.getItem('pwa_banner_dismissed')) {
                banner.classList.remove('hidden');
            }
        },

        // Hide install button elements
        hideInstallButtons() {
            const buttons = document.querySelectorAll('.pwa-install-trigger');
            buttons.forEach((el) => {
                el.classList.add('hidden');
            });
            const banner = document.getElementById('pwaInstallBanner');
            if (banner) {
                banner.classList.add('hidden');
            }
        },

        // Dismiss the floating install banner for this session
        dismissBanner() {
            const banner = document.getElementById('pwaInstallBanner');
            if (banner) {
                banner.classList.add('hidden');
            }
            sessionStorage.setItem('pwa_banner_dismissed', 'true');
        }
    };

    // 1. Service Worker Registration
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then((registration) => {
                    console.log('[PWA] Service Worker registered successfully with scope:', registration.scope);

                    // Check for updates
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        if (newWorker) {
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    showUpdateToast(registration);
                                }
                            });
                        }
                    });
                })
                .catch((error) => {
                    console.warn('[PWA] Service Worker registration failed:', error);
                });
        });
    }

    // 2. Capture Android / Chrome / Edge beforeinstallprompt
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent default mini-infobar
        e.preventDefault();
        deferredPrompt = e;
        window.SophisticatePWA.deferredPrompt = e;

        if (!checkStandalone()) {
            window.SophisticatePWA.showInstallButtons();
        }
    });

    // 3. App Installed Event
    window.addEventListener('appinstalled', () => {
        console.log('[PWA] Sophisticate Student Portal was installed on device.');
        window.SophisticatePWA.isInstalled = true;
        window.SophisticatePWA.hideInstallButtons();
        showToast('🎉 Student Portal installed to Home Screen!', 'success');
    });

    // 4. Standalone Mode UI Adjustment
    document.addEventListener('DOMContentLoaded', () => {
        const isStandalone = checkStandalone();
        window.SophisticatePWA.isInstalled = isStandalone;

        if (isStandalone) {
            document.body.classList.add('pwa-standalone');
            window.SophisticatePWA.hideInstallButtons();
        } else {
            // If iOS Safari and not installed, show install trigger
            if (isIOSDevice() && !isStandalone) {
                window.SophisticatePWA.showInstallButtons();
            }
        }

        // Attach click listeners to any .pwa-install-trigger
        document.querySelectorAll('.pwa-install-trigger').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                window.SophisticatePWA.install();
            });
        });
    });

    // 5. Real-time Network Connectivity Watcher
    const updateNetworkStatus = () => {
        const isOnline = navigator.onLine;
        window.SophisticatePWA.isOnline = isOnline;

        const offlineBanner = document.getElementById('pwaOfflineIndicator');
        if (offlineBanner) {
            if (!isOnline) {
                offlineBanner.classList.remove('hidden');
            } else {
                offlineBanner.classList.add('hidden');
                showToast('⚡ Internet connection restored', 'success');
            }
        }
    };

    window.addEventListener('online', updateNetworkStatus);
    window.addEventListener('offline', updateNetworkStatus);

    // 6. Helper: UI Toast
    function showToast(message, type = 'info') {
        let toastContainer = document.getElementById('pwaToastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'pwaToastContainer';
            toastContainer.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-[100] flex flex-col gap-2 max-w-sm w-[90%] pointer-events-none';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        const bgClass = type === 'success' 
            ? 'bg-emerald-500 text-white' 
            : (type === 'warning' ? 'bg-amber-500 text-zinc-950 font-bold' : 'bg-zinc-900 text-white border border-zinc-700');

        toast.className = `${bgClass} px-4 py-3 rounded-2xl shadow-2xl text-xs font-mono font-bold flex items-center justify-between gap-3 pointer-events-auto transition-all duration-300 transform translate-y-[-10px] opacity-0 backdrop-blur-md`;
        toast.innerHTML = `
            <span>${message}</span>
            <button class="opacity-70 hover:opacity-100 text-xs font-black cursor-pointer" onclick="this.parentElement.remove()">&times;</button>
        `;

        toastContainer.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-[-10px]', 'opacity-0');
        });

        // Auto dismiss after 4s
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-[-10px]');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // 7. Helper: Update Notification
    function showUpdateToast(registration) {
        showToast('✨ A new portal version is available. Reloading...', 'info');
        if (registration.waiting) {
            registration.waiting.postMessage({ type: 'SKIP_WAITING' });
        }
    }

})();
