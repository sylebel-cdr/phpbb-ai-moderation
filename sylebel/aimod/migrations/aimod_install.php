<?php
namespace sylebel\aimod\migrations;

class aimod_install extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return isset($this->config['aimod_threshold']);
    }

    public static function depends_on()
    {
        return ['\phpbb\db\migration\data\v330\dev'];
    }

    public function update_data()
    {
        return [
            ['config.add', ['aimod_api_key', '']],
            ['config.add', ['aimod_threshold', 0.50]],

            ['module.add', [
                'acp',
                'ACP_CAT_DOT_MODS',
                [
                    'module_basename' => '\\sylebel\\aimod\\acp\\main_module',
                    'modes'           => ['settings'],
                ],
            ]],
        ];
    }
}
