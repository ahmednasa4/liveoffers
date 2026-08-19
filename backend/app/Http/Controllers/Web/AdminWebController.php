<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LiveStream;
use App\Models\Offer;
use App\Models\Store;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Http\Request;

class AdminWebController extends Controller
{
    /**
     * Admin Dashboard - Platform metrics & overview.
     */
    public function dashboard()
    {
        $metrics = [
            'stores' => Store::count(),
            'active_stores' => Store::active()->count(),
            'pending_stores' => Store::where('is_active', false)->count(),
            'offers' => Offer::count(),
            'active_offers' => Offer::active()->count(),
            'live_streams' => LiveStream::active()->count(),
            'users' => User::count(),
            'categories' => Category::count(),
        ];

        $recentStores = Store::with('owner:id,username')->latest()->take(5)->get();
        $activeStreams = LiveStream::active()->with('store:id,name')->take(5)->get();

        return view('admin.dashboard', compact('metrics', 'recentStores', 'activeStreams'));
    }

    /**
     * Users management page.
     */
    public function users(Request $request)
    {
        $query = User::with('store:id,name');

        if ($request->filled('search')) {
            $query->where('username', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Toggle user active status.
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'لا يمكنك تعطيل حسابك الحالي.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $message = $user->is_active ? 'تم تفعيل المستخدم بنجاح.' : 'تم تعطيل المستخدم بنجاح.';
        return redirect()->back()->with('success', $message);
    }

    /**
     * Stores management page.
     */
    public function stores(Request $request)
    {
        $query = Store::with('owner:id,username,email,phone');

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
        }

        $stores = $query->latest()->paginate(15);

        return view('admin.stores.index', compact('stores'));
    }

    /**
     * Approve a store.
     */
    public function approveStore($id)
    {
        $store = Store::findOrFail($id);
        $store->update(['is_active' => true]);

        return redirect()->back()->with('success', 'تم اعتماد المتجر "' . $store->name . '" بنجاح.');
    }

    /**
     * Suspend a store.
     */
    public function suspendStore($id)
    {
        $store = Store::findOrFail($id);
        $store->update(['is_active' => false]);

        // End any active live streams for this store
        $store->liveStreams()->active()->update([
            'is_active' => false,
            'ended_at' => now(),
        ]);

        return redirect()->back()->with('success', 'تم تعطيل المتجر "' . $store->name . '" بنجاح.');
    }

    /**
     * Categories management page.
     */
    public function categories()
    {
        $categories = Category::withCount('subcategories')->ordered()->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a new category.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        Category::create($validated);

        return redirect()->back()->with('success', 'تم إنشاء القسم "' . $validated['name'] . '" بنجاح.');
    }

    /**
     * Update a category.
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'تم تحديث القسم بنجاح.');
    }

    /**
     * Delete a category.
     */
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $name = $category->name;
        $category->delete();

        return redirect()->back()->with('success', 'تم حذف القسم "' . $name . '" بنجاح.');
    }

    /**
     * Subcategories management page.
     */
    public function subcategories()
    {
        $subcategories = Subcategory::with('category:id,name')->latest()->paginate(15);
        $categories = Category::active()->ordered()->get();

        return view('admin.subcategories.index', compact('subcategories', 'categories'));
    }

    /**
     * Store a new subcategory.
     */
    public function storeSubcategory(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
        ]);

        Subcategory::create($validated);

        return redirect()->back()->with('success', 'تم إنشاء القسم الفرعي بنجاح.');
    }

    /**
     * Update a subcategory.
     */
    public function updateSubcategory(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        $subcategory->update($validated);

        return redirect()->back()->with('success', 'تم تحديث القسم الفرعي بنجاح.');
    }

    /**
     * Delete a subcategory.
     */
    public function deleteSubcategory($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->delete();

        return redirect()->back()->with('success', 'تم حذف القسم الفرعي بنجاح.');
    }

    /**
     * Live streams monitoring page.
     */
    public function liveStreams()
    {
        $activeStreams = LiveStream::active()
            ->with(['store:id,name,logo', 'store.owner:id,username,phone'])
            ->orderBy('started_at', 'desc')
            ->get();

        $recentStreams = LiveStream::with(['store:id,name'])
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        return view('admin.live-streams.index', compact('activeStreams', 'recentStreams'));
    }

    /**
     * Force end a live stream.
     */
    public function endLiveStream($id)
    {
        $stream = LiveStream::active()->findOrFail($id);
        $stream->update([
            'is_active' => false,
            'ended_at' => now(),
        ]);

        return redirect()->back()->with('success', 'تم إنهاء البث المباشر بنجاح.');
    }
}