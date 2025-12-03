<?php
// includes/render_table.php
// Expects: $tableData, $visibleColumns, $fieldsConfig, $fieldLabels, $pk
// Optional: $foreignKeys, $fkCache

foreach ($fieldsConfig as $f => $t) {
    if (empty($fieldLabels[$f])) {
        $fieldLabels[$f] = ucwords(str_replace('_',' ',$f));
    }
}
?>

<div class="result-table">
    <table class="rounded-border">
        <thead>
            <tr>
                <?php foreach($visibleColumns as $f): ?>
                    <?php 
                        // If this column is a foreign key, show its label
                        if (isset($foreignKeys[$f])) {
                            $label = $foreignKeys[$f]['label'];
                            echo "<th>" . ucwords(str_replace('_',' ',$label)) . "</th>";
                        } else {
                            echo "<th>" . e($fieldLabels[$f]) . "</th>";
                        }
                    ?>
                <?php endforeach; ?>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php if(empty($tableData)): ?>
                <tr><td colspan="<?= count($visibleColumns) + 1 ?>">No records found.</td></tr>
            <?php endif; ?>

            <?php foreach($tableData as $row): ?>
                <tr>
                    <?php foreach($visibleColumns as $f): ?>
                        <?php if ($fieldsConfig[$f] === 'image'): ?>
                            <?php
                                $imgFile = $row[$f] ?? '';
                                $imgPath = "../Assets/UserGenerated/" . $imgFile;
                                $exists = !empty($imgFile) && file_exists(__DIR__ . "/../Assets/UserGenerated/" . $imgFile);
                            ?>
                            <td>
                                <?php if ($exists): ?>
                                    <img src="<?= $imgPath ?>" class="thumb-img" onclick="openImagePreview('<?= $imgPath ?>')">
                                <?php else: ?>
                                    <span>No image</span>
                                <?php endif; ?>
                            </td>

                        <?php elseif ($f === 'status'): ?>
                            <?php
                                $val     = $row[$f] ?? '';
                                $options = $fieldsConfig['status'];
                                $index   = array_search($val, $options);
                                $class   = ($index !== false) ? "status-color-$index" : "status-color-unknown";
                            ?>
                            <td><span class="<?= $class ?>"><?= e($val) ?></span></td>

                        <?php elseif (isset($foreignKeys[$f])): ?>
                            <!-- Show the cached FK label instead of raw ID -->
                            <td><?= e($fkCache[$f][$row[$f]] ?? "Unknown") ?></td>

                        <?php else: ?>
                            <td><?= e($row[$f] ?? '') ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <td>
                        <?php $json = htmlspecialchars(json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8'); ?>

                        <?php if(!isset($actionType)): ?>
                            <button onclick='openSharedModal("edit", <?= $json ?>)' style='width:60px;' class='btn btn-primary action-btn'>Edit</button>
                            <button onclick='openDeleteModal(<?= e($row[$pk]) ?>,"<?= e($row[$pk]) ?>")' style='width:60px;' class='btn btn-secondary action-btn'>Delete</button>
                        <?php elseif(isset($actionType) && $actionType === 'setProperty'): ?>
                            <button onclick="openSetPropertyModal( <?= $json ?>)" class='btn btn-info action-btn'>Options</button>
                            
                            <?php if($tableName === 'adoption_application'): ?>
                                <a href="adoptions-screening.php?application_id=<?=$row['a_application_id']?>"><button class='btn btn-info action-btn'>Show Screening</button></a>
                            <?php endif; ?>
                            
                        <?php endif; ?>

                    

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
