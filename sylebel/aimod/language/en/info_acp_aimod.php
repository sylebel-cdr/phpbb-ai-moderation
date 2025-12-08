<?php
/**
* English language file – info_acp – Extension sylebel/aimod
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

    // Name of the module in the ACP menu
    'ACP_AIMOD_TITLE'    => 'AI Moderation',
    'ACP_AIMOD'          => 'AI Moderation',

    // Description of the settings (rarely displayed in phpBB)
    'ACP_AIMOD_SETTINGS' => 'AI Moderation Settings',

    // ✅ Alias required by phpBB for the left menu entry
    'AIMOD_ACP_SETTINGS' => 'AI Moderation Settings',
));

