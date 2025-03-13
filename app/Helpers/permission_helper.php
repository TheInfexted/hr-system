<?php
// app/Helpers/permission_helper.php

if (!function_exists('has_permission')) {
    /**
     * Check if user has specific permission
     *
     * @param string $permission The permission to check
     * @return bool
     */
    function has_permission($permission)
    {
        $session = session();
        
        // Admin role always has all permissions
        if ($session->get('role_id') == 1) {
            return true;
        }
        
        // Get user permissions from session
        $permissions = $session->get('permissions');
        
        // If permissions isn't in session, load from database
        if (empty($permissions)) {
            $db = \Config\Database::connect();
            
            // Try to get user-specific permissions first
            $userPermission = $db->table('user_permissions')
                              ->where('user_id', $session->get('user_id'))
                              ->get()->getRow();
                              
            if ($userPermission) {
                $permissions = json_decode($userPermission->permissions, true) ?? [];
            } else {
                // Fall back to role permissions if no user-specific ones exist
                $role = $db->table('roles')->where('id', $session->get('role_id'))->get()->getRow();
                
                if ($role) {
                    $permissions = json_decode($role->permissions, true) ?? [];
                }
            }
            
            // Store in session
            if (!empty($permissions)) {
                $session->set('permissions', $permissions);
            } else {
                return false;
            }
        }
        
        // Check if user has 'all' permission
        if (isset($permissions['all']) && $permissions['all'] === true) {
            return true;
        }
        
        // Check specific permission
        return isset($permissions[$permission]) && $permissions[$permission] === true;
    }
}