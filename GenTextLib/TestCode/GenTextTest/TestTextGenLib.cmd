del php_errors.log
del Debug\LJCSectionsStatic.txt
php TestTextGenLib.php > TestTextGenLibOutput.txt
copy TestTextGenLibOutput.txt *.html
