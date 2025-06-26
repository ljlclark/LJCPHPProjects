del php_errors.log
del ErrorOutput.log
php HTMLTableTest.php > HTMLTableOutput.log
call ParseError.cmd
copy HTMLTableOutput.log *.html
