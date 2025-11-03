<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $roles = Role::all();
        return view("role.index")->with('roles',$roles);
    }

    public function save()
    {
            return view("role.save");
    }
    public function edit($id){
        $permissions = Permission::where('role_id', $id)->pluck('routes')->toArray();
        $roles = Role::find($id);
        return view('role.edit', compact(['permissions', 'roles', 'id']));

    }
    public function store(Request $request)
    {
        // Validate role name
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $permission = $request->all();

        // Create role
        $roles = Role::create([
            'role_name' => $request->input('name'),
        ]);
        $roleId = $roles->id;

        // Create permissions
        foreach ($permission as $key => $data) {
            if (is_array($data)) {
                foreach ($data as $value) {
                    // Ensure values are strings
                    Permission::create([
                        'role_id' => (int) $roleId,
                        'permission' => (string) $key,
                        'routes' => (string) $value
                    ]);
                }
            }
        }

        return redirect('role')->with('success', 'Role created successfully');
    }
    public function update(Request $request, $id)
    {
        // Validate role name
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $permission = $request->all();
        $roleHasPermissions = Permission::where('role_id', $id)->pluck('routes')->toArray();
        $chkPermissionArr = [];
        
        // Update role
        $roles = Role::find($id);
        if ($roles) {
            $roles->role_name = $request->input('name');
            $roles->save();
        }
        
        $roleId = (int) $id;
        
        // Add new permissions
        foreach ($permission as $key => $data) {
            if (is_array($data)) {
                foreach ($data as $value) {
                    array_push($chkPermissionArr, $value);
                    if (!in_array($value, $roleHasPermissions)) {
                        // Ensure values are strings
                        Permission::create([
                            'role_id' => $roleId,
                            'permission' => (string) $key,
                            'routes' => (string) $value
                        ]);
                    }
                }
            }
        }
        
        // Remove old permissions that are no longer selected
        for ($i = 0; $i < count($roleHasPermissions); $i++) {
            if (!in_array($roleHasPermissions[$i], $chkPermissionArr)) {
                $permissionToDelete = Permission::where('routes', $roleHasPermissions[$i])
                    ->where('role_id', $roleId);
                if ($permissionToDelete) {
                    $permissionToDelete->delete();
                }
            }
        }
 
        return redirect('role')->with('success', 'Role updated successfully');
    }

    public function delete($id){
        $permissions = Permission::where('role_id', $id);
        if ($permissions) {
            $permissions->delete();
        }
        $id = json_decode($id);

        if (is_array($id)) {

            for ($i = 0; $i < count($id); $i++) {
                $roles = Role::find($id[$i]);
                $roles->delete();
            }

        } else {
            $roles = Role::find($id);
            $roles->delete();
        }

        return redirect()->back();
    }

}
