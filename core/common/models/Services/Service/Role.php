<?php
/**
 * Phanbook : Delightfully simple forum software
 *
 * Licensed under The GNU License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link    http://phanbook.com Phanbook Project
 * @since   1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */
namespace Phanbook\Models\Services\Service;

use Phalcon\Text;
use Phanbook\Models\Roles;
use Phanbook\Models\Services\Service;
use Phanbook\Models\Services\Exceptions;

/**
 * \Phanbook\Models\Services\Service\Role
 *
 * @package Phanbook\Models\Services\Service
 */
class Role extends Service
{
    /**
     * @var Roles
     */
    protected $defaultRole;

    /**
     * Finds first default role.
     *
     * @return Roles|null
     */
    public function findFirstDefault()
    {
        $role = Roles::query()
            ->where('isDefault = :default:', ['default' => true])
            ->cache(['key' => $this->getCacheKey()])
            ->limit(1)
            ->execute();

        return $role->valid() ? $role->getFirst() : null;
    }

    /**
     * Gets (or creates) first default role.
     *
     * @return Roles
     */
    public function getOrCreateFirstDefaultRole()
    {
        if ($this->defaultRole) {
            return $this->defaultRole;
        }

        if (!$entity = $this->findFirstDefault()) {
            $entity = $this->restoreDefault();
        }

        $this->defaultRole = $entity;

        return $entity;
    }

    /**
     * Restore default role.
     *
     * @return Roles
     * @throws Exceptions\EntityException
     */
    protected function restoreDefault()
    {
        $entity = new Roles([
            'name'        => 'Users',
            'description' => 'Member privileges, granted after account confirmation.',
            'type'        => 'user',
            'isSpecial'   => true,
            'isDefault'   => true,
        ]);

        if (!$entity->save()) {
            throw new Exceptions\EntityException($entity, 'Unable to restore default user role.');
        }

        return $entity;
    }

    protected function getCacheKey()
    {
        return Text::underscore(str_replace('\\', ' ', trim(Roles::class, '\\'))) . '_Default';
    }
}
