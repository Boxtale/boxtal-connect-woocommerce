echo "Packing plugin ..."

if [ ! -d "vendor" ]; then
    echo "Installing composer components ..."
    composer install --no-scripts --no-autoloader
fi

if [ ! -d "node_modules" ]; then
    echo "Installing npm components ..."
    npm install
fi

# reset possible previous changes
rm -rf pack

# initialize pack folder
echo "Copying sources into pack folder ..."
mkdir pack
cp -rf src/* pack/
rm -rf pack/Boxtal/BoxtalConnectWoocommerce/assets/less
rm pack/Boxtal/BoxtalConnectWoocommerce/assets/js/*
rm pack/Boxtal/BoxtalConnectWoocommerce/translation/*.mo

# add library
echo "Adding BoxtalPhp library ..."
cp -rf vendor/boxtal/boxtal-php-poc/src pack/Boxtal/BoxtalPhp

# add css files
echo "Adding compiled css files ..."
./node_modules/gulp/bin/gulp.js css
mv src/Boxtal/BoxtalConnectWoocommerce/assets/css pack/Boxtal/BoxtalConnectWoocommerce/assets/css

# add minified js files
echo "Adding minified js files ..."
./node_modules/gulp/bin/gulp.js js
mv src/Boxtal/BoxtalConnectWoocommerce/assets/js/*.min.js pack/Boxtal/BoxtalConnectWoocommerce/assets/js

# apply folder access rights
echo "Setting files rights ..."
find ./pack -type d -exec chmod 775 {} \;
find ./pack -type f -exec chmod 644 {} \;

echo "Pack done."
