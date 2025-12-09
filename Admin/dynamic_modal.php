<!-- SHARED ADD/VIEW/EDIT MODAL -->
<div id="sharedModalBackdrop" class="modal-backdrop hidden" onclick="closeSharedModal()">></div>
<div id="sharedModal" class="modal hidden" role="dialog" aria-modal="true">
    <form id="sharedForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" id="shared_action" value="">
        <input type="hidden" name="<?= $pk ?>" id="shared_id" value="">

        <div style="margin:10px;" class="flex-c center">
            <h2 id="sharedTitle">Modal</h2>
        </div>

        <div id="sharedFieldsContainer" class="modal-fields"></div>

        <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
            <button type="button" class="btn btn-secondary" onclick="closeSharedModal()">Cancel</button>
            <button type="submit" class="btn btn-primary" id="sharedConfirmBtn">Confirm</button>
        </div>
    </form>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div id="deleteModalBackdrop" class="modal-backdrop hidden" onclick="closeDeleteModal()">
    <div class="modal flex-c center">
        <h3>Confirm Delete</h3>
        <p id="deleteModalText">Are you sure you want to delete this record?</p>
        <form id="deleteForm" method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="<?= $pk ?>" id="delete_id">
            <button type="submit" class="btn btn-danger">Yes, Delete</button>
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
        </form>
    </div>
</div>

<!-- GENERAL MESSAGE MODAL -->
<div id="messageModalBackdrop" class="modal-backdrop hidden" onclick="closeMessageModal()">
    <div class="modal">
        <p id="messageModalText"></p>
        <button type="button" class="btn btn-primary" onclick="closeMessageModal()">OK</button>
    </div>
</div>

<!-- SET PROPERTY MODAL -->
<div id="setPropertyBackdrop" class="modal-backdrop hidden" onclick="closeSetPropertyModal()">></div>
<div id="setPropertyModal" class="modal hidden" role="dialog" aria-modal="true">
  <form id="setPropertyForm" method="POST">
    <input type="hidden" name="action" value="set_property">
    <input type="hidden" name="<?= $pk ?>" id="setProperty_id">
    <input type="hidden" name="property" value="<?= $setPropertyConfig['property'] ?>">
    <input type="hidden" name="value" id="setProperty_value">

    <div style="margin:10px;" class="flex-c center">
      <h2><?= $setPropertyConfig['modal_title'] ?></h2>
    </div>

    <!-- Report details -->
    <div id="setPropertyDetails" class="modal-fields"></div>

    <!-- Action buttons -->
    <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;" id="setPropertyButtons"></div>
  </form>
</div>

<!-- TABLE IMAGE PREVIEW MODAL -->
<div id="imgPreviewBackdrop" onclick="closeImagePreview()">
    <img id="imgPreview">
</div>

<script>
// Safe defaults from PHP
const foreignKeys = <?= json_encode($foreignKeys ?? []) ?> || {};
const fieldLabels = <?= json_encode($fieldLabels ?? []) ?> || {};
const fieldsConfig = <?= json_encode($fieldsConfig ?? []) ?> || {};
const pk = "<?= $pk ?>";
const tableName = "<?= $tableName ?>";
const fkCache = <?= json_encode($fkCache ?? []) ?> || {};


function openSharedModal(mode, data = null) {
    const backdrop = document.getElementById('sharedModalBackdrop');
    const modal = document.getElementById('sharedModal');
    const title = document.getElementById('sharedTitle');
    const form = document.getElementById('sharedForm');
    const actionInput = document.getElementById('shared_action');
    const idInput = document.getElementById('shared_id');
    const container = document.getElementById('sharedFieldsContainer');

    backdrop.classList.remove('hidden');
    modal.classList.remove('hidden');
    container.innerHTML = '';

    idInput.value = data ? data[pk] : '';
    actionInput.value = mode === 'add' ? 'add' : (mode === 'edit' ? 'update' : '');
    title.textContent = mode.charAt(0).toUpperCase() + mode.slice(1) + " " + tableName;

    function formatLabel(str) {
        return str.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    }

    // Collect non-image fields separately
    const nonImageFields = [];
    let imagePreviewHTML = '';

    for (const [field, type] of Object.entries(fieldsConfig)) {
        let labelText = fieldLabels[field];
        let value = data ? data[field] || '' : '';

        if (type === 'image') {
            // Render image preview at top
            imagePreviewHTML = `
                <div class="flex-c center" style="margin-bottom:12px;">
                    ${value ? `<img src="../Assets/UserGenerated/${value}" style="max-width:200px;max-height:200px;border-radius:8px;">` : '<span><hr></span>'}
                </div>
                <input type="hidden" name="${field}" value="${value}">
                <div class='col'>
                <label>${labelText}</label>
                <input type="file" name="${field}_file" id="file_${field}" accept="image/*">
                </div>
            `;

        } else {
            // Save non-image fields for later layout
            nonImageFields.push({field, type, labelText, value});
        }
    }

    // Insert image preview at top if exists
    if (imagePreviewHTML) {
        container.innerHTML += imagePreviewHTML;
    }

    // Render non-image fields in two-column layout
    for (let i = 0; i < nonImageFields.length; i += 2) {
        const row = document.createElement('div');
        row.className = 'row';

        // First column
        const f1 = nonImageFields[i];
        let col1 = `<div class="col"><label>${f1.labelText}</label>`;
        col1 += renderFieldHTML(f1, mode);
        col1 += `</div>`;
        row.innerHTML += col1;

        // Second column (if exists)
        if (i + 1 < nonImageFields.length) {
            const f2 = nonImageFields[i+1];
            let col2 = `<div class="col"><label>${f2.labelText}</label>`;
            col2 += renderFieldHTML(f2, mode);
            col2 += `</div>`;
            row.innerHTML += col2;
        } else if (mode === 'add') {
            // Last field spans full width
            row.innerHTML = `<div class="col" style="flex:2;"><label>${f1.labelText}</label>${renderFieldHTML(f1, mode)}</div>`;
        }

        container.appendChild(row);
    }

    if (mode === 'view') {
        form.querySelectorAll('input, select, textarea').forEach(el => el.setAttribute('disabled', 'disabled'));
        document.getElementById('sharedConfirmBtn').textContent = 'Close';
        form.onsubmit = e => { e.preventDefault(); closeSharedModal(); };
    } else {
        form.querySelectorAll('input, select, textarea').forEach(el => el.removeAttribute('disabled'));
        document.getElementById('sharedConfirmBtn').textContent = mode==='add'?'Add':'Save';
        form.onsubmit = null;
    }
}

function renderFieldHTML(f, mode) {
    // Foreign key dropdown
    if (foreignKeys && foreignKeys[f.field]) {
        const fk = foreignKeys[f.field];
        let html = `<select name="${f.field}" id="field_${f.field}">`;
        html += `<option value="">-- Select --</option>`;
        for (const rec of (fk.records || [])) {
            const val = rec[fk.key];
            const label = rec[fk.label];
            const selected = (f.value == val) ? 'selected' : '';
            html += `<option value="${val}" ${selected}>${label}</option>`;
        }
        html += `</select>`;
        return html;
    }

    // Regular fields
    if (Array.isArray(f.type)) {
        let html = `<select name="${f.field}" id="field_${f.field}">`;
        for (const opt of f.type) {
            const selected = (f.value === opt) ? 'selected' : '';
            html += `<option value="${opt}" ${selected}>${opt}</option>`;
        }
        html += `</select>`;
        return html;
    } else if (f.type === 'textarea') {
        return `<textarea name="${f.field}" id="field_${f.field}" rows="3">${f.value}</textarea>`;
    } else if (['number','text','date','time'].includes(f.type)) {
        return `<input type="${f.type}" name="${f.field}" id="field_${f.field}" value="${f.value}">`;
    } else if (f.type === 'id') {
        return `<input type="number" name="${f.field}" id="field_${f.field}" value="${f.value}" readonly>`;
    }
    return '';
}



function closeSharedModal() {
    document.getElementById('sharedModalBackdrop').classList.add('hidden');
    document.getElementById('sharedModal').classList.add('hidden');
}

function toggleColumnSelector() {
    let el = document.getElementById('column-selector');
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

function applyColumnSelection() {
    let checked = [];
    document.querySelectorAll('.column-toggle:checked').forEach(cb => checked.push(cb.value));
    
    let form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    let input = document.createElement('input');
    input.name = 'visible_columns';
    input.value = JSON.stringify(checked);
    form.appendChild(input);
    
    let actionInput = document.createElement('input');
    actionInput.name = 'action';
    actionInput.value = 'set_columns';
    form.appendChild(actionInput);
    
    document.body.appendChild(form);
    form.submit();
}

// DELETE MODAL
function openDeleteModal(id, label='') {
    document.getElementById('delete_id').value = id;
    document.getElementById('deleteModalText').textContent = 
        `Are you sure you want to delete this record?`;
    document.getElementById('deleteModalBackdrop').classList.remove('hidden');
}
function closeDeleteModal() {
    document.getElementById('deleteModalBackdrop').classList.add('hidden');
}

// GENERAL MESSAGE MODAL
function showMessage(msg) {
    document.getElementById('messageModalText').textContent = msg;
    document.getElementById('messageModalBackdrop').classList.remove('hidden');
}
function closeMessageModal() {
    document.getElementById('messageModalBackdrop').classList.add('hidden');
}

function openImagePreview(src) {
    const backdrop = document.getElementById("imgPreviewBackdrop");
    const img = document.getElementById("imgPreview");
    img.src = src;
    backdrop.style.display = "flex";
}

function closeImagePreview() {
    document.getElementById("imgPreviewBackdrop").style.display = "none";
}



</script>


<script>

function openSetPropertyModal(data) {
    const setPropertyConfig = <?= json_encode($setPropertyConfig) ?>;
    const fieldsConfig      = <?= json_encode($fieldsConfig) ?>;
    const fieldLabels       = <?= json_encode($fieldLabels) ?>;
    const pk                = "<?= $pk ?>";
    const fkCache = <?= json_encode($fkCache ?? []) ?> || {};


    const backdrop        = document.getElementById('setPropertyBackdrop');
    const modal           = document.getElementById('setPropertyModal');
    const idInput         = document.getElementById('setProperty_id');
    const valueInput      = document.getElementById('setProperty_value');
    const buttonsContainer= document.getElementById('setPropertyButtons');
    const detailsContainer= document.getElementById('setPropertyDetails');

    backdrop.classList.remove('hidden');
    modal.classList.remove('hidden');
    idInput.value = data[pk];
    buttonsContainer.innerHTML = '';
    detailsContainer.innerHTML = '';

    // Collect non-image fields separately
    const nonImageFields = [];
    let imagePreviewHTML = '';

    for (const [field, type] of Object.entries(fieldsConfig)) {
        let labelText = fieldLabels[field];
        let value     = data[field] ?? '';

        if (type === 'image') {
            // Render image preview at top
            imagePreviewHTML = `
                <div class="flex-c center" style="margin-bottom:12px;">
                    ${value ? `<img src="../Assets/UserGenerated/${value}" style="max-width:200px;max-height:200px;border-radius:8px;">` : '<span><hr></span>'}
                </div>
                <input type="hidden" name="${field}" value="${value}">
            `;

        } else {
            nonImageFields.push({field, type, labelText, value});
        }
    }

    // Insert image preview at top if exists
    if (imagePreviewHTML) {
        detailsContainer.innerHTML += imagePreviewHTML;
    }

    // Render non-image fields in two-column layout
    for (let i = 0; i < nonImageFields.length; i += 2) {
        const row = document.createElement('div');
        row.className = 'row';

        // First column
        const f1 = nonImageFields[i];
        let col1 = `<div class="col"><label>${f1.labelText}</label>`;
        col1 += renderReadOnlyFieldHTML(f1, data);
        col1 += `</div>`;
        row.innerHTML += col1;

        // Second column (if exists)
        if (i + 1 < nonImageFields.length) {
            const f2 = nonImageFields[i+1];
            let col2 = `<div class="col"><label>${f2.labelText}</label>`;
            col2 += renderReadOnlyFieldHTML(f2);
            col2 += `</div>`;
            row.innerHTML += col2;
        } else {
            // Last field spans full width
            row.innerHTML = `<div class="col" style="flex:2;"><label>${f1.labelText}</label>${renderReadOnlyFieldHTML(f1)}</div>`;
        }

        detailsContainer.appendChild(row);
    }

    // Render action buttons from config
    setPropertyConfig.values.forEach((val, idx) => {
        const btn = document.createElement('button');
        btn.type  = 'submit';
        btn.className = setPropertyConfig.button_class[idx] || 'btn btn-primary';
        btn.textContent = setPropertyConfig.button_text[idx];
        btn.onclick = () => { valueInput.value = val; };
        buttonsContainer.appendChild(btn);
    });

    // Add a separate Close button
    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'btn btn-secondary';
    closeBtn.textContent = 'Close';
    closeBtn.onclick = closeSetPropertyModal;
    buttonsContainer.appendChild(closeBtn);
}

function renderReadOnlyFieldHTML(f, data) {
    // Foreign key label
    if (foreignKeys && foreignKeys[f.field]) {
        const fkLabel = fkCache[f.field] && fkCache[f.field][f.value] 
            ? fkCache[f.field][f.value] 
            : f.value;

        // Hidden input posts the raw ID, visible input shows the label
        return `
            <input type="hidden" name="${f.field}" value="${f.value}">
            <input type="text" value="${fkLabel}" readonly class="value-preview">
        `;
    }

    if (Array.isArray(f.type)) {
        return `<input type="text" name="${f.field}" value="${f.value}" readonly class="value-preview">`;
    } else if (f.type === 'textarea') {
        return `<textarea name="${f.field}" rows="3" readonly>${f.value}</textarea class="value-preview">`;
    } else {
        return `<input type="text" name="${f.field}" value="${f.value}" readonly class="value-preview">`;
    }
}



function closeSetPropertyModal() {
    document.getElementById('setPropertyBackdrop').classList.add('hidden');
    document.getElementById('setPropertyModal').classList.add('hidden');
}
</script>
