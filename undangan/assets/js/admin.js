document.addEventListener('DOMContentLoaded', () => {
    // Editor tab switching
    document.querySelectorAll('.editor-tabs .tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;
            document.querySelectorAll('.editor-tabs .tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            const panel = document.querySelector(`.tab-panel[data-tab="${target}"]`);
            if (panel) panel.classList.add('active');
            const hiddenTab = document.querySelector('input[name="tab"]');
            if (hiddenTab) hiddenTab.value = target;
        });
    });

    // Font preset — tampilkan field upload kustom
    const fontPreset = document.getElementById('font_preset');
    const customFontFields = document.getElementById('custom-font-fields');
    if (fontPreset && customFontFields) {
        const toggleFontFields = () => {
            customFontFields.style.display = fontPreset.value === 'custom' ? '' : 'none';
        };
        fontPreset.addEventListener('change', toggleFontFields);
        toggleFontFields();
    }

    // Pages editor (susunan halaman undangan)
    const pagesEditor = document.getElementById('pages-editor');
    const pagesAdd = document.getElementById('pages-add');

    function reindexPageRows() {
        if (!pagesEditor) return;
        pagesEditor.querySelectorAll('.page-row').forEach((row, idx) => {
            const num = row.querySelector('.page-order-num');
            if (num) num.textContent = String(idx + 1);
            row.querySelectorAll('[name^="pages["]').forEach((el) => {
                el.name = el.name.replace(/pages\[\d+\]/, `pages[${idx}]`);
            });
        });
    }

    function bindPageRow(row) {
        const up = row.querySelector('.page-up');
        const down = row.querySelector('.page-down');
        const remove = row.querySelector('.page-remove');
        up?.addEventListener('click', () => {
            const prev = row.previousElementSibling;
            if (prev) {
                pagesEditor.insertBefore(row, prev);
                reindexPageRows();
            }
        });
        down?.addEventListener('click', () => {
            const next = row.nextElementSibling;
            if (next) {
                pagesEditor.insertBefore(next, row);
                reindexPageRows();
            }
        });
        remove?.addEventListener('click', () => {
            row.remove();
            reindexPageRows();
        });
    }

    if (pagesEditor) {
        pagesEditor.querySelectorAll('.page-row').forEach(bindPageRow);
        pagesAdd?.addEventListener('click', () => {
            const idx = pagesEditor.querySelectorAll('.page-row').length;
            const key = 'custom_' + Math.random().toString(36).slice(2, 10);
            const row = document.createElement('div');
            row.className = 'page-row page-row-custom';
            row.dataset.type = 'custom';
            row.innerHTML = `
                <div class="page-row-tools">
                    <span class="page-order-num">${idx + 1}</span>
                    <button type="button" class="btn btn-sm btn-outline page-up" title="Naik">↑</button>
                    <button type="button" class="btn btn-sm btn-outline page-down" title="Turun">↓</button>
                </div>
                <label class="page-enabled checkbox-label">
                    <input type="checkbox" name="pages[${idx}][enabled]" value="1" checked> Aktif
                </label>
                <div class="page-fields">
                    <input type="hidden" name="pages[${idx}][key]" value="${key}" class="page-key-input">
                    <input type="hidden" name="pages[${idx}][type]" value="custom" class="page-type-input">
                    <span class="page-type-badge">Halaman Kustom</span>
                    <small class="page-hint">Isi judul & teks di bawah</small>
                    <input type="text" name="pages[${idx}][title]" placeholder="Judul halaman" class="page-title-input">
                    <textarea name="pages[${idx}][body]" rows="4" placeholder="Isi teks halaman tambahan..." class="page-body-input"></textarea>
                </div>
                <button type="button" class="btn btn-sm btn-danger page-remove" title="Hapus halaman">×</button>
            `;
            pagesEditor.appendChild(row);
            bindPageRow(row);
            reindexPageRows();
        });
    }

    // Schedule editor (susunan acara)
    const scheduleEditor = document.getElementById('schedule-editor');
    const scheduleAdd = document.getElementById('schedule-add');
    if (scheduleEditor && scheduleAdd) {
        scheduleAdd.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'schedule-row';
            row.innerHTML = `
                <input type="text" name="schedule_time[]" placeholder="08.00" class="schedule-time">
                <input type="text" name="schedule_title[]" placeholder="Nama acara" class="schedule-title">
                <input type="text" name="schedule_desc[]" placeholder="Keterangan (opsional)" class="schedule-desc">
                <button type="button" class="btn btn-sm btn-danger schedule-remove" title="Hapus baris">×</button>
            `;
            scheduleEditor.appendChild(row);
        });
        scheduleEditor.addEventListener('click', (e) => {
            if (e.target.classList.contains('schedule-remove')) {
                const rows = scheduleEditor.querySelectorAll('.schedule-row');
                if (rows.length > 1) e.target.closest('.schedule-row').remove();
            }
        });
    }

    // Theme preset picker
    const themePicker = document.getElementById('theme-picker');
    const customColors = document.getElementById('custom-colors');
    if (themePicker) {
        const updateThemeSelection = () => {
            const selected = themePicker.querySelector('input[name="theme_preset"]:checked');
            if (!selected) return;
            themePicker.querySelectorAll('.theme-card').forEach(c => c.classList.remove('selected'));
            const card = selected.closest('.theme-card');
            if (card) card.classList.add('selected');
            const isCustom = selected.value === 'custom';
            if (customColors) customColors.style.display = isCustom ? '' : 'none';
            if (!isCustom && card) {
                const primary = document.getElementById('color_primary');
                const accent = document.getElementById('color_accent');
                if (primary) primary.value = card.dataset.primary;
                if (accent) accent.value = card.dataset.accent;
                document.querySelectorAll('.color-input').forEach(group => {
                    const picker = group.querySelector('input[type="color"]');
                    const hex = group.querySelector('.color-hex');
                    if (picker && hex) hex.value = picker.value;
                });
            }
        };
        themePicker.querySelectorAll('input[name="theme_preset"]').forEach(radio => {
            radio.addEventListener('change', updateThemeSelection);
        });
        themePicker.querySelectorAll('.theme-card').forEach(card => {
            card.addEventListener('click', () => {
                const radio = card.querySelector('input[type="radio"]');
                if (radio) { radio.checked = true; updateThemeSelection(); }
            });
        });
    }

    // Sync color picker with hex display
    document.querySelectorAll('.color-input').forEach(group => {
        const picker = group.querySelector('input[type="color"]');
        const hex = group.querySelector('.color-hex');
        if (picker && hex) {
            picker.addEventListener('input', () => { hex.value = picker.value; });
        }
    });

    // Toggle audio URL field
    const audioMode = document.getElementById('audio_mode');
    const audioUrlField = document.getElementById('audio_url_field');
    if (audioMode && audioUrlField) {
        audioMode.addEventListener('change', () => {
            audioUrlField.style.display = audioMode.value === 'url' ? '' : 'none';
        });
    }

    // Auto slug from title on create page
    const titleInput = document.querySelector('input[name="title"]');
    const slugInput = document.querySelector('input[name="slug"]');
    if (titleInput && slugInput && !slugInput.value) {
        titleInput.addEventListener('blur', () => {
            if (!slugInput.value && titleInput.value) {
                slugInput.value = titleInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/[\s-]+/g, '-')
                    .replace(/^-|-$/g, '');
            }
        });
    }
});
