<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPermissionsTable extends AbstractMigration {
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
        $table = $this->table('permissions', [
            'id' => false,
            'primary_key' => ['id'],
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $table->addColumn('id', 'string', [
            'limit' => 36,
            'null' => false,
        ])
              ->addColumn('name', 'string', [
                  'limit' => 150,
                  'null' => false,
              ])
              ->addColumn('description', 'string', [
                  'limit' => 255,
                  'null' => false,
              ])
              ->create();
    }
}
