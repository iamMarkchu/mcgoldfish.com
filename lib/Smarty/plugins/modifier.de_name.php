<?php

function smarty_modifier_de_name($termname)
{
    $regex_array = array("DE","Deutschland");
    $regex = "/ ".implode("$|", $regex_array)."$/i";
    return preg_replace($regex, "", $termname);
}

?>
