<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateForgotPasswords extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('forgot_passwords');
        $table->addColumn('token', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('user_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('is_used', 'boolean', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('expired', 'timestamp', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();
    }
}
