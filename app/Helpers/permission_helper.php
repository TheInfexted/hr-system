<?php
// app/Helpers/permission_helper.php

if (!function_exists('has_permission')) {
    /**
     * Check if user has specific permission
     *
     * @param string $permission The permission to check
     * @return bool
     */
    function has_permission($permission) {
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
            
            // Get user-specific permissions
            $userPermission = $db->table('user_permissions')
                             ->where('user_id', $session->get('user_id'))
                             ->get()->getRow();
                             
            if ($userPermission) {
                $permissions = json_decode($userPermission->permissions, true) ?? [];
            } else {
                // Fall back to role permissions
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
        
        // For sub-accounts with company acknowledgment
        if ($session->get('role_id') == 3 && $session->get('active_company_id')) {
            // Check if the sub-account is properly acknowledged
            $acknowledgmentModel = new \App\Models\CompanyAcknowledgmentModel();
            $isAcknowledged = $acknowledgmentModel->isUserAcknowledged(
                $session->get('user_id'),
                $session->get('active_company_id')
            );
            
            if (!$isAcknowledged) {
                return false;
            }
            
            // If acknowledged and has the permission, allow access
            return isset($permissions[$permission]) && $permissions[$permission] === true;
        }
        
        // Standard permission check for other roles
        return isset($permissions[$permission]) && $permissions[$permission] === true;
    }
}

if (!function_exists('get_active_company_id')) {
    /**
     * Get the active company ID for the current user
     * 
     * @return int|null
     */
    function get_active_company_id() {
        $session = session();
        
        // For admin and company roles, use their company ID
        if ($session->get('role_id') == 1) {
            // Admins don't have a specific company
            return null;
        } elseif ($session->get('role_id') == 2) {
            // Company users have their own company ID
            return $session->get('company_id');
        } elseif ($session->get('role_id') == 3) {
            // Sub-accounts use their active company ID
            return $session->get('active_company_id');
        }
        
        return null;
    }
}