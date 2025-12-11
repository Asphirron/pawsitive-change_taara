<?php
// admin_controls.php
// Assumes $tableName, $fieldsConfig, $filterConfig, $visibleColumns, $suggestions, $filterSuggestions are defined

//SEARCH SUGGESTIONS
$conn = connect();

// --- SEARCH SUGGESTIONS ---
$suggestions = [];
if (in_array($searchBy, array_keys($fieldsConfig))) {
    $sql = "SELECT DISTINCT `$searchBy` FROM `$tableName` 
            WHERE `$searchBy` IS NOT NULL AND `$searchBy` <> '' 
            ORDER BY `$searchBy` LIMIT 10";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $suggestions[] = $row[$searchBy];
    }
}

// Include previously searched value
if (!empty($_POST['search_bar']) && !in_array($_POST['search_bar'], $suggestions)) {
    array_unshift($suggestions, $_POST['search_bar']);
}

// --- FILTER SUGGESTIONS ---
$filterSuggestions = [];
foreach ($filterConfig as $f) {
    if (isset($fieldsConfig[$f]) && !is_array($fieldsConfig[$f])) {
        $sql = "SELECT DISTINCT `$f` FROM `$tableName` 
                WHERE `$f` IS NOT NULL AND `$f` <> '' 
                ORDER BY `$f` LIMIT 10";
        $res = $conn->query($sql);
        $filterSuggestions[$f] = [];
        while ($row = $res->fetch_assoc()) {
            $filterSuggestions[$f][] = $row[$f];
        }
    }
}

$conn->close();

// Detect if filters/controls were used
$filtersUsed = false;
foreach ($filterConfig as $f) {
    if (!empty($_POST[$f])) {
        $filtersUsed = true;
        break;
    }
}
if (!empty($_POST['order_by']) || !empty($_POST['num_of_results']) || isset($_POST['visible_columns'])) {
    $filtersUsed = true;
}
?>

<div class="flex-r" style="width: 100%; "> 
    
    <?php if($tableName === 'donation_inventory'): ?>
        <h2 style="margin-left: 10px;">Inventory Table</h2>
    <?php elseif($tableName === 'donation_post'): ?>
        <h2 style="margin-left: 10px;">Donation Allocations Table</h2>
    <?php else: ?>
        <h2 style="margin-left: 10px;"><?= ucwords(str_replace('_',' ',$tableName)) ?> Table</h2>
    <?php endif; ?>

    <div class="flex-rc-gap" style="margin-left: auto; margin-block: auto;">

    <!-- ACTION BUTTON -->
    <?php if($tableName === 'donation_inventory'): ?>
        <button class="main-btn btn btn-primary" onclick="openSharedModal('add')">+ Add Item </button>
    <?php elseif($tableName === 'donation_post'): ?>
        <button class="main-btn btn btn-primary" onclick="openSharedModal('add')">+ Add Donation Allocation </button>
    <?php else: ?>
        <button class="main-btn btn btn-primary" onclick="openSharedModal('add')">+ Add <?= ucwords(str_replace('_',' ',$tableName)) ?></button>
    <?php endif; ?>

    <a href="../export/export_pdf.php?table=<?=$tableName?>" target="_blank"><button type='button' class="main-btn btn btn-success">Export as PDF</button></a>

    </div>

</div>

<?php if($tableName === 'donation_inventory'): ?>

<?php
$totalItemsTable = $crud->readAll();
$totalItems = 0;
$kinds = count($totalItemsTable) ?? 0;
$lowStock = 0;
$noStock = 0;


foreach($totalItemsTable as $t){
    if($t['quantity'] > 0){
        $totalItems += $t['quantity'];

        if($t['quantity'] <= 10){
            $lowStock++;
        }
    }else{
        $noStock++;
    }

}




?>

<div class="flex-r c-gap1" style="width: 100%;">
    <div class='stat-preview-container'>
        <small>Total Items</small>
        <h4><b> <?= $totalItems ?> </b></h4>
        <small style="color: gray;"> <?= $kinds ?> kinds</small>
    </div>
    <div class='stat-preview-container'>
        <small>Low Stock</small>
        <h4><b> <?= $lowStock ?> </b></h4>
        <small style="color: gray;"><= 10 threshold</small>
    </div>
        <div class='stat-preview-container'>
        <small>In Stock</small>
        <h4><b> <?= ($totalItems - $noStock) ?> </b></h4>
        <small style="color: gray;">Quantity > 10</small>
    </div>
    <div class='stat-preview-container'>
        <small>Out of Stock</small>
        <h4><b> <?= $noStock ?> </b></h4>
        <small style="color: gray;">Quantity = 0</small>
    </div>
</div>

<?php endif; ?>

<form method="POST" enctype="multipart/form-data" style="margin-bottom:12px;">

    <!-- SEARCH GROUP (always visible) -->
    <div class="search-group">
        <input type="text" 
            name="search_bar" 
            class="search-bar" 
            placeholder="Search..." 
            list="search-suggestions"
            value="<?= e($_POST['search_bar'] ?? '') ?>">

        <datalist id="search-suggestions">
            <?php foreach ($suggestions as $s): ?>
                <option value="<?= e($s) ?>">
            <?php endforeach; ?>
        </datalist>

        <button type="submit" name="search_btn" class="search-btn btn btn-primary">Search</button>
    </div>

    <br>

    <!-- TOGGLE BUTTON -->
    <button type="button" class="btn btn-success" onclick="toggleFilters()">
        Hide Filters
    </button>

    <!-- TOGGLEABLE CONTAINER -->
    <fieldset id="filter-container" style="margin-top:10px; max-width: 100%">

        <legend>Filters</legend>

        <!-- OTHER CONTROLS -->
        <select name="order_by" class="filter-field">
            <option value="descending" <?= (isset($_POST['order_by']) && $_POST['order_by']==='descending')?'selected':'' ?>>Descending</option>
            <option value="ascending" <?= (isset($_POST['order_by']) && $_POST['order_by']==='ascending')?'selected':'' ?>>Ascending</option>
        </select>

        <input type="number" class="filter-field" name="num_of_results" min="1" max="1000" value="<?= intval($_POST['num_of_results']??10) ?>" title='Results shown'>

        <button type="button" class="btn btn-secondary filter-field" onclick="toggleColumnSelector()">Show Columns</button>

        <div id="column-selector" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; padding:10px; top: 10%; left: 50%;">
            <?php foreach($fieldsConfig as $f=>$t): ?>
                <label>
                    <input type="checkbox" class="column-toggle" value="<?= $f ?>" 
                        <?= in_array($f,$visibleColumns)?'checked':'' ?>> <?= ucwords(str_replace('_',' ',$f)) ?>
                </label><br>
            <?php endforeach; ?>
            <button type="button" onclick="applyColumnSelection()">Apply</button>
        </div>

        <button type="submit" name="reset_btn" class="btn btn-secondary filter-field">Reset</button>

        <hr>

        <!-- FILTERS -->
        <div class="filter-group flex-r wrap">
            <?php foreach ($filterConfig as $f): ?>
                <?php if (isset($fieldsConfig[$f])): ?>
                    <?php if (is_array($fieldsConfig[$f])): ?>
                        <select class="filter-field auto-filter" name="<?= $f ?>">
                            <option class="filter-field" value=""><?= "Any " . ucwords(str_replace('_',' ',$f)) ?></option>
                            <?php foreach ($fieldsConfig[$f] as $opt): ?>
                                <option value="<?= $opt ?>" <?= (($_POST[$f] ?? '')===$opt)?'selected':'' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input 
                            class="filter-field auto-filter"
                            type="<?= $fieldsConfig[$f] === 'number' ? 'number' : ($fieldsConfig[$f] === 'date' ? 'date' : 'text') ?>" 
                            name="<?= $f ?>" 
                            value="<?= e($_POST[$f] ?? '') ?>" 
                            placeholder="<?= 'Any ' . ucwords(str_replace('_',' ',$f)) ?>" 
                            list="filter-suggestions-<?= $f ?>">

                        <?php if (!empty($filterSuggestions[$f])): ?>
                            <datalist id="filter-suggestions-<?= $f ?>">
                                <?php foreach ($filterSuggestions[$f] as $s): ?>
                                    <option value="<?= e($s) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </fieldset>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".auto-filter, select[name='order_by'], input[name='num_of_results']").forEach(function(el) {
        // Skip the search bar — only filters/controls auto-apply
        if (el.name === "search_bar") return;

        if (el.tagName.toLowerCase() === "select") {
            el.addEventListener("change", function() {
                el.form.submit();
            });
        } else {
            el.addEventListener("input", function() {
                clearTimeout(el._debounceTimer);
                el._debounceTimer = setTimeout(function() {
                    el.form.submit();
                }, 700); // debounce typing for filters/controls
            });
        }
    });

    // Column selector auto-apply
    window.applyColumnSelection = function() {
        const selected = [];
        document.querySelectorAll("#column-selector .column-toggle:checked").forEach(cb => selected.push(cb.value));
        const form = document.querySelector("form");
        const hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.name = "action";
        hidden.value = "set_columns";
        form.appendChild(hidden);

        const hiddenCols = document.createElement("input");
        hiddenCols.type = "hidden";
        hiddenCols.name = "visible_columns";
        hiddenCols.value = JSON.stringify(selected);
        form.appendChild(hiddenCols);

        form.submit();
    };
});

function toggleFilters() {
    const container = document.getElementById("filter-container");
    const btn = document.querySelector("button[onclick='toggleFilters()']");
    if (container.style.display === "none") {
        container.style.display = "block";
        btn.textContent = "Hide Filters";
    } else {
        container.style.display = "none";
        btn.textContent = "Show Filters";
    }
}
</script>

<!-- DYNAMIC MODAL -->
<?php 
include 'dynamic_modal.php'; 
//Includes Add/Edit/View, Delete, Message, and ImagePreview Modals
?> 
