<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserWebController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'search']);
        $view = $request->input('view', 'active');

        $query = User::query()->with('roles');

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('mobile', 'like', '%' . $filters['search'] . '%');
            });
        }

        $users = $query->paginate($request->input('per_page', 10))->withQueryString();
        $roles = Role::all();

        if ($request->ajax()) {
            return view('admin.users._table', compact('users', 'view'))->render();
        }

        return view('admin.users.index', compact('users', 'roles', 'view'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|unique:users,mobile',
            'password' => 'required|string|min:8',
            'roles' => 'nullable|array',
            'status' => 'required|in:active,suspended,blocked',
        ]);

        $this->userService->createUser($validated);

        return redirect()->back()->with('success', 'User created successfully');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'mobile' => 'sometimes|string|unique:users,mobile,' . $user->id,
            'password' => 'nullable|string|min:8',
            'roles' => 'nullable|array',
            'status' => 'sometimes|in:active,suspended,blocked',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $this->userService->updateUser($user, $validated);

        return redirect()->back()->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User moved to trash');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->back()->with('success', 'User restored successfully');
    }

    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();
        return redirect()->back()->with('success', 'User permanently deleted');
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No items selected');
        }

        switch ($action) {
            case 'delete':
                User::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected users moved to trash';
                break;
            case 'restore':
                User::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected users restored';
                break;
            case 'force-delete':
                User::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected users permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
