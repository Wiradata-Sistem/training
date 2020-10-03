<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ForgotPassword Entity
 *
 * @property int $id
 * @property string $token
 * @property int $user_id
 * @property bool $is_used
 * @property \Cake\I18n\FrozenTime $expired
 *
 * @property \App\Model\Entity\User $user
 */
class ForgotPassword extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'token' => true,
        'user_id' => true,
        'is_used' => true,
        'expired' => true,
        'user' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'token',
    ];
}
