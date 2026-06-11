let activeDropdown = null;

export function openInlineSelect({
    anchor,
    options = [],
    fetchOptions = null,
    selectedValue,
    placeholder = 'Search...',
    searchable = true,
    width = 260,
    pageSize = 20,
    onSelect,
}) {
    closeInlineSelect();

    const anchorEl = anchor?.jquery ? anchor[0] : anchor;
    const dropdown = document.createElement('div');
    dropdown.className = 'inline-floating-select';
    dropdown.style.width = `${width}px`;

    const searchHtml = searchable
        ? `<input type="text" class="form-control form-control-sm inline-floating-search" placeholder="${placeholder}">`
        : '';

    dropdown.innerHTML = `
        ${searchHtml}
        <div class="inline-floating-options"></div>
        <div class="inline-floating-loader d-none">Loading...</div>
    `;

    document.body.appendChild(dropdown);

    const searchInput = dropdown.querySelector('.inline-floating-search');
    const optionList = dropdown.querySelector('.inline-floating-options');
    const loader = dropdown.querySelector('.inline-floating-loader');
    const selected = String(selectedValue ?? '');
    const remoteMode = typeof fetchOptions === 'function';
    const baseOptions = [...options];
    let items = [...baseOptions];
    let term = '';
    let page = 1;
    let hasMore = false;
    let loading = false;
    let debounceTimer = null;

    const renderOptions = () => {
        const filteredOptions = remoteMode
            ? items
            : items.filter((item) => item.text.toLowerCase().includes(term.toLowerCase()));

        optionList.innerHTML = filteredOptions.length
            ? filteredOptions.map((item) => {
                const value = String(item.id ?? '');
                const isActive = value === selected ? ' active' : '';
                return `<button type="button" class="inline-floating-option${isActive}" data-value="${escapeHtml(value)}">${escapeHtml(item.text)}</button>`;
            }).join('')
            : '<div class="inline-floating-empty">No result found</div>';
    };

    const setLoading = (value) => {
        loading = value;
        loader.classList.toggle('d-none', !value);
    };

    const loadRemoteOptions = async (reset = false) => {
        if (!remoteMode || loading) return;

        if (reset) {
            page = 1;
            items = [...baseOptions];
            hasMore = false;
        }

        setLoading(true);

        try {
            const response = await fetchOptions({ term, page, pageSize });
            const newItems = response?.data ?? [];
            hasMore = Boolean(response?.pagination?.more);
            items = reset ? [...baseOptions, ...newItems] : [...items, ...newItems];
            page += 1;
            renderOptions();
            positionDropdown();
        } catch {
            if (reset) {
                optionList.innerHTML = '<div class="inline-floating-empty">Unable to load options</div>';
            }
        } finally {
            setLoading(false);
        }
    };

    const positionDropdown = () => {
        const rect = anchorEl.getBoundingClientRect();
        const gap = 6;
        const margin = 10;
        const dropdownHeight = dropdown.offsetHeight;
        const left = Math.min(Math.max(rect.left, margin), window.innerWidth - width - margin);
        let top = rect.bottom + gap;

        if (top + dropdownHeight > window.innerHeight - margin && rect.top > dropdownHeight + gap) {
            top = rect.top - dropdownHeight - gap;
        }

        dropdown.style.left = `${left}px`;
        dropdown.style.top = `${Math.max(top, margin)}px`;
    };

    renderOptions();
    positionDropdown();

    if (remoteMode) {
        loadRemoteOptions(true);
    }

    if (searchInput) {
        searchInput.focus();
        searchInput.addEventListener('input', () => {
            term = searchInput.value;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                if (remoteMode) {
                    loadRemoteOptions(true);
                } else {
                    renderOptions();
                    positionDropdown();
                }
            }, 250);
        });
    }

    optionList.addEventListener('scroll', () => {
        if (!remoteMode || !hasMore || loading) return;

        const nearBottom = optionList.scrollTop + optionList.clientHeight >= optionList.scrollHeight - 24;
        if (nearBottom) {
            loadRemoteOptions();
        }
    });

    dropdown.addEventListener('click', (event) => {
        const option = event.target.closest('.inline-floating-option');
        if (!option) return;

        closeInlineSelect();
        onSelect(option.dataset.value);
    });

    const outsideHandler = (event) => {
        if (dropdown.contains(event.target) || anchorEl.contains(event.target)) return;
        closeInlineSelect();
    };

    const keyHandler = (event) => {
        if (event.key === 'Escape') closeInlineSelect();
    };

    const repositionHandler = () => positionDropdown();

    document.addEventListener('mousedown', outsideHandler);
    document.addEventListener('keydown', keyHandler);
    window.addEventListener('scroll', repositionHandler, true);
    window.addEventListener('resize', repositionHandler);

    activeDropdown = {
        element: dropdown,
        destroy: () => {
            document.removeEventListener('mousedown', outsideHandler);
            document.removeEventListener('keydown', keyHandler);
            window.removeEventListener('scroll', repositionHandler, true);
            window.removeEventListener('resize', repositionHandler);
            clearTimeout(debounceTimer);
            dropdown.remove();
        },
    };
}

export function closeInlineSelect() {
    if (!activeDropdown) return;

    activeDropdown.destroy();
    activeDropdown = null;
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
