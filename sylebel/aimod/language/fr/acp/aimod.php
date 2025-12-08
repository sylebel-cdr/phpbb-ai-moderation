<?php
/**
* Fichier de langue FR – ACP – Extension sylebel/aimod
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

    'AIMOD_ACP_TITLE'              => 'Modération IA',
    'AIMOD_ACP_SETTINGS'           => 'Paramètres de la modération IA',
    'AIMOD_ACP_SETTINGS_EXPLAIN'   => 'Configurer ici la clé API, le modèle et les règles de modération automatique.',

    'AIMOD_SAVED'                  => 'Les paramètres ont été enregistrés.',
    'AIMOD_RESET_DEFAULTS'         => 'Réinitialiser les valeurs par défaut',
    'AIMOD_RESET_DEFAULTS_DONE'    => 'Les paramètres ont été réinitialisés.',

    'AIMOD_API_KEY'                => 'Clé API OpenAI',
    'AIMOD_API_KEY_EXPLAIN'        => 'Saisissez ici votre clé API OpenAI.',

    'AIMOD_THRESHOLD'              => 'Seuil de blocage',
    'AIMOD_THRESHOLD_EXPLAIN'      => 'Score à partir duquel un message est bloqué ou mis en modération.',

    'AIMOD_MODEL'                  => 'Modèle OpenAI',
    'AIMOD_MODEL_EXPLAIN'          => 'Nom du modèle utilisé pour la modération (ex. : gpt-4.1-mini).',

    'AIMOD_PROMPT'                 => 'Prompt système (contenus problématiques)',
    'AIMOD_PROMPT_EXPLAIN'         => 'Instructions détaillées pour la modération des contenus sensibles. Laisser vide pour utiliser le prompt interne par défaut.',

    'AIMOD_ENABLE_OFFTOPIC'        => 'Modération des hors-sujet',
    'AIMOD_ENABLE_OFFTOPIC_EXPLAIN'=> 'Active une deuxième passe de modération dédiée au hors-sujet.',
    'AIMOD_ENABLE_OFFTOPIC_LABEL'  => 'Activer la modération des hors-sujet',

    'AIMOD_PROMPT_OFFTOPIC'        => 'Instructions pour la modération des hors-sujet',
    'AIMOD_PROMPT_OFFTOPIC_EXPLAIN'=> 'Directives spécifiques pour détecter les messages hors-sujet. Laisser vide pour utiliser le prompt par défaut.',

    'AIMOD_SPINNER_TEXT'           => 'Analyse automatique en cours…',

    'AIMOD_BLOCKED_MESSAGE'        => 'Ce message a été bloqué par la modération automatique (score = %1$s, seuil = %2$s).',
));
