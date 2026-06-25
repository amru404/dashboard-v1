/**
 * Sub-Product Real-time Search and Filter
 * Filters sub-products by name, code, and status (active/inactive)
 */

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('subProductSearch');
    const statusFilter = document.getElementById('subProductStatus');
    
    if (!searchInput || !statusFilter) return;

    const subProductItems = document.querySelectorAll('.sub-product-item');
    const noResults = document.getElementById('noSubProductResults');
    const countElement = document.getElementById('subProductCount');
    const labelElement = document.getElementById('subProductLabel');

    function filterSubProducts() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const statusValue = statusFilter.value; // 'all', 'active', 'inactive'
        let visibleCount = 0;

        subProductItems.forEach(function(item) {
            const name = item.getAttribute('data-name');
            const code = item.getAttribute('data-code');
            const status = item.getAttribute('data-status'); // 'active' or 'inactive'
            
            // Check search match
            const searchMatches = !searchTerm || 
                name.includes(searchTerm) || 
                code.includes(searchTerm);
            
            // Check status match
            const statusMatches = statusValue === 'all' || status === statusValue;
            
            // Show item if both conditions match
            if (searchMatches && statusMatches) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        // Update counter
        if (countElement) {
            countElement.textContent = visibleCount;
        }
        if (labelElement) {
            labelElement.textContent = visibleCount === 1 ? 'item' : 'items';
        }

        // Show/hide no results message
        if (noResults) {
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }
    }

    // Event listeners
    searchInput.addEventListener('input', filterSubProducts);
    statusFilter.addEventListener('change', filterSubProducts);

    // Clear search on Escape key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            filterSubProducts();
            this.blur();
        }
    });
});
