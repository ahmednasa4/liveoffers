<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LiveStream;
use App\Models\Offer;
use App\Models\Store;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminController extends Controller
{
    /**
     * Get all stores (pending and active).
     */
    public function stores(Request $request)
    {
        $query = Store::with('owner:id,username,email,phone');

        if ($request->has('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $stores = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $stores,
        ]);
    }

    /**
     * Approve/activate a store.
     */
    public function approveStore($id)
    {
        $store = Store::findOrFail($id);
        $store->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'تم تفعيل المتجر بنجاح.',
            'data' => $store->fresh(),
        ]);
    }

    /**
     * Suspend/deactivate a store.
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

        return response()->json([
            'success' => true,
            'message' => 'تم تعطيل المتجر بنجاح.',
            'data' => $store->fresh(),
        ]);
    }

    /**
     * Get all users.
     */
    public function users()
    {
        $users = User::with('store:id,name')->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Toggle user active status.
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'message' => $user->is_active ? 'تم تفعيل المستخدم.' : 'تم تعطيل المستخدم.',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Get all categories.
     */
    public function categories()
    {
        $categories = Category::withCount('subcategories')
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Create a new category.
     */
    public function createCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء القسم بنجاح.',
            'data' => $category,
        ], Response::HTTP_CREATED);
    }

    /**
     * Update a category.
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'sometimes|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث القسم بنجاح.',
            'data' => $category->fresh(),
        ]);
    }

    /**
     * Delete a category.
     */
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف القسم بنجاح.',
        ]);
    }

    /**
     * Create a new subcategory.
     */
    public function createSubcategory(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
        ]);

        $subcategory = Subcategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء القسم الفرعي بنجاح.',
            'data' => $subcategory,
        ], Response::HTTP_CREATED);
    }

    /**
     * Update a subcategory.
     */
    public function updateSubcategory(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:100',
            'is_active' => 'boolean',
        ]);

        $subcategory->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث القسم الفرعي بنجاح.',
            'data' => $subcategory->fresh(),
        ]);
    }

    /**
     * Delete a subcategory.
     */
    public function deleteSubcategory($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف القسم الفرعي بنجاح.',
        ]);
    }

    /**
     * Get all active live streams (for oversight).
     */
    public function liveStreams()
    {
        $streams = LiveStream::active()
            ->with(['store:id,name,logo', 'store.owner:id,username,phone'])
            ->orderBy('started_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $streams,
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'تم إنهاء البث المباشر بنجاح.',
        ]);
    }

    /**
     * Get platform metrics.
     */
    public function metrics()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'stores' => Store::count(),
                'active_stores' => Store::active()->count(),
                'offers' => Offer::count(),
                'active_offers' => Offer::active()->count(),
                'live_streams' => LiveStream::active()->count(),
                'users' => User::count(),
                'categories' => Category::count(),
            ],
        ]);
    }
}