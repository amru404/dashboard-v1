import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

const productTreeManager = () => ({
    search: '',
    status: 'all',
    level: 'all',
    allCollapsed: false,
    collapsedIds: [],
    expandedIds: [],

    init() {
        const expandWhenFiltering = () => {
            if (this.filtersActive()) {
                this.expandAll();
            }
        };

        this.$watch('search', expandWhenFiltering);
        this.$watch('status', expandWhenFiltering);
        this.$watch('level', expandWhenFiltering);
    },

    normalize(value) {
        return (value || '').toString().toLowerCase();
    },

    branchMatches(text, statuses, depths) {
        const query = this.normalize(this.search).trim();
        const matchesSearch = query === '' || this.normalize(text).includes(query);
        const matchesStatus = this.status === 'all' || statuses.includes(this.status);
        const matchesLevel = this.level === 'all'
            || (this.level === 'top' && depths.includes(0))
            || (this.level === 'child' && depths.some((depth) => Number(depth) > 0));

        return matchesSearch && matchesStatus && matchesLevel;
    },

    filtersActive() {
        return this.normalize(this.search).trim() !== ''
            || this.status !== 'all'
            || this.level !== 'all';
    },

    isCollapsed(productId) {
        const id = Number(productId);

        if (this.allCollapsed) {
            return ! this.expandedIds.includes(id);
        }

        return this.collapsedIds.includes(id);
    },

    toggleBranch(productId) {
        const id = Number(productId);

        if (this.allCollapsed) {
            this.expandedIds = this.toggleId(this.expandedIds, id);

            return;
        }

        this.collapsedIds = this.toggleId(this.collapsedIds, id);
    },

    toggleId(ids, id) {
        return ids.includes(id)
            ? ids.filter((existingId) => existingId !== id)
            : [...ids, id];
    },

    expandAll() {
        this.allCollapsed = false;
        this.collapsedIds = [];
        this.expandedIds = [];
    },

    collapseAll() {
        this.allCollapsed = true;
        this.collapsedIds = [];
        this.expandedIds = [];
    },

    clearFilters() {
        this.search = '';
        this.status = 'all';
        this.level = 'all';
    },
});

Alpine.data('productTreeManager', productTreeManager);

const parentProductPicker = (options = [], selectedId = '') => ({
    open: false,
    search: '',
    selectedId: selectedId ? String(selectedId) : '',
    options: options.map((option) => ({
        ...option,
        id: String(option.id),
        depth: Number(option.depth || 0),
        is_active: Boolean(option.is_active),
    })),

    normalize(value) {
        return (value || '').toString().toLowerCase();
    },

    selectedOption() {
        return this.options.find((option) => option.id === this.selectedId) || null;
    },

    filteredOptions() {
        const query = this.normalize(this.search).trim();

        return this.options.filter((option) => {
            const searchableText = [
                option.name,
                option.code,
                option.path,
                option.label,
            ].join(' ');

            return query === '' || this.normalize(searchableText).includes(query);
        });
    },

    selectOption(option) {
        this.selectedId = option.id;
        this.close();
    },

    clearSelection() {
        this.selectedId = '';
        this.close();
    },

    close() {
        this.open = false;
        this.search = '';
    },

    indentStyle(option) {
        return `padding-left: ${Math.min(option.depth, 8) * 1.25}rem`;
    },
});

Alpine.data('parentProductPicker', parentProductPicker);

Alpine.start();
