<?php
namespace sylebel\aimod\acp;

class main_module
{
    public $u_action;
    public $tpl_name;
    public $page_title;

    /**
     * Modèle par défaut.
     */
    protected function get_default_model()
    {
        return 'gpt-4.1-mini';
    }

    /**
     * Prompt par défaut (règles globales).
     * Doit rester cohérent avec le listener.
     */
    protected function get_default_prompt()
    {
        return <<<TXT
Your job is to review a single user message (subject + body, in any language)
and decide if it should be ALLOWED or BLOCKED according to these rules:

1) Harassment / Insults
   - Block explicit insults, targeted harassment, degrading or humiliating language toward a person or group.
   - Allow polite disagreement or neutral discussion.

2) Hate / Protected groups
   - Block hateful or dehumanizing content targeting protected groups
     (e.g. race, ethnicity, religion, nationality, gender, sexual orientation, disability, etc.).
   - Block praise or explicit support for discrimination, exclusion or violence against such groups.

3) Violence / Sexual content / Self-harm
   - Block explicit threats, promotion of violence, or encouragement of self-harm or suicide.
   - Block explicit sexual content involving minors, or clearly inappropriate sexualized content.
   - Neutral or factual mentions (e.g. news, history) are allowed if they do not praise or encourage harm.

4) Extremism, genocide denial, glorification of murderous regimes
   - Block any praise, justification, or minimization of crimes against humanity,
     genocides, or openly murderous regimes (e.g. Nazism).
   - Block messages presenting such figures as "good", "innocent" or "misunderstood",
     or denying well-established atrocities (e.g. the Holocaust).
   - Neutral or critical historical discussion is allowed if it does not excuse or glorify them.

5) Scams, phishing, fraud, commercial spam and recruitment
   - Block messages that attempt to defraud, trick or manipulate users:
     fake offers, phishing links, "easy money", fake investment schemes, etc.
   - Block obvious advertising, sales pitches, commercial promotions and spam links
     that are unrelated to the forum's scientific topics.
   - Block recruitment for paid services (coaching, training, closed private groups, etc.)
     when presented as advertising or solicitation.
   - Normal non-commercial sharing of ideas, scientific content or opinions is allowed.

6) Illicit or clearly harmful activities
   - Block content that promotes or explains how to perform obviously illegal or harmful acts
     (e.g. serious cybercrime, explicit instructions to harm others).
   - Neutral/legal discussion or academic analysis of such topics is allowed if it does not
     provide step-by-step instructions or encouragement.

7) Everything else
   - Neutral, academic, conceptual, or respectful discussion is allowed.
   - When in doubt, prefer ALLOW and keep the severity low.
TXT;
    }

    /**
     * Prompt par défaut pour la modération des hors-sujet.
     */
    protected function get_default_offtopic_prompt()
    {
        return <<<TXT
You are an AI assistant moderating a phpBB forum.
Your task is to detect OFF-TOPIC messages inside a discussion thread.

A message is OFF-TOPIC if:
- it does not answer or relate to the thread’s subject;
- it introduces unrelated themes, stories, or debates;
- it changes the direction of the discussion;
- it attempts to start a new conversation unrelated to the topic.

A message is ON-TOPIC if:
- it responds directly to the subject of the thread;
- it continues the ongoing discussion naturally;
- it brings relevant information, examples, or clarifications.

Be tolerant with minor digressions that remain helpful.
Return only one of the following strings:
"on_topic"
"off_topic"

TXT;
    }

function main($id, $mode)
    {
        global $config, $template, $request, $user, $db;

        $user->add_lang_ext('sylebel/aimod', 'acp/aimod');

        $this->tpl_name   = 'acp_aimod_body';
        $this->page_title = $user->lang('AIMOD_ACP_TITLE');

        $submit         = $request->is_set_post('submit');
        $reset_defaults = $request->is_set_post('reset_defaults');

        // ------------------------------------------------------------------
        // Réinitialiser modèle + prompt aux valeurs par défaut
        // ------------------------------------------------------------------
        if ($reset_defaults)
        {
            $default_model  = $this->get_default_model();
            $default_prompt = $this->get_default_prompt();
            $default_prompt_offtopic = $this->get_default_offtopic_prompt();

            // Modèle dans phpbb_config
            $config->set('aimod_model', $default_model);

            // Prompt dans phpbb_config_text (long texte)
            $sql = 'DELETE FROM ' . CONFIG_TEXT_TABLE . "
                    WHERE config_name = 'aimod_prompt'";
            $db->sql_query($sql);

            $sql = 'INSERT INTO ' . CONFIG_TEXT_TABLE . " (config_name, config_value)
                    VALUES ('aimod_prompt', '" . $db->sql_escape($default_prompt) . "')";
            $db->sql_query($sql);

            // Prompt hors-sujet : on remet le texte par défaut
            $sql = 'DELETE FROM ' . CONFIG_TEXT_TABLE . "
                    WHERE config_name = 'aimod_prompt_offtopic'";
            $db->sql_query($sql);

            $sql = 'INSERT INTO ' . CONFIG_TEXT_TABLE . " (config_name, config_value)
                    VALUES ('aimod_prompt_offtopic', '" . $db->sql_escape($default_prompt_offtopic) . "')";
            $db->sql_query($sql);

            trigger_error($user->lang('AIMOD_RESET_DEFAULTS_DONE') . adm_back_link($this->u_action));
        }

        // ------------------------------------------------------------------
        // Sauvegarde des paramètres
        // ------------------------------------------------------------------
        if ($submit)
        {
            $api_key = trim($request->variable('aimod_api_key', '', true));

            $threshold_input = (string) $request->variable('aimod_threshold', '0.5');
            $threshold       = (float) $threshold_input;
            if ($threshold <= 0.0 || $threshold > 1.0)
            {
                $threshold = 0.5;
            }

            $model  = trim($request->variable('aimod_model', '', true));
            $prompt = trim($request->variable('aimod_prompt', '', true));
            $prompt_offtopic = trim($request->variable('aimod_prompt_offtopic', '', true));

            // Case unique : activer / désactiver la modération des hors-sujet
            $enable_offtopic = $request->variable('aimod_enable_offtopic', 0);

            // Sauvegarde dans phpbb_config
            $config->set('aimod_api_key', $api_key);
            $config->set('aimod_threshold', $threshold);
            $config->set('aimod_model', $model);
            $config->set('aimod_enable_offtopic', $enable_offtopic ? 1 : 0);

            // Prompt dans phpbb_config_text (long texte)
            $sql = 'DELETE FROM ' . CONFIG_TEXT_TABLE . "
                    WHERE config_name = 'aimod_prompt'";
            $db->sql_query($sql);

            if ($prompt !== '')
            {
                $sql = 'INSERT INTO ' . CONFIG_TEXT_TABLE . " (config_name, config_value)
                        VALUES ('aimod_prompt', '" . $db->sql_escape($prompt) . "')";
                $db->sql_query($sql);
            }

            // Prompt hors-sujet dans phpbb_config_text
            $sql = 'DELETE FROM ' . CONFIG_TEXT_TABLE . "
                    WHERE config_name = 'aimod_prompt_offtopic'";
            $db->sql_query($sql);

            if ($prompt_offtopic !== '')
            {
                $sql = 'INSERT INTO ' . CONFIG_TEXT_TABLE . " (config_name, config_value)
                        VALUES ('aimod_prompt_offtopic', '" . $db->sql_escape($prompt_offtopic) . "')";
                $db->sql_query($sql);
            }

            trigger_error($user->lang('AIMOD_SAVED') . adm_back_link($this->u_action));
        }

        // ------------------------------------------------------------------
        // Chargement du prompt actuel depuis phpbb_config_text
        // ------------------------------------------------------------------
        $current_prompt = '';
        $sql = 'SELECT config_value
                FROM ' . CONFIG_TEXT_TABLE . "
                WHERE config_name = 'aimod_prompt'";
        $result = $db->sql_query($sql);
        $current_prompt = (string) $db->sql_fetchfield('config_value');
        $db->sql_freeresult($result);

        // Prompt hors-sujet : on charge depuis config_text, ou valeur par défaut si absent
        $current_prompt_offtopic = '';
        $sql = 'SELECT config_value
                FROM ' . CONFIG_TEXT_TABLE . "
                WHERE config_name = 'aimod_prompt_offtopic'";
        $result = $db->sql_query($sql);
        $current_prompt_offtopic = (string) $db->sql_fetchfield('config_value');
        $db->sql_freeresult($result);

        if ($current_prompt_offtopic === '')
        {
            $current_prompt_offtopic = $this->get_default_offtopic_prompt();
        }

        // ------------------------------------------------------------------
        // Variables de template
        // ------------------------------------------------------------------
        $template->assign_vars(array(
            'U_ACTION'        => $this->u_action,
            'AIMOD_API_KEY'   => $config['aimod_api_key']   ?? '',
            'AIMOD_THRESHOLD' => $config['aimod_threshold'] ?? '0.5',
            'AIMOD_MODEL'     => $config['aimod_model']     ?? $this->get_default_model(),
            'AIMOD_PROMPT'    => $current_prompt,
            'AIMOD_PROMPT_OFFTOPIC' => $current_prompt_offtopic,

            // IMPORTANT : correspond à {AIMOD_ENABLE_OFFTOPIC_CHECKED} dans le template
            'AIMOD_ENABLE_OFFTOPIC_CHECKED' => !empty($config['aimod_enable_offtopic']) ? ' checked="checked"' : '',
        ));
    }
}
