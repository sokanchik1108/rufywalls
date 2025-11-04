<!-- Кнопка для мобильных + фильтры рядом -->
<div class="filters-wrapper">
    <button class="toggle-filters" style="margin-bottom: -10px;">Фильтры</button>

    <div class="filters" id="filters">
        <h3>Фильтры</h3>
        <a href="{{ route('catalog') }}" style="color:black;text-decoration:none;">
            <strong>Все товары</strong>
        </a>
        @include('partials.filters-part')
    </div>
</div>

<div class="filters-modal" id="filtersModal">
    <div class="filters-modal-content">
        <div class="filters-modal-header mb-3">
            <h3>Фильтры</h3>
            <button class="close-filters" aria-label="Закрыть">&times;</button>
        </div>

        <!-- ✅ Тут тоже тот же include -->
        @include('partials.filters-part')

        <div class="filter-actions">
            <button type="button" class="apply-filters">Показать товары</button>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleFiltersButton = document.querySelector(".toggle-filters");
        const filtersBlock = document.getElementById("filters");

        const filtersModal = document.getElementById("filtersModal");
        const closeFiltersBtn = document.querySelector(".close-filters");
        const applyFiltersBtn = document.querySelector(".apply-filters");

        let activeRooms = [];
        let activeCategories = [];

        function getModalForm() {
            return filtersModal ? filtersModal.querySelector("form") : null;
        }

        function getDesktopForm() {
            return document.querySelector("#filters form#filter-form") || document.getElementById("filter-form");
        }

        function getActiveForm() {
            if (filtersModal && filtersModal.classList.contains("visible")) {
                return getModalForm() || getDesktopForm();
            }
            return getDesktopForm() || getModalForm();
        }

        // --- открытие/закрытие фильтров ---
        if (toggleFiltersButton) {
            toggleFiltersButton.addEventListener("click", () => {
                if (window.innerWidth <= 767 && filtersModal) {
                    filtersModal.classList.add("visible");
                    document.body.style.overflow = "hidden";
                } else if (filtersBlock) {
                    filtersBlock.classList.toggle("visible");
                }
            });
        }

        if (closeFiltersBtn) {
            closeFiltersBtn.addEventListener("click", () => {
                if (filtersModal) {
                    filtersModal.classList.remove("visible");
                    document.body.style.overflow = "";
                }
            });
        }

        // --- отправка AJAX ---
        function sendAjax(formForRequest) {
            if (!window.catalogRoutes || !window.catalogRoutes.catalog) {
                console.error("catalog route not found.");
                return;
            }

            const form = formForRequest || getActiveForm();
            if (!form) {
                console.error("Filter form not found.");
                return;
            }

            const formData = new FormData(form);
            const search = document.getElementById("search");
            const sort = document.getElementById("sort");

            if (search) formData.set("search", search.value);
            if (sort) formData.set("sort", sort.value);

            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                if (key === "room_id" || key === "room_id[]" || key === "category_id" || key === "category_id[]") continue;
                if (value === "") continue;
                params.append(key, value);
            }

            activeRooms.forEach(rid => params.append("room_id[]", rid));
            activeCategories.forEach(cid => params.append("category_id[]", cid));

            const url = `${window.catalogRoutes.catalog}?${params.toString()}`;

            fetch(url, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => res.ok ? res.text() : Promise.reject("Network response was not ok"))
                .then(html => {
                    const container = document.getElementById("product-container");
                    if (container) container.innerHTML = html;

                    initTopBarListeners();
                    initRoomLinks();
                    initCategoryMultiSelect();
                    bindFormChangeListeners();
                    if (typeof initLazyCarousel === "function") initLazyCarousel(document.querySelectorAll(".carousel"));

                    window.scrollTo({
                        top: 0,
                        behavior: "smooth"
                    });
                })
                .catch(err => console.error("Ошибка при фильтрации:", err));
        }

        // --- обработчики форм ---
        function bindFormChangeListeners() {
            const desktopForm = getDesktopForm();
            const modalForm = getModalForm();

            if (desktopForm) {
                desktopForm.removeEventListener?.("change", desktopForm._handler || (() => {}));
                desktopForm._handler = () => sendAjax(desktopForm);
                desktopForm.addEventListener("change", desktopForm._handler);
            }

            if (modalForm) {
                modalForm.removeEventListener?.("change", modalForm._handler || (() => {}));
                modalForm._handler = () => {};
                modalForm.addEventListener("change", modalForm._handler);
            }
        }

        // --- верхняя панель (поиск, сортировка) ---
        function initTopBarListeners() {
            const search = document.getElementById("search");
            const sort = document.getElementById("sort");
            const clearBtn = document.getElementById("clearSearch");

            if (search && clearBtn) {
                clearBtn.style.display = search.value.length > 0 ? "block" : "none";
                search.addEventListener("input", () => clearBtn.style.display = search.value.length > 0 ? "block" : "none");
                clearBtn.addEventListener("click", () => {
                    search.value = "";
                    clearBtn.style.display = "none";
                    sendAjax();
                });

                // --- AUTOCOMPLETE ---
                if (typeof $ !== 'undefined' && $.ui && typeof $(search).autocomplete === 'function') {
                    const autocompleteUrl = window.catalogRoutes?.autocomplete ?? null;
                    if (!autocompleteUrl) console.error('autocomplete route not found.');
                    else {
                        $(search).autocomplete({
                            source: function(request, response) {
                                $.ajax({
                                    url: autocompleteUrl,
                                    method: 'GET',
                                    data: {
                                        term: request.term
                                    },
                                    success: function(data) {
                                        if (!Array.isArray(data) || data.length === 0) {
                                            response([{
                                                label: 'Товары не найдены',
                                                value: '',
                                                disabled: true
                                            }]);
                                        } else response(data);
                                    },
                                    error: function(xhr, status, err) {
                                        console.error('Autocomplete error:', status, err);
                                        response([]);
                                    }
                                });
                            },
                            minLength: 1,
                            delay: 100,
                            select: function(event, ui) {
                                if (ui.item.disabled || ui.item.value === '') {
                                    event.preventDefault();
                                    return false;
                                }
                                search.value = ui.item.value;
                                clearBtn.style.display = 'block';
                                sendAjax();
                            }
                        }).autocomplete("instance")._renderItem = function(ul, item) {
                            const li = $("<li>");
                            const wrapper = $("<div>").text(item.label || item.value || '');
                            if (item.disabled) {
                                wrapper.css({
                                    color: "#000",
                                    fontStyle: "italic",
                                    pointerEvents: "none",
                                    cursor: "default"
                                });
                            }
                            wrapper.addClass("ui-menu-item-wrapper");
                            return li.append(wrapper).appendTo(ul);
                        };
                    }
                } else {
                    console.error('jQuery UI autocomplete не найдена.');
                }

                search.addEventListener("keypress", e => {
                    if (e.key === "Enter") {
                        e.preventDefault();
                        sendAjax();
                    }
                });
            }

            if (sort) {
                sort.removeEventListener?.("change", sort._handler || (() => {}));
                sort._handler = () => sendAjax();
                sort.addEventListener("change", sort._handler);
            }
        }

        // --- Комнаты (мультивыбор через ссылки) ---
        function initRoomLinks() {
            const roomLinks = Array.from(document.querySelectorAll(".filter-links a[data-room]"));
            if (!roomLinks.length) return;

            roomLinks.forEach(a => a.classList.remove("active"));
            if (activeRooms.length === 0) {
                const allLink = document.querySelector('.filter-links a[data-room=""]');
                if (allLink) allLink.classList.add("active");
            } else {
                roomLinks.forEach(a => {
                    const rid = a.dataset.room || "";
                    if (rid !== "" && activeRooms.includes(rid)) a.classList.add("active");
                });
                const allLink = document.querySelector('.filter-links a[data-room=""]');
                if (allLink) allLink.classList.remove("active");
            }

            roomLinks.forEach(link => {
                link.removeEventListener("click", link._handler || (() => {}));
                link._handler = function(e) {
                    e.preventDefault();
                    const roomId = this.dataset.room ?? "";
                    if (roomId === "") activeRooms = [];
                    else activeRooms.includes(roomId) ? activeRooms = activeRooms.filter(id => id !== roomId) : activeRooms.push(roomId);

                    roomLinks.forEach(a => a.classList.remove("active"));
                    if (activeRooms.length === 0) {
                        const allLink = document.querySelector('.filter-links a[data-room=""]');
                        if (allLink) allLink.classList.add("active");
                    } else roomLinks.forEach(a => {
                        const rid = a.dataset.room ?? "";
                        if (rid !== "" && activeRooms.includes(rid)) a.classList.add("active");
                    });

                    sendAjax();
                };
                link.addEventListener("click", link._handler);
            });
        }

        // --- Мультиселект категорий ---
        function initCategoryMultiSelect() {
            const multiselects = document.querySelectorAll(".filter-multiselect");
            multiselects.forEach(ms => {
                const display = ms.querySelector(".select-display");
                const optionsContainer = ms.querySelector(".options");
                const options = Array.from(ms.querySelectorAll(".option"));
                const hiddenInput = ms.querySelector("input[type='hidden']");

                options.forEach(opt => opt.classList.toggle("active", activeCategories.includes(opt.dataset.value)));
                display.textContent = activeCategories.length ?
                    activeCategories.map(v => options.find(o => o.dataset.value === v)?.textContent || v).join(", ") :
                    "Все";

                display.onclick = (e) => {
                    e.stopPropagation();
                    const isOpen = optionsContainer.style.display === "block";
                    document.querySelectorAll(".filter-multiselect .options").forEach(o => o.style.display = "none");
                    optionsContainer.style.display = isOpen ? "none" : "block";
                };

                options.forEach(opt => {
                    opt.onclick = (e) => {
                        e.stopPropagation();
                        const val = opt.dataset.value;
                        if (activeCategories.includes(val)) {
                            activeCategories = activeCategories.filter(v => v !== val);
                            opt.classList.remove("active");
                        } else {
                            activeCategories.push(val);
                            opt.classList.add("active");
                        }

                        display.textContent = activeCategories.length ?
                            activeCategories.map(v => options.find(o => o.dataset.value === v)?.textContent || v).join(", ") :
                            "Все";

                        hiddenInput.value = activeCategories.join(",");
                        sendAjax();
                    };
                });
            });

            document.addEventListener("click", () => {
                document.querySelectorAll(".filter-multiselect .options").forEach(o => o.style.display = "none");
            });
        }

        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener("click", () => {
                const modalForm = getModalForm();
                sendAjax(modalForm);
                if (filtersModal) {
                    filtersModal.classList.remove("visible");
                    document.body.style.overflow = "";
                }
            });
        }

        // --- инициализация ---
        bindFormChangeListeners();
        initTopBarListeners();
        initRoomLinks();
        initCategoryMultiSelect();

        (function hydrateActiveRoomsFromHidden() {
            const firstHidden = document.querySelector('input[name="room_id"]');
            if (firstHidden && firstHidden.value) {
                const vals = firstHidden.value.split(',').map(v => v.trim()).filter(Boolean);
                if (vals.length) {
                    activeRooms = vals;
                    initRoomLinks();
                }
            }
        })();

        (function hydrateCategoriesFromHidden() {
            const firstHidden = document.querySelector('input[name="category_id"]');
            if (firstHidden && firstHidden.value) {
                const vals = firstHidden.value.split(',').map(v => v.trim()).filter(Boolean);
                if (vals.length) {
                    activeCategories = vals;
                    initCategoryMultiSelect();
                }
            }
        })();
    });
</script>