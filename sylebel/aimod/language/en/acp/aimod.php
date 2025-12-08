<?php
/**
* EN language file – ACP – Extension sylebel/aimod
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

    'AIMOD_ACP_TITLE'              => 'AI Moderation',
    'AIMOD_ACP_SETTINGS'           => 'AI Moderation Settings',
    'AIMOD_ACP_SETTINGS_EXPLAIN'   => 'Configure API key, model and moderation rules here.',

    'AIMOD_SAVED'                  => 'Settings have been saved.',
    'AIMOD_RESET_DEFAULTS'         => 'Reset to default values',
    'AIMOD_RESET_DEFAULTS_DONE'    => 'The default parameters have been restored.',

    'AIMOD_API_KEY'                => 'OpenAI API Key',
    'AIMOD_API_KEY_EXPLAIN'        => 'Enter your OpenAI API key here.',

    'AIMOD_THRESHOLD'              => 'Blocking threshold',
    'AIMOD_THRESHOLD_EXPLAIN'      => 'Score above which a message is blocked or queued.',

    'AIMOD_MODEL'                  => 'OpenAI Model',
    'AIMOD_MODEL_EXPLAIN'          => 'Name of the model used for moderation (e.g. gpt-4.1-mini).',

    'AIMOD_PROMPT'                 => 'System prompt (problematic content)',
    'AIMOD_PROMPT_EXPLAIN'         => 'Detailed rules for safety moderation. Leave empty to use the internal default prompt.',

    'AIMOD_ENABLE_OFFTOPIC'        => 'Off-topic moderation',
    'AIMOD_ENABLE_OFFTOPIC_EXPLAIN'=> 'Enables a second moderation pass for off-topic detection.',
    'AIMOD_ENABLE_OFFTOPIC_LABEL'  => 'Enable off-topic moderation',

    'AIMOD_PROMPT_OFFTOPIC'        => 'Off-topic moderation instructions',
    'AIMOD_PROMPT_OFFTOPIC_EXPLAIN'=> 'Specific guidelines for detecting off-topic messages. Leave empty to use the default prompt.',

    'AIMOD_SPINNER_TEXT'           => 'Automatic analysis in progress…',

    'AIMOD_BLOCKED_MESSAGE'        => 'This message was blocked by AI moderation (score = %1$s, threshold = %2$s).',
));
