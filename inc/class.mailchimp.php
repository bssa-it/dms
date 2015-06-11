<?php

class dmsMailchimp extends Mailchimp {

    function __construct() {
        $this->apikey = (string)$GLOBALS['xmlConfig']->emailconfig->mailchimpApiKey;
        parent::__construct($this->apikey);
    }
   
}