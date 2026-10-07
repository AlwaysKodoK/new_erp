<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'libraries/LZCompressor/LZString.php'; // if you don't use framework
 
class Send_compiler extends LZString {
     /**
     * @dataProvider simpleTextProvider
     * @param $test
     * @throws Exception
     */

    public function send()
    {
        $test="cod";
        // $a=$this->decompress($this->compress($test));
        // $a= $this->compress($test);
        $a= $this->compressToBase64($test);
        // $b= $this->decompress("ㆇ뀦䀀");
        // $a= $this->decompress($this->compress($test));
        var_dump ('compress   :'.$a); 
        // var_dump ('decompress :'.$b); exit;
    }

    public function simpleTextProvider() {
        return [
            ['a'],
            ['A'],
            ['Aa'],
            ['AA'],
            ['ӪӹĆĹ߅œƠيϼϾ'],
            ['Ӫӹ'],
            ['AAAAAA'],
            ['متن شگفت انگیز در اینجا'],
            [" «sauvegardes», «"]
        ];
    }
}