<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\ConnectionManager;

/**
 * Acl component
 */
class AclComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function Check(string $username, string $controller, string $action) : bool
    {
        $c = $this->getController();
        $c->loadModel('Users');
        $user = $c->Users->find()->contain('Groups')->where(['username' => $username])->first();
        if (empty($user)) return false;
        
        $user = $user->toArray();
        if (!isset($user['groups']) || sizeof($user['groups']) < 1) return false;
        
        $aroId = $user['groups'][0]['id'];
        $conn = ConnectionManager::get('default');
        $sql = "SELECT 1 FROM `aros_acos` JOIN `acos` ON `aros_acos`.`aco_id` = `acos`.`id` JOIN `aros` ON `aros_acos`.`aro_id` = `aros`.`id` AND `aros`.`model` = 'Groups' AND `aros`.`foreign_key` = ? WHERE `acos`.`alias` = 'controllers' OR `acos`.`alias` = ? OR `acos`.`alias` = ?";
        $stmt = $conn->execute($sql, [$aroId, $controller, $action]);
        $row = $stmt->fetch('assoc');
        if (!isset($row) || empty($row)) return false;

        return true;
    }

}
