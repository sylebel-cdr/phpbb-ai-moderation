<?php
/**
* Fichier de langue FR – Frontend – Extension sylebel/aimod
* VERSION CORRIGÉE avec toutes les clés nécessaires
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

    // Messages de blocage
    'AIMOD_BLOCK_HEADER'           => 'Votre message a été bloqué par la modération automatique.',
    'AIMOD_BLOCK_REASON_SENSITIVE' => 'Motif : contenu sensible ou potentiellement dangereux (haine, harcèlement, violence, spam, etc.).',
    'AIMOD_BLOCK_REASON_OFFTOPIC'  => 'Motif : le message semble hors sujet par rapport au fil de discussion.',

    // Détails du blocage (paramètres : %1$s = catégorie, %2$s = sévérité, %3$s = seuil)
    'AIMOD_BLOCK_DETAIL_SENSITIVE' => 'Catégorie : %1$s | Sévérité : %2$01.2f | Seuil : %3$01.2f',

    // Catégorie inconnue
    'AIMOD_CATEGORY_UNKNOWN'       => 'non spécifiée',

    // Extrait problématique (paramètre : %s = l\'extrait)
    'AIMOD_BLOCK_SPAN'             => 'Extrait problématique : « %s »',

    // Pied de message
    'AIMOD_BLOCK_FOOTER'           => 'Veuillez modifier votre message et réessayer. Si vous pensez qu\'il s\'agit d\'une erreur, contactez un modérateur.',

    // Messages d'avertissement
    'AIMOD_WARN_HEADER'            => 'Avertissement de la modération automatique',
    'AIMOD_WARN_REASON_SENSITIVE'  => 'Ce message pourrait contenir du contenu sensible. Merci de respecter les règles du forum.',
    'AIMOD_WARN_REASON_OFFTOPIC'   => 'Ce message semble hors sujet. Merci de vérifier qu\'il correspond bien au thème du fil.',

    // Spinner d'analyse
    'AIMOD_SPINNER_TEXT'           => 'Analyse automatique en cours…',
));
