<?php
    if (isset($argv[1])) {
        if (($handle = fopen(dirname(__DIR__).'/'.$argv[1], "r")) !== FALSE) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ",", '"')) !== FALSE) {
                if ($row === 0) {
                    continue;
                }
                list($type, $sku, $name, $published, $isFeatured, $visibility, $shortDesc, $longDesc, , , , , , , , , $weight, , , , , , , $regularPrice, , $tags, , $images) = $data;
                shell_exec(dirname(__DIR__).'/vendor/wp-cli/wp-cli/bin/wp wc product create --name="'.$name.'" --type="'.$type.'" --status="'.$published.'" --featured="'.$isFeatured.'" --catalog_visibility="'.$visibility.'" --description="'.$longDesc.'" --short_description="'.$shortDesc.'" --sku="'.$sku.'" --regular_price="'.$regularPrice.'" --weight="'.$weight.'" --tags="'.$tags.'" --images="'.$images.'"');
                $row++;
            }
            fclose($handle);
        }
    }
?>
