<?php
/**
 * OpenCart Thumbnail Regeneration Script
 *
 * This script regenerates missing thumbnail images for all products.
 * It can be run with specific dimensions or will default to 47x47.
 *
 * Usage:
 *   php regenerate_thumbnails.php [width] [height]
 *
 * Example:
 *   php regenerate_thumbnails.php 47 47
 *   php regenerate_thumbnails.php 250 250
 *
 * To run via Docker:
 *   docker exec php php /opt/lotsalert/opencart/upload/regenerate_thumbnails.php
 */

// Get dimensions from command line arguments or use defaults
$target_width = isset($argv[1]) ? (int)$argv[1] : 47;
$target_height = isset($argv[2]) ? (int)$argv[2] : 47;

echo "OpenCart Thumbnail Regeneration Script\n";
echo "======================================\n";
echo "Target size: {$target_width}x{$target_height}\n\n";

// Load OpenCart configuration
require_once('config.php');

// Define missing constants
define('DIR_CATALOG', DIR_APPLICATION);

// Bootstrap essentials only
require_once(DIR_SYSTEM . 'startup.php');

// Autoloader
$autoloader = new \Opencart\System\Engine\Autoloader();
$autoloader->register('Opencart\Catalog', DIR_APPLICATION);
$autoloader->register('Opencart\Extension', DIR_EXTENSION);
$autoloader->register('Opencart\System', DIR_SYSTEM);

// Registry
$registry = new \Opencart\System\Engine\Registry();

// Config
$config = new \Opencart\System\Engine\Config();
$config->set('config_url', HTTP_SERVER);
$registry->set('config', $config);

// Database
$db = new \Opencart\System\Library\DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
$registry->set('db', $db);

// Settings from database
$query = $db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '0'");
foreach ($query->rows as $result) {
    if (!$result['serialized']) {
        $config->set($result['key'], $result['value']);
    } else {
        $config->set($result['key'], json_decode($result['value'], true));
    }
}

// Load image model directly
require_once(DIR_CATALOG . 'model/tool/image.php');
$image_model = new \Opencart\Catalog\Model\Tool\Image($registry);

// Get all products with images
$query = $db->query("SELECT product_id, image FROM `" . DB_PREFIX . "product` WHERE image IS NOT NULL AND image != '' ORDER BY product_id");

$total = count($query->rows);
$success = 0;
$failed = 0;
$skipped = 0;
$missing = 0;

echo "Found {$total} products with primary images.\n";
echo "Generating {$target_width}x{$target_height} thumbnails...\n\n";

$start_time = time();

foreach ($query->rows as $row) {
    $image_path = $row['image'];
    $product_id = $row['product_id'];

    // Check if thumbnail already exists
    $cache_path = 'cache/' . substr($image_path, 0, strrpos($image_path, '.')) . '-' . $target_width . 'x' . $target_height . '.' . pathinfo($image_path, PATHINFO_EXTENSION);
    $full_path = DIR_IMAGE . $cache_path;

    if (file_exists($full_path)) {
        // Already exists, skip silently
        $skipped++;
        continue;
    }

    $missing++;
    echo "Product {$product_id}: {$image_path} ... ";

    // Generate thumbnail
    try {
        $thumbnail = $image_model->resize($image_path, $target_width, $target_height);

        if (file_exists($full_path)) {
            echo "✓ SUCCESS\n";
            $success++;
        } else {
            echo "✗ FAILED (file not created)\n";
            $failed++;
        }
    } catch (Exception $e) {
        echo "✗ ERROR: " . $e->getMessage() . "\n";
        $failed++;
    } catch (Error $e) {
        echo "✗ ERROR: " . $e->getMessage() . "\n";
        $failed++;
    }
}

$elapsed = time() - $start_time;

echo "\n";
echo "====================================\n";
echo "Summary:\n";
echo "------------------------------------\n";
echo "Total products: {$total}\n";
echo "Already had thumbnails: {$skipped}\n";
echo "Missing thumbnails: {$missing}\n";
echo "Successfully generated: {$success}\n";
echo "Failed: {$failed}\n";
echo "Time elapsed: {$elapsed} seconds\n";
echo "====================================\n";

if ($failed > 0) {
    echo "\nNote: Some thumbnails failed to generate.\n";
    echo "Common causes:\n";
    echo "- Original image file missing or corrupted\n";
    echo "- Insufficient PHP memory\n";
    echo "- Permission issues on cache directory\n";
    exit(1);
}

echo "\nAll thumbnails generated successfully!\n";
exit(0);
