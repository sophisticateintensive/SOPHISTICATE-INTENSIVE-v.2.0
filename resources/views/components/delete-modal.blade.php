{{-- resources/views/components/delete-modal.blade.php --}}

<div id="deleteModal" class="fixed inset-0 bg-gray-900/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full transform transition-all">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Confirm Deletion</h3>
                    <p class="text-xs text-red-200">This action cannot be undone</p>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6">
            <div class="flex items-start space-x-4">
                <div class="bg-red-100 rounded-full p-2">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-700 font-medium" id="deleteMessage">
                        Are you sure you want to delete this item?
                    </p>
                    <p class="text-sm text-gray-500 mt-1" id="deleteDetails">
                        This action is permanent and cannot be reversed.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 px-6 py-4 flex justify-end space-x-3">
            <button type="button" onclick="closeDeleteModal()"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                Cancel
            </button>
            <button type="button" id="confirmDeleteBtn"
                class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition shadow-md">
                Yes, Delete
            </button>
        </div>
    </div>
</div>

<script>
    let deleteForm = null;
    let deleteCallback = null;

    function openDeleteModal(message, details, formOrCallback) {
        const modal = document.getElementById('deleteModal');
        const messageEl = document.getElementById('deleteMessage');
        const detailsEl = document.getElementById('deleteDetails');

        // Set message
        messageEl.textContent = message || 'Are you sure you want to delete this item?';
        detailsEl.textContent = details || 'This action is permanent and cannot be reversed.';

        // Store form or callback
        if (typeof formOrCallback === 'object' && formOrCallback.tagName === 'FORM') {
            deleteForm = formOrCallback;
            deleteCallback = null;
        } else {
            deleteForm = null;
            deleteCallback = formOrCallback;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        deleteForm = null;
        deleteCallback = null;
    }

    function confirmDelete() {
        if (deleteForm) {
            deleteForm.submit();
        } else if (deleteCallback) {
            deleteCallback();
        }
        closeDeleteModal();
    }

    // Close modal when clicking outside
    document.addEventListener('click', function (e) {
        const modal = document.getElementById('deleteModal');
        if (e.target === modal) {
            closeDeleteModal();
        }
    });

    // Add event listener to confirm button
    document.getElementById('confirmDeleteBtn')?.addEventListener('click', confirmDelete);
</script>
