<?php

declare(strict_types=1);

namespace Modules\User\traits;

use Illuminate\Support\Str;

trait Permission
{
    /**
     * @var array
     */
    private array $permissions;

    /**
     *
     * @param array|null $permissions
     */
    public function __construct(array $permissions = [])
    {
        $this->permissions = $permissions;
    }

    /**
     * @param string $permission
     * @param boolean $value
     * @return self
     */
    public function updatePermission(string $permission, $value = true): self
    {
        if (array_key_exists($permission, $this->permissions)) {
            $permissions = $this->permissions;

            $permissions[$permission] = $value;

            $this->permissions = $permissions;
        }
        return $this;
    }

    /**
     * @param string $permission
     * @param boolean $value
     * @return self
     */
    public function addPermission(string $permission, $value = true): self
    {
        if (!array_key_exists($permission, $this->permissions)) {
            $this->permissions = array_merge($this->permissions, [$permission => $value]);
        }

        return $this;
    }

    /**
     * @param string $permission
     * @return self
     */
    public function removePermission(string $permission): self
    {
        if (array_key_exists($permission, $this->permissions)) {
            $permissions = $this->permissions;

            unset($permissions[$permission]);

            $this->permissions = $permissions;
        }

        return $this;
    }

    /**
     * @param array $permissions
     * @return boolean
     */
    public function hasAccess(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->checkPermission($this->permissions, $permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $permissions
     * @return boolean
     */
    public function hasAnyAccess(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->checkPermission($this->permissions, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks a permission in the permissions array.
     *
     * @param array $prepared
     * @param string $permission
     * @return bool
     */
    protected function checkPermission(array $prepared, string $permission): bool
    {
        if (array_key_exists($permission, $prepared) && $prepared[$permission] === true) {
            return true;
        }

        foreach ($prepared as $key => $value) {
            if ((Str::is($permission, $key) || Str::is($key, $permission)) && $value === true) {
                return true;
            }
        }

        return false;
    }
}
