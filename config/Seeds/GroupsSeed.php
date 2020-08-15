<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Groups seed.
 */
class GroupsSeed extends AbstractSeed
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
        $data = [
            [
                'name' => 'superadmin', 
                'modified' => date('Y-m-d H:i:s'), 
                'created' => date('Y-m-d H:i:s')
            ], 
            [
                'name' => 'admin', 
                'modified' => date('Y-m-d H:i:s'), 
                'created' => date('Y-m-d H:i:s')
            ]
        ];

        $table = $this->table('groups');
        $table->insert($data)->save();
    }
}
