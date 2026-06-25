/**
 * License Key Real-time Search and Filter
 * Filters licenses by product name (including sub-products), license key, and status
 */

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('licenseSearch');
    const statusFilter = document.getElementById('licenseStatus');
    
    if (!searchInput || !statusFilter) return;

    const productSections = document.querySelectorAll('.product-section');
    const noResults = document.getElementById('noLicenseResults');
    const countElement = document.getElementById('licenseCount');
    const labelElement = document.getElementById('licenseLabel');

    function filterLicenses() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const statusValue = statusFilter.value; // 'all', 'active', 'expired'
        
        let visibleSections = 0;
        let totalVisibleLicenses = 0;

        productSections.forEach(function(section) {
            const productName = section.getAttribute('data-product-name');
            const productCode = section.getAttribute('data-product-code');
            
            // Get all license items in this section (including nested sub-products)
            const licenseItems = section.querySelectorAll('.license-item');
            let visibleLicensesInSection = 0;

            licenseItems.forEach(function(item) {
                const licenseKey = item.getAttribute('data-license-key');
                const licenseStatus = item.getAttribute('data-license-status'); // 'active' or 'expired'
                const licenseProduct = item.getAttribute('data-license-product');
                const licenseSubProduct = item.getAttribute('data-license-subproduct');
                
                // Check search match (product name, code, license key, sub-product)
                const searchMatches = !searchTerm || 
                    productName.includes(searchTerm) || 
                    productCode.includes(searchTerm) ||
                    licenseKey.includes(searchTerm) ||
                    (licenseProduct && licenseProduct.includes(searchTerm)) ||
                    (licenseSubProduct && licenseSubProduct.includes(searchTerm));
                
                // Check status match
                const statusMatches = statusValue === 'all' || licenseStatus === statusValue;
                
                // Show license if both conditions match
                if (searchMatches && statusMatches) {
                    item.classList.remove('hidden');
                    visibleLicensesInSection++;
                    totalVisibleLicenses++;
                } else {
                    item.classList.add('hidden');
                }
            });

            // Show/hide entire product section based on visible licenses
            if (visibleLicensesInSection > 0) {
                section.classList.remove('hidden');
                visibleSections++;
            } else {
                section.classList.add('hidden');
            }
        });

        // Update counter
        if (countElement) {
            countElement.textContent = totalVisibleLicenses;
        }
        if (labelElement) {
            labelElement.textContent = totalVisibleLicenses === 1 ? 'license' : 'licenses';
        }

        // Show/hide no results message
        if (noResults) {
            if (visibleSections === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }
    }

    // Event listeners
    searchInput.addEventListener('input', filterLicenses);
    statusFilter.addEventListener('change', filterLicenses);

    // Clear search on Escape key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            filterLicenses();
            this.blur();
        }
    });
});
