<?php
if ($totalMigrations > 0) {
    echo $result != '' ? $result : 'Migration apply successfully!';
} else {
    echo 'No new migrations found';
}

