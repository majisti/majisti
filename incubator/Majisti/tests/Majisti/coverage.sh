rm -r ../../../coverage/*
phpunit --coverage-html ../../../coverage AllTests.php 2>/dev/null
