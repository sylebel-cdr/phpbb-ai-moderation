<?php
/**
* EN language file – info_acp – Extension sylebel/aimod
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

    // Module name in the ACP menu
    'ACP_AIMOD_TITLE'    => 'AI Moderation',
    'ACP_AIMOD'          => 'AI Moderation',

    // Optional description
    'ACP_AIMOD_SETTINGS' => 'AI Moderation settings',
));
