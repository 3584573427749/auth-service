<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUserTable extends AbstractMigration {
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
    public function change() : void {

        $table = $this->table('users', [
            'id' => false,
            'primary_key' => ['id'],
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $table->addColumn('id', 'string', [
            'limit' => 36,
            'null' => false,
        ])
              ->addColumn('email', 'string', [
                  'limit' => 100,
                  'null' => false,
              ])
              ->addColumn('first_name', 'string', [
                  'limit' => 100,
                  'null' => false,
              ])
              ->addColumn('last_name', 'string', [
                  'limit' => 100,
                  'null' => false,
              ])
              ->addColumn('is_active', 'boolean', [
                  'null' => false,
                  'default' => true,
              ])
              ->addColumn('created_at', 'datetime', [
                  'null' => false,
              ])
              ->addColumn('updated_at', 'datetime', [
                  'null' => true,
                  'default' => null,
              ])
              ->addIndex(['email'], [
                  'unique' => true,
                  'name' => 'idx_users_email_unique',
              ])
              ->create();
    }
}
