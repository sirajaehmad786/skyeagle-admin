/**
 * Geoapify city autocomplete (via Laravel proxy).
 * Selected value is stored as plain string in the input for DB.
 */
export function initCityAutocomplete({
    searchUrl: defaultSearchUrl = null,
    selector = 'input.city-autocomplete',
} = {}) {
    document.querySelectorAll(selector).forEach((input) => {
        const searchUrl =
            defaultSearchUrl ||
            input.dataset.citySearchUrl ||
            window.packageCitySearchUrl;

        if (!searchUrl) {
            return;
        }

        if (input.dataset.cityAutocompleteInit === '1') {
            return;
        }
        input.dataset.cityAutocompleteInit = '1';
        input.setAttribute('autocomplete', 'off');

        initSingleCityInput(input, searchUrl);
    });
}

function initSingleCityInput(input, searchUrl) {
    const parent = input.closest('.mb-3') || input.parentElement;
    parent.classList.add('city-autocomplete-wrapper');

    const dropdown = document.createElement('div');
    dropdown.className = 'city-autocomplete-dropdown d-none';
    dropdown.setAttribute('role', 'listbox');
    parent.appendChild(dropdown);

    const responseCache = new Map();
    const DEBOUNCE_MS = 150;

    let debounceTimer = null;
    let items = [];
    let activeIndex = -1;
    let abortController = null;
    let requestId = 0;
    let isSelecting = false;
    let lastFetchedTerm = '';

    const hideDropdown = () => {
        dropdown.classList.add('d-none');
        dropdown.replaceChildren();
        activeIndex = -1;
    };

    const selectItem = (index) => {
        const item = items[index];
        if (!item?.value) {
            return;
        }

        isSelecting = true;
        requestId += 1;

        if (abortController) {
            abortController.abort();
            abortController = null;
        }

        input.value = item.value;
        lastFetchedTerm = item.value;
        hideDropdown();
        items = [];

        input.dispatchEvent(new Event('change', { bubbles: true }));

        if (window.jQuery) {
            window.jQuery(input).valid();
        }

        input.focus();

        requestAnimationFrame(() => {
            isSelecting = false;
        });
    };

    const renderSuggestions = (suggestions) => {
        dropdown.replaceChildren();
        items = (suggestions || []).filter((row) => row?.label && row?.value);

        if (!items.length) {
            hideDropdown();
            return;
        }

        items.forEach((item, index) => {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'city-autocomplete-option';
            option.textContent = item.label;
            option.setAttribute('role', 'option');
            option.dataset.cityIndex = String(index);
            dropdown.appendChild(option);
        });

        dropdown.classList.remove('d-none');
        activeIndex = -1;
    };

    const fetchCities = async (term) => {
        const normalizedTerm = term.toLowerCase();
        const currentRequestId = ++requestId;

        if (responseCache.has(normalizedTerm)) {
            if (input.value.trim() !== term) {
                return;
            }
            renderSuggestions(responseCache.get(normalizedTerm));
            return;
        }

        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        try {
            const url = new URL(searchUrl, window.location.origin);
            url.searchParams.set('term', term);

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                signal: abortController.signal,
            });

            const json = await response.json();

            if (currentRequestId !== requestId || isSelecting) {
                return;
            }

            if (input.value.trim() !== term) {
                return;
            }

            if (!response.ok || !json.status) {
                hideDropdown();
                return;
            }

            const data = json.data || [];
            responseCache.set(normalizedTerm, data);
            lastFetchedTerm = term;

            if (responseCache.size > 50) {
                const firstKey = responseCache.keys().next().value;
                responseCache.delete(firstKey);
            }

            renderSuggestions(data);
        } catch (error) {
            if (error.name !== 'AbortError' && currentRequestId === requestId) {
                hideDropdown();
            }
        }
    };

    input.addEventListener('input', () => {
        if (isSelecting) {
            return;
        }

        clearTimeout(debounceTimer);
        const term = input.value.trim();

        if (term.length < 2) {
            requestId += 1;
            hideDropdown();
            items = [];
            lastFetchedTerm = '';
            return;
        }

        if (term === lastFetchedTerm && items.length) {
            dropdown.classList.remove('d-none');
            return;
        }

        debounceTimer = setTimeout(() => fetchCities(term), DEBOUNCE_MS);
    });

    input.addEventListener('keydown', (e) => {
        if (dropdown.classList.contains('d-none') || !items.length) {
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = Math.min(activeIndex + 1, items.length - 1);
            highlightActive();
            scrollActiveIntoView();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
            highlightActive();
            scrollActiveIntoView();
        } else if (e.key === 'Enter' && activeIndex >= 0) {
            e.preventDefault();
            selectItem(activeIndex);
        } else if (e.key === 'Escape') {
            hideDropdown();
        } else if (e.key === 'Tab' && activeIndex >= 0) {
            e.preventDefault();
            selectItem(activeIndex);
        }
    });

    dropdown.addEventListener('pointerdown', (e) => {
        e.preventDefault();
    });

    dropdown.addEventListener('click', (e) => {
        const option = e.target.closest('[data-city-index]');
        if (!option) {
            return;
        }
        selectItem(Number(option.dataset.cityIndex));
    });

    input.addEventListener('blur', () => {
        window.setTimeout(() => {
            if (isSelecting) {
                return;
            }
            hideDropdown();
        }, 150);
    });

    function highlightActive() {
        dropdown.querySelectorAll('.city-autocomplete-option').forEach((btn, i) => {
            btn.classList.toggle('is-active', i === activeIndex);
        });
    }

    function scrollActiveIntoView() {
        dropdown.querySelector('.city-autocomplete-option.is-active')?.scrollIntoView({
            block: 'nearest',
        });
    }
}
