// Admin Download Filter & Layout Switcher
(function() {
    'use strict';

    // ── Elements ──
    const searchInput = document.getElementById('downloadSearch');
    const productFilter = document.getElementById('downloadProductFilter');
    const statusFilter = document.getElementById('downloadStatusFilter');
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const noResults = document.getElementById('noDownloadResults');
    const downloadCount = document.getElementById('downloadCount');
    const downloadLabel = document.getElementById('downloadLabel');
    const layoutButtons = document.querySelectorAll('[data-layout]');
    
    let currentLayout = 'grid';
    
    // ── Filter Function ──
    function filterDownloads() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedProduct = productFilter.value;
        const selectedStatus = statusFilter.value;
        
        const items = document.querySelectorAll('.download-item');
        let visibleCount = 0;
        
        items.forEach(item => {
            const filename = item.dataset.filename || '';
            const product = item.dataset.product || '';
            const productId = item.dataset.productId || '';
            const status = item.dataset.status || '';
            
            // Search filter
            const matchesSearch = !searchTerm || 
                                 filename.includes(searchTerm) || 
                                 product.includes(searchTerm);
            
            // Product filter
            const matchesProduct = selectedProduct === 'all' || productId === selectedProduct;
            
            // Status filter
            const matchesStatus = selectedStatus === 'all' || status === selectedStatus;
            
            // Show/hide based on all filters
            if (matchesSearch && matchesProduct && matchesStatus) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Update counter
        downloadCount.textContent = visibleCount;
        downloadLabel.textContent = visibleCount === 1 ? 'item' : 'items';
        
        // Show/hide no results message
        if (visibleCount === 0) {
            gridView.style.display = 'none';
            listView.style.display = 'none';
            noResults.classList.remove('hidden');
            noResults.style.display = 'block';
        } else {
            noResults.classList.add('hidden');
            noResults.style.display = 'none';
            if (currentLayout === 'grid') {
                gridView.style.display = 'grid';
                listView.style.display = 'none';
            } else {
                gridView.style.display = 'none';
                listView.style.display = 'block';
            }
        }
    }
    
    // ── Layout Switcher ──
    function switchLayout(layout) {
        currentLayout = layout;
        
        // Update buttons
        layoutButtons.forEach(btn => {
            if (btn.dataset.layout === layout) {
                btn.classList.add('bg-vd-primary', 'text-white');
                btn.classList.remove('text-gray-400', 'hover:bg-white/5', 'hover:text-white');
            } else {
                btn.classList.remove('bg-vd-primary', 'text-white');
                btn.classList.add('text-gray-400', 'hover:bg-white/5', 'hover:text-white');
            }
        });
        
        // Switch views
        if (layout === 'grid') {
            gridView.style.display = 'grid';
            listView.style.display = 'none';
        } else {
            gridView.style.display = 'none';
            listView.style.display = 'block';
        }
        
        // Save preference to localStorage
        localStorage.setItem('adminDownloadLayout', layout);
    }
    
    // ── Event Listeners ──
    let searchTimeout = null;
    
    // Debounced search input
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterDownloads, 300);
    });
    
    // Clear search on ESC
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchInput.value = '';
            filterDownloads();
        }
    });
    
    // Instant filter on select changes
    productFilter.addEventListener('change', filterDownloads);
    statusFilter.addEventListener('change', filterDownloads);
    
    // Layout button clicks
    layoutButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            switchLayout(this.dataset.layout);
        });
    });
    
    // ── Initialize ──
    // Restore saved layout preference
    const savedLayout = localStorage.getItem('adminDownloadLayout');
    if (savedLayout && (savedLayout === 'grid' || savedLayout === 'list')) {
        switchLayout(savedLayout);
    }
    
    // Initial filter (in case of pre-filled values)
    filterDownloads();
    
})();
