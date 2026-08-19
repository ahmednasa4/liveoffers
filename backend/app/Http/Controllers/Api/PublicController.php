<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LiveStream;
use App\Models\Offer;
use App\Models\Store;
use App\Services\AgoraTokenService;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Get all active categories with subcategories.
     */
    public function categories()
    {
        $categories = Category::active()
            ->ordered()
            ->with(['subcategories' => function ($query) {
                $query->active();
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get active offers with pagination and filters.
     */
    public function offers(Request $request)
    {
        $query = Offer::active()
            ->with(['store:id,name,logo', 'category:id,name', 'subcategory:id,name']);

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->has('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->has('subcategory_id')) {
            $query->bySubcategory($request->subcategory_id);
        }

        if ($request->has('store_id')) {
            $query->byStore($request->store_id);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        $offers = $query->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $offers,
        ]);
    }

    /**
     * Get a single offer by ID (increments view count).
     */
    public function showOffer($id)
    {
        $offer = Offer::active()
            ->with(['store:id,name,logo,phone,whatsapp_number,address', 'category:id,name', 'subcategory:id,name'])
            ->findOrFail($id);

        $offer->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $offer,
        ]);
    }

    /**
     * Get all active live streams.
     */
    public function liveStreams()
    {
        $streams = LiveStream::active()
            ->with(['store:id,name,logo'])
            ->orderBy('started_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $streams,
        ]);
    }

    /**
     * Get a single live stream by ID (for joining as viewer).
     */
    public function showLiveStream($id)
    {
        $stream = LiveStream::active()
            ->with(['store:id,name,logo,phone,whatsapp_number'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $stream->id,
                'store_id' => $stream->store_id,
                'channel_name' => $stream->channel_name,
                'agora_token' => $stream->agora_token,
                'app_id' => config('services.agora.app_id'),
                'preview_image' => $stream->preview_image,
                'max_viewers' => $stream->max_viewers,
                'is_active' => $stream->is_active,
                'started_at' => $stream->started_at,
                'ended_at' => $stream->ended_at,
                'created_at' => $stream->created_at,
                'updated_at' => $stream->updated_at,
                'store' => $stream->store,
            ],
        ]);
    }

    /**
     * Mint a fresh subscriber (viewer) token for joining a live stream.
     * Public (no auth) — shoppers join anonymously.
     */
    public function viewerToken(Request $request, AgoraTokenService $agora, $id)
    {
        $stream = LiveStream::active()->findOrFail($id);

        // uid=0 lets the SDK assign a unique uid per viewer; the token is
        // minted as a Subscriber (role 2) so viewers can only subscribe.
        $token = $agora->generateToken($stream->channel_name, 0, 2, 3600);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'app_id' => config('services.agora.app_id'),
                'channel_name' => $stream->channel_name,
                'uid' => 0,
            ],
        ]);
    }

    /**
     * Get all active stores.
     */
    public function stores()
    {
        $stores = Store::active()
            ->withCount(['offers' => function ($query) {
                $query->active();
            }])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stores,
        ]);
    }

    /**
     * Get a single store by ID.
     */
    public function showStore($id)
    {
        $store = Store::active()
            ->with(['offers' => function ($query) {
                $query->active()->latest();
            }])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $store,
        ]);
    }
}