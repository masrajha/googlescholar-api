<?php
require_once('vendor/autoload.php');
use Stichoza\GoogleTranslate\GoogleTranslate;
function translate($teks,$src='id',$target='en'){
    $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
    return  ucwords(strtolower($tr->setSource($src)->setTarget($target)->translate($teks)));
}

// echo translate('Ubah ini dengan inggris');