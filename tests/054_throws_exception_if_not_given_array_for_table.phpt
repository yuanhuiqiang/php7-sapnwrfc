--TEST--
Throws FunctionCallException if not given array for RFC TABLE.
--SKIPIF--
<?php include("should_run_online_tests.inc"); ?>
--FILE--
<?php
$config = include "sapnwrfc.config.inc";
$c = new \SAPNWRFC\Connection($config);

$params = [
    "RFCTABLE" => "foobar", //< ERROR
];

$f = $c->getFunction("STFC_STRUCTURE");
$f->setParameterActive('IMPORTSTRUCT', false);
try {
    $f->invoke($params);
} catch(\SAPNWRFC\FunctionCallException $e) {
    echo "ok";
}
--EXPECT--
ok
