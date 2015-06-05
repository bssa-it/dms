<?php

$ldapconn = ldap_connect("mymail.biblesociety.co.za") or die("could not connect");
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

if ($ldapconn) {
    $un = "fredericks";
    $pw = "London2014";
    $binddn = "uid=$un,ou=people,dc=biblesociety,dc=co,dc=za";    
    $ldapbind = ldap_bind($ldapconn,$binddn,$pw);
    
    if ($ldapbind)  {
        echo "LDAP bound";
    } else {
        echo "<pre />";
        echo ldap_error($ldapconn);
        echo "<p>LDAP DID NOT BIND</p>";
    }
}

?>