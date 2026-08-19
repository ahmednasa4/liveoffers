<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LiveStream;
use App\Models\Offer;
use App\Models\Store;
use App\Services\AgoraTokenService;
use App\Services\GeminiAIService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class StoreOwnerController extends Controller
{
    /**
     * Get the authenticated store owner's store.
     */
    public function myStore(Request $request)
    {
        $store = $request->user()->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم إنشاء متجر بعد.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $store,
        ]);
    }

    /**
     * Create a new store for the authenticated store owner.
     */
    public function createStore(Request $request)
    {
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

        if ($request->user()->store) {
            return response()->json([
                'success' => false,
                'message' => 'لديك متجر بالفعل.',
            ], Response::HTTP_CONFLICT);
        }

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('stores/logos', 'public');
        }

        $validated['owner_id'] = $request->user()->id;
        $validated['is_active'] = false;

        $store = Store::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المتجر بنجاح. سيتم تفعيله بعد مراجعة المشرف.',
            'data' => $store,
        ], Response::HTTP_CREATED);
    }

    /**
     * Update the authenticated store owner's store.
     */
    public function updateStore(Request $request)
    {
        $store = $request->user()->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم إنشاء متجر بعد.',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:150',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'address' => 'sometimes|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone' => 'sometimes|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
        ]);

        if ($request->hasFile('logo')) {
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }
            $validated['logo'] = $request->file('logo')->store('stores/logos', 'public');
        }

        $store->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات المتجر بنجاح.',
            'data' => $store->fresh(),
        ]);
    }

    /**
     * Get all offers for the authenticated store owner's store.
     */
    public function myOffers(Request $request)
    {
        $store = $request->user()->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم إنشاء متجر بعد.',
            ], Response::HTTP_NOT_FOUND);
        }

        $offers = $store->offers()
            ->with(['category:id,name', 'subcategory:id,name'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $offers,
        ]);
    }

    /**
     * Create a new offer for the authenticated store owner's store.
     */
    public function createOffer(Request $request)
    {
        $store = $request->user()->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم إنشاء متجر بعد.',
            ], Response::HTTP_NOT_FOUND);
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
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $validated['store_id'] = $store->id;
        $validated['image'] = $request->file('image')->store('offers/images', 'public');

        $offer = Offer::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء العرض بنجاح.',
            'data' => $offer,
        ], Response::HTTP_CREATED);
    }

    /**
     * Update an existing offer.
     */
    public function updateOffer(Request $request, $id)
    {
        $store = $request->user()->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم إنشاء متجر بعد.',
            ], Response::HTTP_NOT_FOUND);
        }

        $offer = $store->offers()->findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'title' => 'sometimes|string|max:200',
            'description' => 'sometimes|string',
            'original_price' => 'sometimes|numeric|min:0',
            'offer_price' => 'sometimes|numeric|min:0|lte:original_price',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
        ]);

        if ($request->hasFile('image')) {
            if ($offer->image) {
                Storage::disk('public')->delete($offer->image);
            }
            $validated['image'] = $request->file('image')->store('offers/images', 'public');
        }

        $offer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث العرض بنجاح.',
            'data' => $offer->fresh(),
        ]);
    }

    /**
     * Delete an offer.
     */
    public function deleteOffer(Request $request, $id)
    {
        $store = $request->user()->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم إنشاء متجر بعد.',
            ], Response::HTTP_NOT_FOUND);
        }

        $offer = $store->offers()->findOrFail($id);

        if ($offer->image) {
            Storage::disk('public')->delete($offer->image);
        }

        $offer->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف العرض بنجاح.',
        ]);
    }

    /**
     * Generate an AI product description from an image.
     */
    public function generateAiDescription(Request $request, GeminiAIService $gemini)
    {
        $request->validate([
            'image' => 'required|image|max:4096',
            'title' => 'required|string|max:200',
            'offer_price' => 'required|numeric|min:0',
        ]);

        $imagePath = $request->file('image')->getRealPath();
        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = $request->file('image')->getMimeType();

        $description = $gemini->generateDescription(
            $imageData,
            $mimeType,
            $request->title,
            $request->offer_price
        );

        return response()->json([
            'success' => true,
            'data' => [
                'description' => $description,
            ],
        ]);
    }

    /**
     * Start a live broadcast (generate Agora token).
     */
    public function startLiveStream(Request $request, AgoraTokenService $agora)
    {
        $store = $request->user()->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم إنشاء متجر بعد.',
            ], Response::HTTP_NOT_FOUND);
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
            'data' => $stream,
        ], Response::HTTP_CREATED);
    }

    /**
     * End a live broadcast.
     */
    public function endLiveStream(Request $request, $id)
    {
        $store = $request->user()->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم إنشاء متجر بعد.',
            ], Response::HTTP_NOT_FOUND);
        }

        // Find by id within the store's streams (not restricted to active),
        // so ending an already-ended stream is a safe no-op instead of a 404.
        $stream = $store->liveStreams()->findOrFail($id);

        if ($stream->is_active) {
            $stream->update([
                'is_active' => false,
                'ended_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إنهاء البث المباشر بنجاح.',
        ]);
    }
}