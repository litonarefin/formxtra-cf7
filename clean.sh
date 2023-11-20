#!/bin/bash

source .env
echo "package slug: $SLUG-pro"
echo "$(tput setaf 6)" &&

echo 'Cleanup and bundling Process started...      (10%)\r' &&
npm run prod

echo -ne "Removing existing $SLUG built Folder......              (20%)\r"

rm -rf built
#mkdir built
mkdir -p built/$SLUG-pro #multiple folder creation

echo -ne '$SLUG-pro Cleanup and bundling files started......              (30%)\r'

rsync -r --exclude '.git' --exclude '.svn' --exclude 'built' --exclude 'node_modules' --exclude 'dev' --exclude '.vscode' . built/$SLUG-pro/ --exclude 'script.js' --exclude 'clean.sh' --exclude '.github/' --exclude '.env'

echo -ne '..........          (50%)\r'

rm -rf built/$SLUG-pro/assets/scss &&
rm -rf built/$SLUG-pro/mix-manifest.json &&
rm -rf built/$SLUG-pro/package.json &&
rm -rf built/$SLUG-pro/package-lock.json &&
rm -rf built/$SLUG-pro/webpack.mix.js &&
rm -rf built/$SLUG-pro/.babelrc &&
rm -rf built/$SLUG-pro/.gitignore &&
#rm -rf built/$SLUG-pro/.DS_Store &&
find . -type f -name '*.DS_Store' -ls -delete &&
rm -rf built/$SLUG-pro/.AppleDouble &&
rm -rf built/$SLUG-pro/.LSOverride &&
rm -rf built/$SLUG-pro/.Trashes &&
rm -rf built/$SLUG-pro/.AppleDB &&
rm -rf built/$SLUG-pro/.idea &&
rm -rf built/$SLUG-pro/clean.sh &&
rm -rf built/$SLUG-pro/yarn.lock &&
rm -rf built/$SLUG-pro/composer.json &&
rm -rf built/$SLUG-pro/composer.lock &&
rm -rf built/$SLUG-pro/assets/nothing.js &&
rm -rf built/$SLUG-pro/vendor/composer/LICENSE &&
rm -rf built/$SLUG-pro/vendor/composer/installed.json &&
rm -rf built/$SLUG-pro/vendor/composer/bin &&
rm -rf built/$SLUG-pro/$SLUG-prolog.log &&
rm -rf built/$SLUG-pro/phpcs.xml.dist  &&

# Remove all Sourcemap(.map) files
find . -type f -name '*.map' -ls -delete &&
find . -type f -name '*.LICENSE.txt' -ls -delete &&

echo -ne 'Creating $SLUG-pro.zip file ......(70%)'

cd built
zip -r $SLUG-pro.zip $SLUG-pro/.
# rm -r $SLUG-pro #Delete $SLUG-pro folder

echo -ne "Creating $SLUG-pro.zip file ......(80%)"

ls -l


cd ../

# ls -l
npm i
node code-spliter.js

# Free Version Codebase Cleanup
rm -rf built/$SLUG-free/.wordpress-org &&
rm -rf built/$SLUG-free/.distignore &&
rm -rf built/$SLUG-free/.gitattributes &&
rm -rf built/$SLUG-free/.eslintignore &&
rm -rf built/$SLUG-free/.eslintrc.json &&
rm -rf built/$SLUG-free/.php-cs-fixer.dist.php &&
rm -rf built/$SLUG-free/.stylelintrc.json &&
rm -rf built/$SLUG-free/code-spliter.js &&
rm -rf built/$SLUG-free/assets/scss &&
rm -rf built/$SLUG-free/mix-manifest.json &&
rm -rf built/$SLUG-free/package.json &&
rm -rf built/$SLUG-free/package-lock.json &&
rm -rf built/$SLUG-free/webpack.mix.js &&
rm -rf built/$SLUG-free/.babelrc &&
rm -rf built/$SLUG-free/.gitignore &&
rm -rf built/$SLUG-free/.AppleDouble &&
rm -rf built/$SLUG-free/.LSOverride &&
rm -rf built/$SLUG-free/.Trashes &&
rm -rf built/$SLUG-free/.AppleDB &&
rm -rf built/$SLUG-free/.idea &&
rm -rf built/$SLUG-free/clean.sh &&
rm -rf built/$SLUG-free/yarn.lock &&
rm -rf built/$SLUG-free/composer.json &&
rm -rf built/$SLUG-free/composer.lock &&
rm -rf built/$SLUG-free/assets/nothing.js &&
rm -rf built/$SLUG-free/vendor/composer/LICENSE &&
rm -rf built/$SLUG-free/vendor/composer/installed.json &&
rm -rf built/$SLUG-free/vendor/composer/bin &&
rm -rf built/$SLUG-free/$SLUG-prolog.log &&
rm -rf built/$SLUG-free/phpcs.xml.dist  &&


# Pro Version Codebase Cleanup
rm -rf built/$SLUG-pro/.wordpress-org &&
rm -rf built/$SLUG-pro/.distignore &&
rm -rf built/$SLUG-pro/.eslintignore &&
rm -rf built/$SLUG-pro/.eslintrc.json &&
rm -rf built/$SLUG-pro/.gitattributes &&
rm -rf built/$SLUG-pro/.php-cs-fixer.dist.php &&
rm -rf built/$SLUG-pro/.stylelintrc.json &&
rm -rf built/$SLUG-pro/code-spliter.js &&

find . -type f -name '*.DS_Store' -ls -delete &&

echo -ne "Congratulations... Successfully done ..........(100%)"
