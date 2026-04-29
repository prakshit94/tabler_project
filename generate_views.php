<?php

$baseDir = __DIR__ . '/resources/views/erp';
$sourceDir = $baseDir . '/brands';

$modules = [
    [
        'dir' => 'crops',
        'route' => 'crops',
        'PluralTitle' => 'Crops',
        'SingularTitle' => 'Crop',
        'plural_var' => 'crops',
        'singular_var' => 'crop'
    ],
    [
        'dir' => 'irrigation_types',
        'route' => 'irrigation-types',
        'PluralTitle' => 'Irrigation Types',
        'SingularTitle' => 'Irrigation Type',
        'plural_var' => 'irrigation_types',
        'singular_var' => 'irrigation_type'
    ],
    [
        'dir' => 'land_units',
        'route' => 'land-units',
        'PluralTitle' => 'Land Units',
        'SingularTitle' => 'Land Unit',
        'plural_var' => 'land_units',
        'singular_var' => 'land_unit'
    ],
    [
        'dir' => 'account_types',
        'route' => 'account-types',
        'PluralTitle' => 'Account Types',
        'SingularTitle' => 'Account Type',
        'plural_var' => 'account_types',
        'singular_var' => 'account_type'
    ]
];

$files = ['index.blade.php', '_table.blade.php'];

foreach ($modules as $mod) {
    $targetDir = $baseDir . '/' . $mod['dir'];
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    foreach ($files as $file) {
        $content = file_get_contents($sourceDir . '/' . $file);
        
        // Replacements
        $content = str_replace('Brand Management', $mod['PluralTitle'] . ' Management', $content);
        $content = str_replace('Create new brand', 'Create new ' . strtolower($mod['SingularTitle']), $content);
        $content = str_replace('Active Brands', 'Active ' . $mod['PluralTitle'], $content);
        $content = str_replace('Search brands', 'Search ' . strtolower($mod['PluralTitle']), $content);
        $content = str_replace('brands.', $mod['route'] . '.', $content);
        $content = str_replace("['brands'", "['" . $mod['plural_var'] . "'", $content);
        $content = str_replace('$brands', '$' . $mod['plural_var'], $content);
        $content = str_replace('$brand', '$' . $mod['singular_var'], $content);
        $content = str_replace('brand-checkbox', $mod['singular_var'] . '-checkbox', $content);
        $content = str_replace('New Brand', 'New ' . $mod['SingularTitle'], $content);
        $content = str_replace('Edit Brand', 'Edit ' . $mod['SingularTitle'], $content);
        $content = str_replace('Brand Name', $mod['SingularTitle'] . ' Name', $content);
        $content = str_replace('Create Brand', 'Create ' . $mod['SingularTitle'], $content);
        $content = str_replace('Update Brand', 'Update ' . $mod['SingularTitle'], $content);
        $content = str_replace('modal-create-brand', 'modal-create-' . $mod['singular_var'], $content);
        $content = str_replace('modal-edit-brand', 'modal-edit-' . $mod['singular_var'], $content);
        
        file_put_contents($targetDir . '/' . $file, $content);
    }
}

echo "Views generated successfully.\n";
