<?php
namespace sylebel\aimod\acp;

class main_info
{
    public function module()
    {
        return array(
            'filename'  => '\\sylebel\\aimod\\acp\\main_module',
            'title'     => 'AIMOD_ACP_TITLE',
            'modes'     => array(
                'settings' => array(
                    'title' => 'AIMOD_ACP_SETTINGS',
                    'auth'  => 'acl_a_board',
                    'cat'   => array('ACP_CAT_DOT_MODS'),
                ),
            ),
        );
    }
}
