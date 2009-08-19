<?php

set_include_path(
	'../..' . PATH_SEPARATOR .
	get_include_path()
);

require_once 'Majisti/BitArray.php';
require_once 'Majisti/Crypto.php';
require_once 'Majisti/Crypto/Serial.php';
require_once 'Majisti/Crypto/Serial/Encoder.php';
require_once 'Majisti/Crypto/Serial/Decoder.php';
require_once 'Zend/Exception.php';
require_once 'Majisti/Exception.php';


header('Content-type: text/plain');

//echo "'z' = " . base_convert('z', 36, 2) . ' = ' . strlen( base_convert('z', 36, 2) ) . "\n";
//echo "'zz' = " . base_convert('zz', 36, 2) . ' = ' . strlen( base_convert('zz', 36, 2) ). "\n";
//echo "'zzz' = " . base_convert('zzz', 36, 2) . ' = ' . strlen( base_convert('zzz', 36, 2) ). "\n";
//echo "'zzzz' = " . base_convert('zzzz', 36, 2) . ' = ' . strlen( base_convert('zzzz', 36, 2) ). "\n";

function loopCheck($crypto, $count = 10) {
	for ($dummy=0; $dummy<$count; $dummy++) {
		$value = array();
		$valueLen = rand(1, 10);
		for ($i=0; $i<$valueLen; $i++) {
			$value[] = rand(-30000, 30000);
		}

		$hash = $crypto->encode($value);
		echo 'value length ' . $valueLen . ' hash = ' . $hash . '... ';

		$decodedValue = $crypto->decode($hash);

		if ( $decodedValue != $value ) {
			echo ' Failed!' . "\n";
			echo 'value = '; print_r( $value );
			echo 'decoded = '; print_r( $decodedValue ); echo "\n";
			echo '----------------------------' . "\n";
		} else {
			echo "ok\n";
		}
	}
}

$options = array();
$crypto = Majisti_Crypto::factory('Serial', $options);

try {
	$hash = $crypto->encode('test');

	echo 'WARNING : array("Test") = '; var_dump( $hash );
} catch (Exception $e) {
	echo 'array("Test") = illegal value... ok' . "\n";
}

// test null value returns an empty array
if ( $crypto->decode( $crypto->encode(NULL) ) != array() ) {
	echo "WARNING : null value does not result in empty array\n";
}


loopCheck($crypto);

echo "------------------------------\n";

$options = array(
	'chunk_separator' => '-',
	'chunk_size' => 5,
);
$crypto = Majisti_Crypto::factory('Serial', $options);

loopCheck($crypto);

echo "------------------------------\n";

$crypto = Majisti_Crypto::factory('Serial');

for ( $dummy=0; $dummy<10; $dummy++ ) {
	echo "Hash for 1 = " . $crypto->encode(1) . "\n";
}
for ( $dummy=0; $dummy<10; $dummy++ ) {
	echo "Hash for array(2 => 1) = " . $crypto->encode(array(2=>1)) . "\n";
}

$options = array(
	'private_key' => 'Hello world'
);
$crypto = Majisti_Crypto::factory('Serial', $options);

for ( $dummy=0; $dummy<1; $dummy++ ) {
	loopCheck($crypto, 3);
}

