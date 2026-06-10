<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTimebasedOneTimeCodesTable extends AbstractMigration
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
        $table = $this->table('totp_secrets', [
            'id' => false,
            'primary_key' => ['user_id'],
        ]);

        $table->addColumn('user_id', 'string', [
            'limit' => 36,
            'null' => false,
        ])
              ->addColumn('secret', 'string', [
                  'limit' => 255,
                  'null' => false,
              ])
              ->addColumn('created_at', 'datetime', [
                  'null' => false,
              ])
              ->addColumn('last_used_at', 'datetime', [
                  'null' => true,
              ])
              ->addForeignKey('user_id', 'users', 'id', [
                  'constraint' => 'fk_totp_user_id',
              ])
              ->create();
    }
}
