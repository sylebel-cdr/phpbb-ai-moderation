<?php
/**
* Fichier de langue FR – info_acp – Extension sylebel/aimod
*/

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

$lang = array_merge($lang, array(

    // Nom du module dans le menu ACP
    'ACP_AIMOD_TITLE'    => 'Modération IA',
    'ACP_AIMOD'          => 'Modération IA',

    // Description éventuelle (peu utilisée dans l’interface)
    'ACP_AIMOD_SETTINGS' => 'Paramètres de la modération IA',

    // ✅ Alias pour le module qui attend AIMOD_ACP_SETTINGS
    'AIMOD_ACP_SETTINGS' => 'Paramètres de la modération IA',
));
