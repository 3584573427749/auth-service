<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddMagicLinksTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {

        $table = $this->table('magic_links', [
            'id' => false,
            'primary_key' => ['id'],
        ]);

        $table
            ->addColumn('id', 'string', [
                'limit' => 36,
                'null' => false,
            ])
            ->addColumn('user_id', 'string', [
                'limit' => 36,
                'null' => false,
            ])
            ->addColumn('token_hash', 'string', [
                'limit' => 64,
                'null' => false,
            ])
            ->addColumn('client_type', 'enum', [
                'values' => [
                    'pwa_trainer',
                    'pwa_results',
                    'main_ui',
                ],
                'null' => false,
            ])
            ->addColumn('expires_at', 'datetime', [
                'null' => false,
            ])
            ->addColumn('consumed_at', 'datetime', [
                'null' => true,
                'default' => null,
            ])
            ->addColumn('created_at', 'datetime', [
                'null' => false,
            ])
            ->addIndex(['user_id'], [
                'name' => 'idx_magic_links_user_id',
            ])
            ->addIndex(['token_hash'], [
                'name' => 'idx_magic_links_token_hash',
            ])
            ->addForeignKey('user_id', 'users', 'id', [
                'constraint' => 'fk_magic_links_user_id',
            ])
            ->create();
    }
}
