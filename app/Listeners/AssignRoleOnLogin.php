<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignRoleOnLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Authenticated  $event
     * @return void
     */
    public function handle(Authenticated $event)
    {
        $user = $event->user;

        // Check if the user has any roles
        if (!$user->hasAnyRole(Role::all())) {
            // Assign a default role to the user
            $defaultRole = 'user'; // Replace with your default role name
            $user->assignRole($defaultRole);

            // Optionally, you can assign specific permissions to the role if not already assigned
            // This step can be omitted if the role already has the necessary permissions
            $role = Role::findByName($defaultRole);
            $permissions = ['view products', 'view categories']; // Example permissions
            $role->syncPermissions($permissions);
        }
    }
}
