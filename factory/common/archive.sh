set -e
./factory/common/pack.sh

echo "Creating archive ..."

rm -rf boxtal-connect
rm -f boxtal-connect.zip
mv pack boxtal-connect

zip -r boxtal-connect.zip boxtal-connect
rm -rf boxtal-connect