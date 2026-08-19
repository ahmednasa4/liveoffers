<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LiveStream;
use App\Models\Offer;
use App\Models\Store;
use App\Models\Subcategory;
use App\Services\AgoraTokenService;
use App\Services\GeminiAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreOwnerWebController extends Controller
{
    /**
     * Store Owner Dashboard - Overview & metrics.
     */
    public function dashboard()
    {
        $store = auth()->user()->store;

        if (!$store) {
            return redirect()->route('store.profile.edit')->with('info', 'يرجى إنشاء بيانات متجرك أولاً.');
        }

        $metrics = [
            'total_offers' => $store->offers()->count(),
            'active_offers' => $store->offers()->active()->count(),
            'total_views' => $store->offers()->sum('view_count'),
            'active_streams' => $store->liveStreams()->active()->count(),
            'total_streams' => $store->liveStreams()->count(),
        ];

        $recentOffers = $store->offers()->with(['category:id,name'])->latest()->take(5)->get();
        $activeStream = $store->liveStreams()->active()->first();

        return view('store.dashboard', compact('store', 'metrics', 'recentOffers', 'activeStream'));
    }

    /**
     * Show store profile edit form.
     */
    public function editStore()
    {
        $store = auth()->user()->store;

        return view('store.profile.edit', compact('store'));
    }

    /**
     * Update store profile.
     */
    public function updateStore(Request $request)
    {
        $store = auth()->user()->store;

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone' => 'required|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
        ]);

        if ($request->hasFile('logo')) {
            if ($store && $store->logo) {
                Storage::disk('public')->delete($store->logo);
            }
            $validated['logo'] = $request->file('logo')->store('stores/logos', 'public');
        }

        if ($store) {
            $store->update($validated);
            return redirect()->back()->with('success', 'تم تحديث بيانات المتجر بنجاح.');
        }

        // Create store if not exists
        $validated['owner_id'] = auth()->id();
        $validated['is_active'] = false;
        Store::create($validated);

        return redirect()->route('store.dashboard')->with('success', 'تم إنشاء المتجر بنجاح. سيتم تفعيله بعد مراجعة المشرف.');
    }

    /**
     * List store offers.
     */
    public function offersIndex(Request $request)
    {
        $store = auth()->user()->store;

        if (!$store) {
            return redirect()->route('store.profile.edit')->with('info', 'يرجى إنشاء بيانات متجرك أولاً.');
        }

        $query = $store->offers()->with(['category:id,name', 'subcategory:id,name']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $offers = $query->latest()->paginate(12);

        return view('store.offers.index', compact('offers'));
    }

    /**
     * Show create offer form.
     */
    public function createOffer()
    {
        $store = auth()->user()->store;

        if (!$store) {
            return redirect()->route('store.profile.edit')->with('info', 'يرجى إنشاء بيانات متجرك أولاً.');
        }

        $categories = Category::active()->ordered()->get();
        $subcategories = Subcategory::active()->get()->groupBy('category_id');

        return view('store.offers.create', compact('categories', 'subcategories'));
    }

    /**
     * Store a new offer.
     */
    public function storeOffer(Request $request)
    {
        $store = auth()->user()->store;

        if (!$store) {
            return redirect()->route('store.profile.edit')->with('info', 'يرجى إنشاء بيانات متجرك أولاً.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'original_price' => 'required|numeric|min:0',
            'offer_price' => 'required|numeric|min:0|lte:original_price',
            'image' => 'required|image|max:2048',
            'is_featured' => 'boolean',
            'is_ai_generated' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $validated['store_id'] = $store->id;
        $validated['image'] = $request->file('image')->store('offers/images', 'public');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_ai_generated'] = $request->boolean('is_ai_generated');

        Offer::create($validated);

        return redirect()->route('store.offers.index')->with('success', 'تم إنشاء العرض بنجاح.');
    }

    /**
     * Show edit offer form.
     */
    public function editOffer($id)
    {
        $store = auth()->user()->store;

        if (!$store) {
            return redirect()->route('store.profile.edit')->with('info', 'يرجى إنشاء بيانات متجرك أولاً.');
        }

        $offer = $store->offers()->findOrFail($id);
        $categories = Category::active()->ordered()->get();
        $subcategories = Subcategory::active()->get()->groupBy('category_id');

        return view('store.offers.edit', compact('offer', 'categories', 'subcategories'));
    }

    /**
     * Update an offer.
     */
    public function updateOffer(Request $request, $id)
    {
        $store = auth()->user()->store;
        $offer = $store->offers()->findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'original_price' => 'required|numeric|min:0',
            'offer_price' => 'required|numeric|min:0|lte:original_price',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($request->hasFile('image')) {
            if ($offer->image) {
                Storage::disk('public')->delete($offer->image);
            }
            $validated['image'] = $request->file('image')->store('offers/images', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        $offer->update($validated);

        return redirect()->route('store.offers.index')->with('success', 'تم تحديث العرض بنجاح.');
    }

    /**
     * Delete an offer.
     */
    public function deleteOffer($id)
    {
        $store = auth()->user()->store;
        $offer = $store->offers()->findOrFail($id);

        if ($offer->image) {
            Storage::disk('public')->delete($offer->image);
        }

        $offer->delete();

        return redirect()->back()->with('success', 'تم حذف العرض بنجاح.');
    }

    // /**
    //  * Generate AI description via Gemini (AJAX endpoint).
    //  */
    // public function generateDescription(Request $request, GeminiAIService $gemini)
    // {
    //     //return "222";
    //     $request->validate([
    //         'image' => 'required|image|max:4096',
    //         'title' => 'required|string|max:200',
    //         'offer_price' => 'required|numeric|min:0',
    //     ]);

    //     try {
    //         $imagePath = $request->file('image')->getRealPath();
    //         $imageData = base64_encode(file_get_contents($imagePath));
    //         $mimeType = $request->file('image')->getMimeType();

    //         $description = $gemini->generateDescription(
    //             $imageData,
    //             $mimeType,
    //             $request->title,
    //             $request->offer_price
    //         );

    //         return response()->json([
    //             'success' => true,
    //             'description' => $description,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'فشل توليد الوصف: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }

   public function generateDescription(Request $request, GeminiAIService $gemini): \Illuminate\Http\JsonResponse
{
    $validated = $request->validate([
        'image' => 'required|image|max:4096', // ✅ يستقبل الملف كالمعتاد
        'title' => 'required|string|max:200',
        'offer_price' => 'required|numeric|min:0',
    ]);

    try {
        $file = $request->file('image');
        $imageData = base64_encode(file_get_contents($file->getRealPath()));
        $mimeType = $file->getMimeType();

        $description = $gemini->generateDescription(
            $imageData,
            $mimeType,
            $validated['title'],
            (float) $validated['offer_price']
        );

        return response()->json([
            'success' => true,
            'description' => $description,
        ]);
    } catch (\Exception $e) {
        \Log::error('AI Generation failed', ['message' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'فشل توليد الوصف: ' . $e->getMessage(),
        ], 500);
    }
}

    /**
     * List live streams for the store.
     */
    public function liveStreamsIndex()
    {
        $store = auth()->user()->store;

        if (!$store) {
            return redirect()->route('store.profile.edit')->with('info', 'يرجى إنشاء بيانات متجرك أولاً.');
        }

        $streams = $store->liveStreams()->latest()->paginate(15);
        $activeStream = $store->liveStreams()->active()->first();

        return view('store.live-streams.index', compact('streams', 'activeStream'));
    }

    /**
     * Show broadcast page.
     */
    public function broadcast()
    {
        $store = auth()->user()->store;

        if (!$store) {
            return redirect()->route('store.profile.edit')->with('info', 'يرجى إنشاء بيانات متجرك أولاً.');
        }

        $activeStream = $store->liveStreams()->active()->first();
        $agoraAppId = config('services.agora.app_id');

        return view('store.live-streams.broadcast', compact('store', 'activeStream', 'agoraAppId'));
    }

    /**
     * Start a live stream (generate Agora token).
     */
    public function startLiveStream(Request $request, AgoraTokenService $agora)
    {
        $store = auth()->user()->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى إنشاء بيانات متجرك أولاً.',
            ], 404);
        }

        // End any existing active stream
        $existing = $store->liveStreams()->active()->first();
        if ($existing) {
            $existing->update([
                'is_active' => false,
                'ended_at' => now(),
            ]);
        }

        $channelName = 'store_' . $store->id . '_' . time();
        $token = $agora->generateToken($channelName, 0);

        $stream = LiveStream::create([
            'store_id' => $store->id,
            'channel_name' => $channelName,
            'agora_token' => $token,
            'is_active' => true,
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم بدء البث المباشر بنجاح.',
            'data' => [
                'stream_id' => $stream->id,
                'channel_name' => $channelName,
                'token' => $token,
                'app_id' => config('services.agora.app_id'),
            ],
        ]);
    }

    /**
     * End a live stream.
     */
    public function endLiveStream($id)
    {
        $store = auth()->user()->store;

        // Find by id within the store's streams (not restricted to active),
        // so ending an already-ended stream is a safe no-op instead of a 404.
        $stream = $store->liveStreams()->findOrFail($id);

        if ($stream->is_active) {
            $stream->update([
                'is_active' => false,
                'ended_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'تم إنهاء البث المباشر بنجاح.');
    }
}