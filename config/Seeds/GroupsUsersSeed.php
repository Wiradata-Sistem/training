<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * GroupsUsers seed.
 */
class GroupsUsersSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = ['user_id' => 1, 'group_id' => 1];

        $table = $this->table('groups_users');
        $table->insert($data)->save();
    }
}
