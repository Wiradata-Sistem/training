<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Initialize extends AbstractMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up()
    {
        $this->table('groups')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('groups_users')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('group_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'user_id',
                    'group_id',
                ],
                ['unique' => true]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->addIndex(
                [
                    'group_id',
                ]
            )
            ->create();

        $this->table('users')
            ->addColumn('username', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('email', 'string', [
                'default' => null,
                'limit' => 120,
                'null' => false,
            ])
            ->addColumn('password', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'username',
                ],
                ['unique' => true]
            )
            ->addIndex(
                [
                    'email',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('groups_users')
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                ]
            )
            ->addForeignKey(
                'group_id',
                'groups',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                ]
            )
            ->update();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down()
    {
        $this->table('groups_users')
            ->dropForeignKey(
                'user_id'
            )
            ->dropForeignKey(
                'group_id'
            )->save();

        $this->table('groups')->drop()->save();
        $this->table('groups_users')->drop()->save();
        $this->table('users')->drop()->save();
    }
}
