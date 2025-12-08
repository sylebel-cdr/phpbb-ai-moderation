<?php
namespace sylebel\aimod\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var string */
    protected $last_text = '';

    public function __construct(\phpbb\config\config $config)
    {
        $this->config = $config;
    }

    public static function getSubscribedEvents()
    {
        return [
            'core.user_setup'                       => 'load_language',
            'core.posting_modify_message_text'      => 'capture_text',
            'core.posting_modify_submission_errors' => 'moderate_message',
        ];
    }

    /**
     * Charge les chaînes de langue de l’extension (spinner + messages d’erreur).
     */
    public function load_language($event)
    {
        // Récupérer le tableau existant
        $lang_set_ext = $event['lang_set_ext'];

        // Ajouter l’extension SANS EFFACER ce qui existe déjà
        $lang_set_ext[] = array(
            'ext_name' => 'sylebel/aimod',
            'lang_set' => 'aimod',
        );

        // Réinjecter le tableau dans l’événement
        $event['lang_set_ext'] = $lang_set_ext;
    }


    /**
     * Étape 1 — On capture sujet + corps quand l’utilisateur soumet le formulaire.
     */
    public function capture_text($event)
    {
        if (empty($event['submit']))
        {
            return;
        }

        $post_data = isset($event['post_data']) ? $event['post_data'] : [];
        $subject   = isset($post_data['post_subject']) ? (string) $post_data['post_subject'] : '';

        $body = '';
        if (isset($event['message_parser']) && is_object($event['message_parser']))
        {
            $body = (string) $event['message_parser']->message;
        }

        $this->last_text = trim($subject . "\n\n" . $body);
    }

    /**
     * Étape 2 — Modération principale.
     */
    public function moderate_message($event)
    {
        global $user, $db;

        if (empty($event['submit']))
        {
            return;
        }

        // Clé API obligatoire, sinon on laisse tout passer.
        $api_key = trim($this->config['aimod_api_key'] ?? '');
        if ($api_key === '')
        {
            return;
        }

        // Seuil global (0–1)
        $threshold_cfg = $this->config['aimod_threshold'] ?? '0.5';
        $threshold     = (float) $threshold_cfg;
        if ($threshold <= 0.0 || $threshold > 1.0)
        {
            $threshold = 0.5;
        }

        // Modération des hors-sujet activée ?
        $enable_offtopic = !empty($this->config['aimod_enable_offtopic']);

        // Récupération du texte (si jamais capture_text n’a rien stocké).
        $text = $this->last_text;

        if ($text === '')
        {
            $post_data = isset($event['post_data']) ? $event['post_data'] : [];
            $subject   = isset($post_data['post_subject']) ? (string) $post_data['post_subject'] : '';

            $body = '';
            if (isset($event['message_parser']) && is_object($event['message_parser']))
            {
                $body = (string) $event['message_parser']->message;
            }

            $text = trim($subject . "\n\n" . $body);
        }

        if ($text === '')
        {
            // Rien à analyser.
            return;
        }

        // Langue de l’interface (fr/en) pour adapter l’explication.
        $ui_lang = 'en';
        if (!empty($user->data['user_lang']))
        {
            $tmp = substr($user->data['user_lang'], 0, 2);
            if ($tmp === 'fr' || $tmp === 'en')
            {
                $ui_lang = $tmp;
            }
        }

        // Contexte de forum (nom + description) pour le hors-sujet.
        $forum_context = '';
        $forum_id      = 0;

        if (isset($event['forum_id']))
        {
            $forum_id = (int) $event['forum_id'];
        }
        elseif (!empty($event['post_data']['forum_id']))
        {
            $forum_id = (int) $event['post_data']['forum_id'];
        }

        if ($forum_id > 0)
        {
            $sql = 'SELECT forum_name, forum_desc
                    FROM ' . FORUMS_TABLE . '
                    WHERE forum_id = ' . $forum_id;
            $result = $db->sql_query($sql);
            if ($row = $db->sql_fetchrow($result))
            {
                $forum_context = (string) $row['forum_name'];
                if (!empty($row['forum_desc']))
                {
                    $forum_context .= ' — ' . $row['forum_desc'];
                }
            }
            $db->sql_freeresult($result);
        }

        // Erreurs existantes
        $errors = isset($event['error']) ? $event['error'] : [];
        if (!is_array($errors))
        {
            $errors = [$errors];
        }

        // Résultat final
        $blocked        = false;
        $block_category = '';
        $block_severity = 0.0;
        $offending_span = '';
        $block_detail   = '';

        // ------------------------------------------------------------------
        // 1) APPEL IA — MODÉRATION “SÉCURITÉ” (insultes, haine, etc.)
        // ------------------------------------------------------------------

        $model = trim($this->config['aimod_model'] ?? '');
        if ($model === '')
        {
            $model = 'gpt-4.1-mini';
        }

        // Prompt “sécurité” (pas de logique hors-sujet ici)
        $system_prompt  = "You are an automatic safety moderation assistant for the forum \"Conscience du Réel (CdR)\".\n\n";
        $system_prompt .= "You only decide if the message is SAFE or UNSAFE according to safety rules (harassment, hate, violence, sexual content, self-harm, extremism, spam, scams).\n";
        $system_prompt .= "You DO NOT handle off-topic detection here.\n\n";
        $system_prompt .= "The forum interface language for this user is \"{$ui_lang}\".\n";
        $system_prompt .= "- If \"{$ui_lang}\" is \"fr\", write the \"explanation\" field in French.\n";
        $system_prompt .= "- If \"{$ui_lang}\" is \"en\", write the \"explanation\" field in English.\n";
        $system_prompt .= "- Default to English only if you are unsure.\n\n";

        $rules_block  = "Your job is to review ONE user message (subject + body, in any language)\n";
        $rules_block .= "and decide if it should be ALLOWED or BLOCKED according to these rules:\n\n";

        $rules_block .= "1) Harassment / Insults\n";
        $rules_block .= "   - Block explicit insults, targeted harassment, degrading or humiliating language toward a person or group.\n";
        $rules_block .= "   - Allow polite disagreement or neutral discussion.\n\n";

        $rules_block .= "2) Hate / Protected groups\n";
        $rules_block .= "   - Block hateful or dehumanizing content targeting protected groups\n";
        $rules_block .= "     (race, ethnicity, religion, nationality, gender, sexual orientation, disability, etc.).\n";
        $rules_block .= "   - Block praise or explicit support for discrimination, exclusion or violence against such groups.\n\n";

        $rules_block .= "3) Violence / Sexual content / Self-harm\n";
        $rules_block .= "   - Block explicit threats, promotion of violence, or encouragement of self-harm.\n";
        $rules_block .= "   - Block explicit sexual content involving minors, or clearly inappropriate sexualized content.\n";
        $rules_block .= "   - Neutral or factual mentions (news, history) are allowed if they do not praise or encourage harm.\n\n";

        $rules_block .= "4) Extremism, genocide denial, glorification of murderous regimes\n";
        $rules_block .= "   - Block any praise, justification, or minimization of crimes against humanity,\n";
        $rules_block .= "     genocides, or openly murderous regimes (e.g. Nazism).\n";
        $rules_block .= "   - Block messages presenting such figures as \"good\", \"innocent\" or \"misunderstood\",\n";
        $rules_block .= "     or denying well-established atrocities (e.g. the Holocaust).\n";
        $rules_block .= "   - Neutral or critical historical discussion is allowed if it does not excuse or glorify them.\n\n";

        $rules_block .= "5) Scams, phishing, fraud, commercial spam and recruitment\n";
        $rules_block .= "   - Block messages that attempt to defraud, trick or manipulate users.\n";
        $rules_block .= "   - Block obvious advertising, sales pitches, commercial promotions and spam links\n";
        $rules_block .= "     unrelated to the forum's scientific topics.\n\n";

        $rules_block .= "6) Illicit or clearly harmful activities\n";
        $rules_block .= "   - Block content that promotes or explains how to perform obviously illegal or harmful acts.\n";
        $rules_block .= "   - Neutral/legal discussion or academic analysis is allowed if it does not provide\n";
        $rules_block .= "     step-by-step instructions or encouragement.\n\n";

        $rules_block .= "7) Everything else\n";
        $rules_block .= "   - Neutral, academic, conceptual, or respectful discussion is allowed.\n";
        $rules_block .= "   - When in doubt, prefer ALLOW and keep the severity low.\n";

        $json_block  = "\n\nYou MUST respond STRICTLY in JSON, with NO extra text, using this structure:\n\n";
        $json_block .= "{\n";
        $json_block .= "  \"decision\": \"allow\" or \"block\",\n";
        $json_block .= "  \"category\": \"harassment\" | \"hate\" | \"violence\" | \"sexual\" | \"selfharm\" | \"extremism\" | \"spam\" | \"other\",\n";
        $json_block .= "  \"severity\": number between 0.0 and 1.0,\n";
        $json_block .= "  \"offending_span\": \"a SHORT exact excerpt of the message that best illustrates the problem (or empty string)\",\n";
        $json_block .= "  \"explanation\": \"a short explanation written in the forum interface language given above\"\n";
        $json_block .= "}\n\n";
        $json_block .= "Important:\n";
        $json_block .= "- \"severity\" should reflect how serious the violation is AND how confident you are that the message violates the rules.\n";
        $json_block .= "- Keep \"offending_span\" very short and do NOT invent new text: always copy exactly from the original message.\n";

        $system_prompt_safety = $system_prompt . $rules_block . $json_block;

        $user_prompt  = "User message (subject + body):\n";
        $user_prompt .= $text;

        $payload_safety = [
            'model'    => $model,
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt_safety],
                ['role' => 'user',   'content' => $user_prompt],
            ],
            'temperature' => 0.0,
        ];

        $data_safety = $this->call_openai_chat($api_key, $payload_safety);

        if ($data_safety === null || empty($data_safety['choices'][0]['message']['content']))
        {
            // Fail open si l’API ne répond pas correctement.
            $event['error'] = $errors;
            return;
        }

        $content_safety = $data_safety['choices'][0]['message']['content'];

        $json_safety = json_decode($content_safety, true);
        if (!is_array($json_safety))
        {
            $event['error'] = $errors;
            return;
        }

        $decision = strtolower(trim((string) ($json_safety['decision'] ?? 'allow')));
        $category = (string) ($json_safety['category'] ?? 'other');
        $severity = (float)  ($json_safety['severity'] ?? 0.0);
        $span     = (string) ($json_safety['offending_span'] ?? '');
        $explain  = (string) ($json_safety['explanation'] ?? '');

        if ($severity < 0.0) $severity = 0.0;
        if ($severity > 1.0) $severity = 1.0;

        if ($decision === 'block' && $severity >= $threshold)
        {
            $blocked        = true;
            $block_category = $category;
            $block_severity = $severity;
            $offending_span = $span;
            $block_detail   = $explain;
        }

        // ------------------------------------------------------------------
        // 2) APPEL IA — MODÉRATION HORS-SUJET (si pas déjà bloqué)
        // ------------------------------------------------------------------

        if (!$blocked && $enable_offtopic)
        {
            $system_prompt_ot  = "You are an assistant that ONLY detects whether a message is off-topic for a given forum section.\n";
            $system_prompt_ot .= "You do NOT evaluate toxicity, insults or safety here, only topicality.\n\n";
            $system_prompt_ot .= "If the message does not reasonably fit the topic of the section, mark it as off-topic.\n";
            $system_prompt_ot .= "If it is clearly off-topic, severity should be high (>= 0.7).\n\n";
            $system_prompt_ot .= "The forum interface language for this user is \"{$ui_lang}\".\n";
            $system_prompt_ot .= "- If \"{$ui_lang}\" is \"fr\", write the \"explanation\" field in French.\n";
            $system_prompt_ot .= "- If \"{$ui_lang}\" is \"en\", write the \"explanation\" field in English.\n";

            $json_block_ot  = "\nYou MUST respond STRICTLY in JSON, with NO extra text, using this structure:\n\n";
            $json_block_ot .= "{\n";
            $json_block_ot .= "  \"offtopic\": true or false,\n";
            $json_block_ot .= "  \"severity\": number between 0.0 and 1.0,\n";
            $json_block_ot .= "  \"offending_span\": \"a SHORT exact excerpt that shows why it is off-topic (or empty string)\",\n";
            $json_block_ot .= "  \"explanation\": \"short explanation, in the forum interface language\"\n";
            $json_block_ot .= "}\n";

            $system_prompt_offtopic = $system_prompt_ot . $json_block_ot;

            $user_prompt_ot  = "Forum section: ";
            $user_prompt_ot .= ($forum_context !== '') ? $forum_context : '(no description)';
            $user_prompt_ot .= "\n\nUser message (subject + body):\n";
            $user_prompt_ot .= $text;

            $payload_ot = [
                'model'    => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $system_prompt_offtopic],
                    ['role' => 'user',   'content' => $user_prompt_ot],
                ],
                'temperature' => 0.0,
            ];

            $data_ot = $this->call_openai_chat($api_key, $payload_ot);

            if ($data_ot !== null && !empty($data_ot['choices'][0]['message']['content']))
            {
                $content_ot = $data_ot['choices'][0]['message']['content'];

                $json_ot = json_decode($content_ot, true);
                if (is_array($json_ot))
                {
                    $offtopic_flag = !empty($json_ot['offtopic']);
                    $sev_ot        = (float) ($json_ot['severity'] ?? 0.0);
                    $span_ot       = (string) ($json_ot['offending_span'] ?? '');
                    $explain_ot    = (string) ($json_ot['explanation'] ?? '');

                    if ($sev_ot < 0.0) $sev_ot = 0.0;
                    if ($sev_ot > 1.0) $sev_ot = 1.0;

                    // On bloque si la réponse dit off-topic et que la sévérité dépasse le seuil.
                    if ($offtopic_flag && $sev_ot >= $threshold)
                    {
                        $blocked        = true;
                        $block_category = 'offtopic';
                        $block_severity = $sev_ot;
                        $offending_span = $span_ot;
                        $block_detail   = $explain_ot;
                    }
                }
            }
        }

        // ------------------------------------------------------------------
        // 3) SI RIEN DE BLOQUÉ → ON LAISSE PASSER
        // ------------------------------------------------------------------

        if (!$blocked)
        {
            $event['error'] = $errors;
            return;
        }

        // ------------------------------------------------------------------
        // 4) CONSTRUCTION DU MESSAGE D’ERREUR UTILISATEUR
        // ------------------------------------------------------------------

        $msg_parts = [];

        // En-tête
        $msg_parts[] = $user->lang('AIMOD_BLOCK_HEADER');

        // Raison générale
        if ($block_category === 'offtopic')
        {
            $msg_parts[] = $user->lang('AIMOD_BLOCK_REASON_OFFTOPIC');
        }
        else
        {
            $msg_parts[] = $user->lang('AIMOD_BLOCK_REASON_SENSITIVE');
        }

        // Détail numérique
        $msg_parts[] = $user->lang(
            'AIMOD_BLOCK_DETAIL_SENSITIVE',
            ($block_category !== '' ? $block_category : $user->lang('AIMOD_CATEGORY_UNKNOWN')),
            $block_severity,
            $threshold
        );

        // Explication de l’IA
        if ($block_detail !== '')
        {
            $msg_parts[] = $block_detail;
        }

        // Extrait problématique
        if ($offending_span !== '')
        {
            $msg_parts[] = $user->lang('AIMOD_BLOCK_SPAN', $offending_span);
        }

        // Pied
        $msg_parts[] = $user->lang('AIMOD_BLOCK_FOOTER');

        $errors[]       = implode(' ', $msg_parts);
        $event['error'] = $errors;
    }

    /**
     * Appel générique à /v1/chat/completions.
     */
    protected function call_openai_chat($api_key, array $payload)
    {
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . 'Bearer ' . $api_key,
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $raw = curl_exec($ch);
        if ($raw === false || curl_errno($ch))
        {
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        $data = json_decode($raw, true);
        if (!is_array($data))
        {
            return null;
        }

        return $data;
    }
}
