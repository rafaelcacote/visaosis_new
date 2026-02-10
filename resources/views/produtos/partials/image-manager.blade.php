@php
    $existingImages = collect();

    if (isset($product) && $product) {
        $existingImages = $product->images
            ->map(function ($image) {
                $url = null;

                if ($image->caminho_arquivo && \Illuminate\Support\Facades\Storage::disk('public')->exists($image->caminho_arquivo)) {
                    $url = asset('storage/' . ltrim($image->caminho_arquivo, '/'));
                }

                return [
                    'id' => $image->id,
                    'url' => $url,
                    'name' => $image->nome_arquivo,
                    'order' => $image->ordem ?? 0,
                    'principal' => (bool) $image->principal,
                    'ativo' => (bool) $image->ativo,
                ];
            })
            ->values();
    }

    $encodedExistingImages = rawurlencode($existingImages->toJson(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
@endphp

<div class="mb-4" data-component="product-images-manager" data-max-images="12" data-existing-images="{{ $encodedExistingImages }}">
    <label class="form-label d-flex align-items-center gap-2">
        <span>Imagens do Produto</span>
        <small class="text-muted">(JPEG, PNG ou WEBP até 5MB)</small>
    </label>
    <div class="text-muted small mb-3">
        Arraste e solte as imagens ou clique para selecionar. Defina uma imagem como principal e organize a ordem
        arrastando os cards.
    </div>

    <div class="product-images-dropzone border border-2 border-dashed rounded-3 p-4 text-center cursor-pointer position-relative">
        <div class="dropzone-content">
            <i class="mdi mdi-cloud-upload-outline display-5 text-primary"></i>
            <p class="mb-1 fw-semibold">Solte as imagens aqui</p>
            <p class="text-muted small mb-3">ou</p>
            <button type="button" class="btn btn-outline-primary px-4" data-action="trigger-picker">
                Selecionar imagens
            </button>
            <p class="text-muted small mt-3 mb-0">
                Você pode enviar até 12 imagens por produto.
            </p>
        </div>
        <div class="dropzone-overlay position-absolute top-0 start-0 w-100 h-100 rounded-3" style="display:none;background:rgba(13,110,253,.1);border:2px dashed rgba(13,110,253,.35);"></div>
        <input type="file" class="d-none product-images-picker" multiple accept="image/*">
    </div>

    <div class="product-images-list row g-3 mt-3"></div>
    <div class="product-images-inputs d-none"></div>
</div>

@push('styles')
    @once
        <style>
            .product-images-dropzone.dragover {
                border-color: var(--bs-primary);
                background-color: rgba(13, 110, 253, 0.05);
            }

            .product-image-card {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .product-image-card.dragging {
                opacity: 0.7;
                transform: scale(0.98);
                box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.15);
            }

            .product-image-card .card-img-top {
                height: 180px;
                object-fit: cover;
            }

            .product-image-card .badge-principal {
                position: absolute;
                top: 0.75rem;
                left: 0.75rem;
                z-index: 2;
                pointer-events: none;
            }

            .product-image-card .card-body {
                min-height: 120px;
            }
        </style>
    @endonce
@endpush

@push('scripts')
    @once
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js" integrity="sha384-BSxuMLxX+FCbTdYec3TbXlnMGEEM2QXTFdtDaveen71o+jswm2J36+xFqp8k4VHM" crossorigin="anonymous"></script>
        <script>
            (function () {
                const initManager = () => {
                    const manager = document.querySelector('[data-component="product-images-manager"]');
                    if (!manager) {
                        return;
                    }

                    const dropzone = manager.querySelector('.product-images-dropzone');
                    const picker = manager.querySelector('.product-images-picker');
                    const triggerButton = manager.querySelector('[data-action="trigger-picker"]');
                    const overlay = manager.querySelector('.dropzone-overlay');
                    const list = manager.querySelector('.product-images-list');
                    const inputsContainer = manager.querySelector('.product-images-inputs');
                    const maxImages = parseInt(manager.dataset.maxImages || '12', 10);
                    let existingData = [];
                    try {
                        existingData = manager.dataset.existingImages
                            ? JSON.parse(decodeURIComponent(manager.dataset.existingImages))
                            : [];
                    } catch (error) {
                        existingData = [];
                        console.error('Não foi possível carregar as imagens existentes do produto.', error);
                    }
                    const initialData = { existing: existingData };

                    const state = {
                        items: [],
                        primaryUid: null
                    };

                    const placeholderImage = 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(
                        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect width="400" height="300" fill="#f8f9fa"/><text x="50%" y="50%" font-family="Arial, sans-serif" font-size="18" fill="#6c757d" text-anchor="middle" dominant-baseline="middle">Pré-visualização indisponível</text></svg>'
                    );

                    const createHiddenInput = (name, value = '') => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name;
                        input.value = value;
                        return input;
                    };

                    const getActiveItems = () => state.items.filter(item => !item.removed);

                    const updateOrderInputs = () => {
                        const activeItems = getActiveItems();
                        activeItems.forEach((item, index) => {
                            item.orderInput.value = index;
                            item.cardEl?.setAttribute('data-order', index);
                        });
                    };

                    const refreshPrimaryBadges = () => {
                        state.items.forEach((item) => {
                            const badge = item.cardEl?.querySelector('.badge-principal');
                            const primaryButton = item.cardEl?.querySelector('[data-action="set-primary"]');
                            const isPrimary = state.primaryUid === item.uid && !item.removed;

                            if (badge) {
                                badge.style.display = isPrimary ? 'inline-flex' : 'none';
                            }

                            if (primaryButton) {
                                primaryButton.classList.toggle('btn-primary', isPrimary);
                                primaryButton.classList.toggle('btn-outline-primary', !isPrimary);
                                primaryButton.innerHTML = isPrimary
                                    ? '<i class="mdi mdi-star me-1"></i> Principal'
                                    : '<i class="mdi mdi-star-outline me-1"></i> Definir como principal';
                            }

                            if (item.principalInput) {
                                item.principalInput.value = isPrimary ? '1' : '0';
                            }
                        });
                    };

                    const ensurePrimaryExists = () => {
                        const activeItems = getActiveItems();
                        if (!activeItems.length) {
                            state.primaryUid = null;
                            refreshPrimaryBadges();
                            return;
                        }

                        const currentPrimary = activeItems.find(item => item.uid === state.primaryUid);
                        if (currentPrimary) {
                            refreshPrimaryBadges();
                            return;
                        }

                        state.primaryUid = activeItems[0].uid;
                        refreshPrimaryBadges();
                    };

                    const setPrimary = (uid) => {
                        const item = state.items.find(entry => entry.uid === uid && !entry.removed);
                        if (!item) {
                            return;
                        }
                        state.primaryUid = uid;
                        refreshPrimaryBadges();
                    };

                    const revokePreviewUrl = (item) => {
                        if (item.previewUrl && item.type === 'new') {
                            URL.revokeObjectURL(item.previewUrl);
                        }
                    };

                    const removeItem = (uid) => {
                        const item = state.items.find(entry => entry.uid === uid);
                        if (!item || item.removed) {
                            return;
                        }

                        if (item.type === 'existing') {
                            if (item.removeInput) {
                                item.removeInput.value = '1';
                            }
                        } else if (item.type === 'new') {
                            item.fileInput?.remove();
                            item.orderInput?.remove();
                            item.principalInput?.remove();
                        }

                        revokePreviewUrl(item);

                        item.removed = true;
                        if (item.cardEl?.parentElement) {
                            item.cardEl.parentElement.remove();
                        }

                        ensurePrimaryExists();
                        updateOrderInputs();
                    };

                    const createCardElement = (item) => {
                        const col = document.createElement('div');
                        col.className = 'col-12 col-sm-6 col-md-4 col-xl-3';
                        col.dataset.uid = item.uid;

                        const card = document.createElement('div');
                        card.className = 'card shadow-sm product-image-card h-100 position-relative';

                        const badge = document.createElement('span');
                        badge.className = 'badge bg-primary badge-principal';
                        badge.textContent = 'Imagem principal';
                        badge.style.display = 'none';
                        card.appendChild(badge);

                        const img = document.createElement('img');
                        img.className = 'card-img-top';
                        img.alt = item.name || 'Imagem do produto';
                        img.src = item.previewUrl || placeholderImage;
                        card.appendChild(img);

                        const body = document.createElement('div');
                        body.className = 'card-body d-flex flex-column justify-content-between';

                        const info = document.createElement('div');
                        info.innerHTML = `
                            <h6 class="card-title text-truncate mb-2" title="${item.name || 'Imagem sem nome'}">
                                ${item.name || 'Imagem sem nome'}
                            </h6>
                            <p class="text-muted small mb-0">
                                ${item.type === 'existing' ? 'Imagem salva' : 'Imagem nova'}
                            </p>
                        `;
                        body.appendChild(info);

                        const actions = document.createElement('div');
                        actions.className = 'd-grid gap-2 mt-3';
                        actions.innerHTML = `
                            <button class="btn btn-outline-primary btn-sm" data-action="set-primary" type="button">
                                <i class="mdi mdi-star-outline me-1"></i> Definir como principal
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" data-action="handle-drag" type="button">
                                <i class="mdi mdi-arrow-all me-1"></i> Arrastar para reordenar
                            </button>
                            <button class="btn btn-outline-danger btn-sm" data-action="remove" type="button">
                                <i class="mdi mdi-trash-can-outline me-1"></i> Remover
                            </button>
                        `;
                        body.appendChild(actions);

                        card.appendChild(body);
                        col.appendChild(card);

                        const setPrimaryButton = actions.querySelector('[data-action="set-primary"]');
                        const removeButton = actions.querySelector('[data-action="remove"]');

                        setPrimaryButton.addEventListener('click', () => setPrimary(item.uid));
                        removeButton.addEventListener('click', () => removeItem(item.uid));

                        item.cardEl = card;

                        return col;
                    };

                    const addExistingImage = (data) => {
                        const uid = `existing-${data.id}`;
                        const orderInput = createHiddenInput(`existing_images[${data.id}][order]`, data.order ?? 0);
                        const principalInput = createHiddenInput(`existing_images[${data.id}][principal]`, data.principal ? '1' : '0');
                        const removeInput = createHiddenInput(`existing_images[${data.id}][remove]`, '0');

                        inputsContainer.append(orderInput, principalInput, removeInput);

                        const item = {
                            uid,
                            type: 'existing',
                            existingId: data.id,
                            orderInput,
                            principalInput,
                            removeInput,
                            previewUrl: data.url,
                            name: data.name,
                            removed: false,
                        };

                        state.items.push(item);

                        const card = createCardElement(item);
                        list.appendChild(card);

                        if (data.principal) {
                            state.primaryUid = uid;
                        }
                    };

                    const addNewImage = (file) => {
                        const uid = `new-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;

                        const fileInput = document.createElement('input');
                        fileInput.type = 'file';
                        fileInput.name = `new_images[${uid}]`;
                        fileInput.className = 'd-none';

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        fileInput.files = dataTransfer.files;

                        const orderInput = createHiddenInput(`new_images_meta[${uid}][order]`, '0');
                        const principalInput = createHiddenInput(`new_images_meta[${uid}][principal]`, '0');

                        inputsContainer.append(fileInput, orderInput, principalInput);

                        const previewUrl = URL.createObjectURL(file);

                        const item = {
                            uid,
                            type: 'new',
                            fileInput,
                            orderInput,
                            principalInput,
                            previewUrl,
                            name: file.name,
                            removed: false,
                        };

                        state.items.push(item);

                        const card = createCardElement(item);
                        list.appendChild(card);

                        if (!state.primaryUid) {
                            state.primaryUid = uid;
                        }
                    };

                    const handleFiles = (fileList) => {
                        const files = Array.from(fileList);
                        const availableSlots = maxImages - getActiveItems().length;

                        if (availableSlots <= 0) {
                            alert(`Você já atingiu o limite de ${maxImages} imagens.`);
                            return;
                        }

                        if (files.length > availableSlots) {
                            alert(`Apenas ${availableSlots} imagem(ns) adicional(is) podem ser enviada(s).`);
                        }

                        files.slice(0, availableSlots).forEach((file) => {
                            if (!file.type.startsWith('image/')) {
                                return;
                            }
                            addNewImage(file);
                        });

                        updateOrderInputs();
                        ensurePrimaryExists();
                    };

                    const handleDrop = (event) => {
                        event.preventDefault();
                        overlay.style.display = 'none';
                        dropzone.classList.remove('dragover');

                        if (event.dataTransfer?.files?.length) {
                            handleFiles(event.dataTransfer.files);
                        }
                    };

                    dropzone.addEventListener('dragover', (event) => {
                        event.preventDefault();
                        overlay.style.display = 'block';
                        dropzone.classList.add('dragover');
                    });

                    dropzone.addEventListener('dragleave', (event) => {
                        if (event.target === dropzone || event.target === overlay) {
                            overlay.style.display = 'none';
                            dropzone.classList.remove('dragover');
                        }
                    });

                    dropzone.addEventListener('drop', handleDrop);
                    dropzone.addEventListener('click', (event) => {
                        if (event.target.closest('[data-action="trigger-picker"]')) {
                            return;
                        }
                        picker.click();
                    });
                    triggerButton?.addEventListener('click', (event) => {
                        event.stopPropagation();
                        picker.click();
                    });
                    picker.addEventListener('change', (event) => {
                        handleFiles(event.target.files || []);
                        picker.value = '';
                    });

                    Sortable.create(list, {
                        animation: 150,
                        handle: '[data-action="handle-drag"]',
                        onEnd: () => {
                            const ordered = Array.from(list.children).map((col) => col.dataset.uid);
                            const position = (item) => {
                                const index = ordered.indexOf(item.uid);
                                return index === -1 ? Number.MAX_SAFE_INTEGER : index;
                            };
                            state.items.sort((a, b) => position(a) - position(b));
                            updateOrderInputs();
                        },
                        setData: () => {}
                    });

                    if (initialData.existing?.length) {
                        initialData.existing.forEach(addExistingImage);
                    }

                    ensurePrimaryExists();
                    updateOrderInputs();
                    refreshPrimaryBadges();
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initManager);
                } else {
                    initManager();
                }
            })();
        </script>
    @endonce
@endpush

