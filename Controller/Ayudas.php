<?php

if(!function_exists('e')){
    function e(?string $texto) : string
    {
        return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
    }
}