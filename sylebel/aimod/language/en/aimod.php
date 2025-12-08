<?php
/**
* EN language file – Frontend – Extension sylebel/aimod
* CORRECTED VERSION with all required keys
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

    // Block messages
    'AIMOD_BLOCK_HEADER'           => 'Your message was blocked by automatic moderation.',
    'AIMOD_BLOCK_REASON_SENSITIVE' => 'Reason: sensitive or potentially dangerous content (hate, harassment, violence, spam, etc.).',
    'AIMOD_BLOCK_REASON_OFFTOPIC'  => 'Reason: the message appears to be off-topic relative to the discussion thread.',

    // Block details (parameters: %1$s = category, %2$s = severity, %3$s = threshold)
    'AIMOD_BLOCK_DETAIL_SENSITIVE' => 'Category: %1$s | Severity: %2$01.2f | Threshold: %3$01.2f',

    // Unknown category
    'AIMOD_CATEGORY_UNKNOWN'       => 'unspecified',

    // Offending excerpt (parameter: %s = the excerpt)
    'AIMOD_BLOCK_SPAN'             => 'Problematic excerpt: "%s"',

    // Footer message
    'AIMOD_BLOCK_FOOTER'           => 'Please edit your message and try again. If you believe this is an error, contact a moderator.',

    // Warning messages
    'AIMOD_WARN_HEADER'            => 'Automatic moderation warning',
    'AIMOD_WARN_REASON_SENSITIVE'  => 'This message may contain sensitive content. Please follow the forum rules.',
    'AIMOD_WARN_REASON_OFFTOPIC'   => 'This message may be off-topic. Please ensure it matches the subject of the thread.',

    // Analysis spinner
    'AIMOD_SPINNER_TEXT'           => 'Automatic analysis in progress…',
));
