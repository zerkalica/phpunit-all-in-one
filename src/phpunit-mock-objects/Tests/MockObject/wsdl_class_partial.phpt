--TEST--
PHPUnit_Framework_MockObject_Generator::generateClassFromWsdl('GoogleSearch.wsdl', 'GoogleSearch', array('doGoogleSearch'))
--SKIPIF--
<?php
if (!extension_loaded('soap')) echo 'skip: SOAP extension is required';
?>
--FILE--
<?php
require_once 'PHPUnit/Autoload.php';
require_once 'Text/Template.php';

$generator = new PHPUnit_Framework_MockObject_Generator;

print $generator->generateClassFromWsdl(
  dirname(dirname(__FILE__)) . '/_files/GoogleSearch.wsdl',
  'GoogleSearch',
  array('doGoogleSearch')
);
?>
--EXPECTF--
class GoogleSearch extends \SOAPClient
{
    public function __construct($wsdl, array $options)
    {
        parent::__construct('%s/GoogleSearch.wsdl', $options);
    }

    public function doGoogleSearch($key, $q, $start, $maxResults, $filter, $restrict, $safeSearch, $lr, $ie, $oe)
    {
    }
}
