<?php

namespace Radiate\Database\Models;

use DateTimeImmutable;
use Radiate\Auth\Authenticatable;
use Radiate\Auth\Contracts\Authenticatable as AuthenticatableContract;
use Radiate\Database\Concerns\HasMeta;
use Radiate\Database\Model;
use Radiate\Database\UserQueryBuilder;
use Radiate\Support\Facades\Gate;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable, HasMeta;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * The object type for the model.
     *
     * @var string
     */
    protected $objectType = 'user';

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'include';
    }

    /**
     * Perform any actions required before the model boots.
     *
     * @return void
     */
    protected static function boot()
    {
        require_once ABSPATH . 'wp-admin/includes/user.php';
    }

    /**
     * Create a new query builder for the model.
     *
     * @return \Radiate\Database\Builder
     */
    public function newQueryBuilder()
    {
        return new UserQueryBuilder();
    }

    /**
     * Perform a model update operation.
     *
     * @return bool
     */
    protected function performUpdate()
    {
        return !is_wp_error(wp_update_user($this->getAttributes()));
    }

    /**
     * Perform a model insert operation.
     *
     * @return bool
     */
    protected function performInsert()
    {
        $id = wp_insert_user($this->getAttributes());

        if (!is_wp_error($id)) {
            $this->fill(get_user_by('ID', $id)->to_array());

            return true;
        }

        return false;
    }

    /**
     * Perform a model delete operation.
     *
     * @return bool
     */
    protected function performDelete()
    {
        return wp_delete_user($this->getKey());
    }

    /**
     * Get the ID
     *
     * @return integer|null
     */
    public function getIdAttribute(): ?int
    {
        return $this->attributes['ID'];
    }

    /**
     * Set the ID
     *
     * @param integer $value
     * @return void
     */
    public function setIdAttribute(int $value): void
    {
        $this->attributes['ID'] = $value;
    }

    /**
     * Get the email attribute
     *
     * @return string
     */
    public function getEmailAttribute()
    {
        return $this->attributes['user_email'];
    }

    /**
     * Set the email attribute
     *
     * @param string $value
     * @return void
     */
    public function setEmailAttribute(string $value)
    {
        $this->attributes['user_email'] = $value;
    }

    /**
     * Get the url attribute
     *
     * @return string|null
     */
    public function getUrlAttribute()
    {
        return $this->attributes['user_url'];
    }

    /**
     * Set the url attribute
     *
     * @param string $value
     * @return void
     */
    public function setUrlAttribute(?string $value = null)
    {
        $this->attributes['user_url'] = $value;
    }

    /**
     * Get the registered attribute
     *
     * @return \DateTimeImmutable
     */
    public function getRegisteredAttribute()
    {
        return new DateTimeImmutable($this->attributes['user_registered']);
    }

    /**
     * Set the registered attribute
     *
     * @param \DateTimeImmutable  $value
     * @return void
     */
    public function setRegisteredAttribute(DateTimeImmutable $value)
    {
        $this->attributes['user_registered'] = $value->format('Y-m-d H:i:s');
    }

    /**
     * Get the login name attribute
     *
     * @return string
     */
    public function getLoginAttribute()
    {
        return $this->attributes['user_login'];
    }

    /**
     * Set the login name attribute
     *
     * @param string $value
     * @return void
     */
    public function setLoginAttribute(string $value)
    {
        $this->attributes['user_login'] = $value;
    }

    /**
     * Get the display name attribute
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->attributes['display_name'];
    }

    /**
     * Set the display name attribute
     *
     * @param string $value
     * @return void
     */
    public function setNameAttribute(string $value)
    {
        $this->attributes['display_name'] = $value;
    }

    /**
     * Get the "nice" name attribute
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return $this->attributes['user_nicename'];
    }

    /**
     * Set the "nice" name attribute
     *
     * @param string $value
     * @return void
     */
    public function setSlugAttribute(string $value)
    {
        $this->attributes['user_nicename'] = $value;
    }

    /**
     * Get the display name attribute
     *
     * @return array
     */
    public function roles()
    {
        return get_userdata($this->getKey())->roles;
    }

    /**
     * Determine if the user has a role
     *
     * @param string $role
     * @return boolean
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles());
    }

    /**
     * Get the user capabilities
     *
     * @return array
     */
    public function capabilities()
    {
        return get_userdata($this->getKey())->allcaps;
    }

    /**
     * Determine if the entity has the given abilities.
     *
     * @param  array|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function can($abilities, $arguments = [])
    {
        return Gate::forUser($this)->check($abilities, $arguments);
    }

    /**
     * Determine if the entity has any of the given abilities.
     *
     * @param  array|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function canAny($abilities, $arguments = [])
    {
        return Gate::forUser($this)->any($abilities, $arguments);
    }

    /**
     * Determine if the entity does not have the given abilities.
     *
     * @param  array|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function cant($abilities, $arguments = [])
    {
        return !$this->can($abilities, $arguments);
    }

    /**
     * Determine if the entity does not have the given abilities.
     *
     * @param  array|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function cannot($abilities, $arguments = [])
    {
        return $this->cant($abilities, $arguments);
    }
}
