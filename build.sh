#!/bin/sh

VENDOR="$(pwd)/src"

EZC_TMP="$VENDOR/php/tmp"
EZC_ROOT="$VENDOR/php/ezc"
EZC_AUTOLOAD="$EZC_ROOT/autoload"

COMPOSER_ONLY=

_init() {
    mkdir -p "$VENDOR"
    mkdir -p "$EZC_AUTOLOAD"
}

download() {
    cd "$VENDOR" || exit 1

    local PKGS="php-code-coverage
php-file-iterator
php-text-template
php-timer
php-token-stream
phpunit
phpunit-mock-objects
phpunit-selenium
phpunit-skeleton-generator
dbunit
phpcov
phploc
phpcpd
phpdcd
finder-facade
version"

    for i in $PKGS ; do
        git clone git://github.com/sebastianbergmann/$i.git
    done
    git clone git://github.com/theseer/fDOMDocument.git
    git clone git://github.com/naderman/ezc-base.git $EZC_TMP/Base
    git clone git://github.com/naderman/ezc-console-tools.git $EZC_TMP/ConsoleTools

    [ "$COMPOSER_ONLY" ] || git clone git://github.com/symfony/Finder.git symfony-finder/Symfony/Component/Finder
    [ "$COMPOSER_ONLY" ] || git clone git://github.com/symfony/Yaml symfony-yaml/Symfony/Component/Yaml
    [ "$COMPOSER_ONLY" ] || git clone git://github.com/symfony/ClassLoader.git symfony-class-loader/Symfony/Component/ClassLoader
}

fix_version() {
    local VER
    local i
    for i in $VENDOR/* ; do
        [ -d "$i/.git" ] || continue
        cd $i
        VER=$(git describe --tags 2> /dev/null || git branch)
        VER=$(echo "$VER" | sed -n 's/\([0-9.]*\).*/\1/p')
        [ "$VER" ] && \
            find . -type f -name '*.php' -exec perl -pi -e "s/\@package_version\@/$VER/g" {} \;
    done
}

fix_fdomdocument() {
    mkdir -p "$VENDOR/ver/SebastianBergmann/Version"
    mv "$VENDOR/version/src/"* "$VENDOR/ver/SebastianBergmann/Version"
    rm -rf "$VENDOR/version"

    mkdir -p "$VENDOR/fdomdocument/TheSeer/fDOMDocument"
    mv $VENDOR/fDOMDocument/src $VENDOR/fdomdocument/TheSeer/fDOMDocument/src
    mv $VENDOR/fDOMDocument/autoload.php $VENDOR/fdomdocument/TheSeer/fDOMDocument
    rm -rf "$VENDOR/fDOMDocument"
}

fix_ezc() {
    local i
    for i in $EZC_TMP/* ; do
        cp -R $i/src $EZC_ROOT/$(basename $i)
        cp $i/src/*_autoload.php "$EZC_AUTOLOAD"
    done
}

fix_finder_facade() {
    mkdir -p "$VENDOR/finder-facade-fix/SebastianBergmann"
    mv $VENDOR/finder-facade/src $VENDOR/finder-facade-fix/SebastianBergmann/FinderFacade
    rm -rf "$VENDOR/finder-facade"
}

fix_eoln() {
    cd $VENDOR
    find . -type f -name '*.html' -exec sed 's/\r//g' -i {} \;
    find . -type f -name '*.bat' -exec sed 's/\r//g' -i {} \;
}

clean() {
    for i in $VENDOR/* ; do
        rm -rf "$i/.git"
    done
    [ "$COMPOSER_ONLY" ] || rm -rf "$VENDOR/symfony-finder/Symfony/Component/Finder/.git"
    [ "$COMPOSER_ONLY" ] || rm -rf "$VENDOR/symfony-class-loader/Symfony/Component/ClassLoader/.git"
    rm -rf "$EZC_TMP"
}

fix_sources() {
    cd $VENDOR
    sed "s/\(\$libraryMode =[ ]*\).*/\1'pear';/g" -i $EZC_ROOT/Base/base.php
}

fix_autoloader() {
    cd $VENDOR
    sed "s/\(require_once 'File\/Iterator.*\)/require_once __DIR__ \. '\/..\/..\/..\/bin\/init\.php';\n\1/" \
        -i $VENDOR/phpunit/PHPUnit/Autoload.php -i $VENDOR/phpunit/PHPUnit/Autoload.php.in
}

rm -rf "$VENDOR"
_init
download
fix_version
fix_fdomdocument
fix_ezc
fix_finder_facade
fix_eoln
fix_sources
fix_autoloader
clean
