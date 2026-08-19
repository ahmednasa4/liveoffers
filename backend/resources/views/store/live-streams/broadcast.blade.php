@extends('layouts.store')

@section('title', 'البث المباشر')

@push('scripts')
<!-- Agora RTC SDK via CDN -->
<script src="https://download.agora.io/sdk/release/AgoraRTC_N.js"></script>
@endpush

@section('content-body')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">البث المباشر</h1>
        <p class="text-sm text-slate-500 mt-1">ابدأ بثك المباشر واصل متجرك مع المتسوقين</p>
    </div>
    <a href="{{ route('store.live-streams.index') }}" class="text-slate-600 hover:text-slate-800 px-4 py-2 font-semibold text-sm">سجل البث</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Video Preview -->
    <div class="lg:col-span-2">
        <div class="bg-slate-900 rounded-xl overflow-hidden shadow-lg relative aspect-video">
            <!-- Local Video Container -->
            <div id="local-video" class="absolute inset-0 flex items-center justify-center">
                <div id="pre-broadcast" class="text-center text-slate-400">
                    <svg class="w-20 h-20 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <p class="text-lg font-semibold">معاينة الكاميرا</p>
                    <p class="text-sm mt-1">اضغط "بدء البث" لتفعيل الكاميرا</p>
                </div>
            </div>

            <!-- Live Badge -->
            <div id="live-badge" class="hidden absolute top-4 right-4 bg-rose-600 text-white px-3 py-1.5 rounded-full text-sm font-bold flex items-center gap-2">
                <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                مباشر
            </div>

            <!-- Controls -->
            <div id="broadcast-controls" class="hidden absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-3 bg-slate-800/80 backdrop-blur px-4 py-2.5 rounded-full">
                <button id="mute-btn" class="w-10 h-10 bg-slate-700 hover:bg-slate-600 rounded-full flex items-center justify-center text-white transition" title="كتم الصوت">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                </button>
                <button id="video-btn" class="w-10 h-10 bg-slate-700 hover:bg-slate-600 rounded-full flex items-center justify-center text-white transition" title="إيقاف الفيديو">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
                <button id="switch-camera-btn" class="w-10 h-10 bg-slate-700 hover:bg-slate-600 rounded-full flex items-center justify-center text-white transition disabled:opacity-40 disabled:cursor-not-allowed" title="تبديل الكاميرا" disabled>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h3l2-3h6l2 3h3a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V8a1 1 0 011-1zm5 7a3 3 0 116 0 3 3 0 01-6 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 10l2-2m0 0l-2 .01M19 8l-.01 2"/></svg>
                </button>
                <button id="end-btn" class="w-10 h-10 bg-rose-600 hover:bg-rose-700 rounded-full flex items-center justify-center text-white transition" title="إنهاء البث">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2M3 21l18-9M3 12l18-9M3 18l18-9"/></svg>
                </button>
            </div>
        </div>

        <!-- Start Button -->
        <div id="start-container" class="mt-4 flex justify-center">
            <button id="start-broadcast-btn" class="bg-rose-600 hover:bg-rose-700 text-white px-8 py-3 rounded-lg font-bold text-lg transition flex items-center gap-3 shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                بدء البث المباشر
            </button>
        </div>
    </div>

    <!-- Stream Info Sidebar -->
    <div class="lg:col-span-1 space-y-4">
        <!-- Store Info -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="font-bold text-slate-700 mb-3">معلومات المتجر</h3>
            <div class="flex items-center gap-3 mb-3">
                @if($store->logo)
                <img src="{{ Storage::url($store->logo) }}" class="w-12 h-12 rounded-full object-cover" alt="{{ $store->name }}">
                @else
                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-lg">{{ mb_substr($store->name, 0, 1) }}</div>
                @endif
                <div>
                    <p class="font-semibold text-slate-800">{{ $store->name }}</p>
                    <p class="text-xs text-slate-500">{{ $store->address }}</p>
                </div>
            </div>
            <div class="text-sm space-y-1">
                <p class="text-slate-500">الحالة: 
                    @if($store->is_active)
                    <span class="text-emerald-600 font-semibold">معتمد</span>
                    @else
                    <span class="text-amber-600 font-semibold">بانتظار الاعتماد</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Stream Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="font-bold text-slate-700 mb-3">إحصائيات البث</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">المشاهدون الآن</span>
                    <span id="viewer-count" class="text-lg font-bold text-slate-800">0</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">أقصى مشاهدين</span>
                    <span id="max-viewers" class="text-lg font-bold text-slate-800">0</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">مدة البث</span>
                    <span id="broadcast-duration" class="text-lg font-bold text-slate-800">00:00</span>
                </div>
            </div>
        </div>

        <!-- Active Stream -->
        @if($activeStream)
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <p class="font-bold text-emerald-800">بث نشط</p>
            </div>
            <p class="text-xs text-emerald-600 mb-3">القناة: <span class="font-mono">{{ $activeStream->channel_name }}</span></p>
            <form action="{{ route('store.live-streams.end', $activeStream->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إنهاء البث؟')">
                @csrf
                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition">إنهاء البث</button>
            </form>
        </div>
        @endif

        <!-- Tips -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
            <h3 class="font-bold text-blue-800 mb-2 text-sm">نصائح للبث ناجح</h3>
            <ul class="text-xs text-blue-700 space-y-1.5 list-disc list-inside">
                <li>تأكد من إضاءة جيدة قبل البدء</li>
                <li>اعرض المنتجات بوضوح وأشر إليها</li>
                <li>تحدث بوضوح وحماس</li>
                <li>تفاعل مع المشاهدين</li>
            </ul>
        </div>
    </div>
</div>

<!-- Error/Status Messages -->
<div id="status-msg" class="hidden fixed bottom-6 right-6 px-5 py-3 rounded-lg shadow-lg z-50"></div>

<script>
const AGORA_APP_ID = '{{ $agoraAppId }}';
const START_URL = '{{ route("store.live-streams.start") }}';
const END_URL = '{{ route("store.live-streams.index") }}';
const END_STREAM_URL = '{{ route("store.live-streams.end", ["id" => "__ID__"]) }}';
const CSRF_TOKEN = '{{ csrf_token() }}';
const ACTIVE_STREAM_ID = @if($activeStream) {{ $activeStream->id }} @else null @endif;

let client = null;
let localAudioTrack = null;
let localVideoTrack = null;
let isBroadcasting = false;
let isMuted = false;
let isVideoOff = false;
let broadcastStartTime = null;
let durationTimer = null;
let viewerCount = 0;
let maxViewers = 0;
let currentStreamId = ACTIVE_STREAM_ID;
let cameraDevices = [];
let currentCameraIndex = 0;
// Mobile browsers expose front/back cameras via facingMode, not stable deviceIds —
// deviceId/setDevice switching is unreliable there. Detect a touch device once and
// toggle facingMode on it instead. See switch-camera-btn handler.
const isMobile = matchMedia('(pointer: coarse)').matches || navigator.maxTouchPoints > 0;
let currentFacingMode = 'user';

// Start Broadcast
document.getElementById('start-broadcast-btn').addEventListener('click', async () => {
    const btn = document.getElementById('start-broadcast-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> جاري البدء...';

    try {
        // 1. Get token from server
        const response = await fetch(START_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'فشل بدء البث');
        }

        currentStreamId = data.data.stream_id;

        // 2. Initialize Agora client
        client = AgoraRTC.createClient({ mode: 'live', codec: 'vp8' });
        client.setClientRole('host');

        // 3. Join channel
        await client.join(data.data.app_id, data.data.channel_name, data.data.token, null);

        // 4. Create tracks (camera + mic). Pin the initial camera to facingMode
        //    'user' (front) so we know our starting point — mobile relies on
        //    facingMode for the front/back toggle, since deviceId-based switching
        //    is unreliable there.
        [localAudioTrack, localVideoTrack] = await AgoraRTC.createMicrophoneAndCameraTracks(
            {},
            { facingMode: 'user' }
        );

        // 5. Play local video (Agora appends its own child element; no need to clear)
        localVideoTrack.play('local-video');

        // 6. Publish tracks
        await client.publish([localAudioTrack, localVideoTrack]);

        // 6a. Enable camera switching. On phones we toggle facingMode (front/back)
        //     via track recreation; on desktop we cycle deviceId from enumerateDevices.
        if (isMobile) {
            currentFacingMode = 'user';
            document.getElementById('switch-camera-btn').disabled = false;
        } else {
            try {
                cameraDevices = await AgoraRTC.getCameras();
                currentCameraIndex = cameraDevices.findIndex(d => d.deviceId === localVideoTrack.getDeviceId());
                if (currentCameraIndex < 0) currentCameraIndex = 0;
                if (cameraDevices.length > 1) {
                    document.getElementById('switch-camera-btn').disabled = false;
                }
            } catch (e) {
                console.warn('Could not enumerate cameras:', e);
            }
        }

        // 7. Update UI
        isBroadcasting = true;
        broadcastStartTime = Date.now();
        document.getElementById('pre-broadcast').classList.add('hidden');
        document.getElementById('live-badge').classList.remove('hidden');
        document.getElementById('broadcast-controls').classList.remove('hidden');
        document.getElementById('start-container').classList.add('hidden');

        // 8. Start duration timer
        durationTimer = setInterval(updateDuration, 1000);

        // 9. Listen for remote users (viewers)
        client.on('user-published', (user, mediaType) => {
            client.subscribe(user, mediaType).then(() => {
                if (mediaType === 'video') {
                    viewerCount++;
                    updateViewerCount();
                }
            });
        });

        client.on('user-unpublished', (user) => {
            viewerCount = Math.max(0, viewerCount - 1);
            updateViewerCount();
        });

        showStatus('تم بدء البث المباشر بنجاح!', 'success');

    } catch (err) {
        showStatus('خطأ: ' + err.message, 'error');
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> بدء البث المباشر';
    }
});

// Mute/Unmute
document.getElementById('mute-btn').addEventListener('click', () => {
    if (!localAudioTrack) return;
    isMuted = !isMuted;
    if (isMuted) {
        localAudioTrack.setEnabled(false);
    } else {
        localAudioTrack.setEnabled(true);
    }
    document.getElementById('mute-btn').classList.toggle('bg-rose-600', isMuted);
});

// Video On/Off
document.getElementById('video-btn').addEventListener('click', () => {
    if (!localVideoTrack) return;
    isVideoOff = !isVideoOff;
    if (isVideoOff) {
        localVideoTrack.setEnabled(false);
    } else {
        localVideoTrack.setEnabled(true);
    }
    document.getElementById('video-btn').classList.toggle('bg-rose-600', isVideoOff);
});

// Switch Camera. On phones toggle facingMode (front/back) by recreating the
// video track — deviceId/setDevice is unreliable on mobile browsers. On
// desktop, cycle through enumerated webcams via setDevice.
document.getElementById('switch-camera-btn').addEventListener('click', async () => {
    if (!localVideoTrack) return;

    if (isMobile) {
        const nextFacing = currentFacingMode === 'user' ? 'environment' : 'user';
        const oldTrack = localVideoTrack;
        try {
            // iOS only allows one camera active at a time: stop the old track
            // before requesting the new one, otherwise getUserMedia rejects.
            try { await client.unpublish(oldTrack); } catch (e) { /* ignore */ }
            oldTrack.close();
            localVideoTrack = null;

            const newTrack = await AgoraRTC.createCameraVideoTrack({
                facingMode: nextFacing,
            });
            newTrack.play('local-video');
            await client.publish([newTrack]);
            localVideoTrack = newTrack;
            currentFacingMode = nextFacing;
            showStatus(nextFacing === 'environment' ? 'تم التبديل إلى الكاميرا الخلفية' : 'تم التبديل إلى الكاميرا الأمامية', 'success');
        } catch (e) {
            // Switch failed — try to restore the original camera so the host
            // isn't left without a video feed.
            try {
                const restored = await AgoraRTC.createCameraVideoTrack({ facingMode: currentFacingMode });
                restored.play('local-video');
                await client.publish([restored]);
                localVideoTrack = restored;
            } catch (e2) {
                console.error('Could not restore camera after failed switch:', e2);
            }
            showStatus('تعذر تبديل الكاميرا: ' + e.message, 'error');
        }
        return;
    }

    // Desktop: cycle deviceId.
    if (cameraDevices.length < 2) return;
    currentCameraIndex = (currentCameraIndex + 1) % cameraDevices.length;
    const nextDevice = cameraDevices[currentCameraIndex];
    try {
        await localVideoTrack.setDevice(nextDevice.deviceId);
        showStatus('تم تبديل الكاميرا إلى: ' + (nextDevice.label || 'كاميرا ' + (currentCameraIndex + 1)), 'success');
    } catch (e) {
        showStatus('تعذر تبديل الكاميرا: ' + e.message, 'error');
    }
});

// End Broadcast
document.getElementById('end-btn').addEventListener('click', async () => {
    if (!confirm('هل أنت متأكد من إنهاء البث؟')) return;

    try {
        if (client) {
            if (localAudioTrack) localAudioTrack.close();
            if (localVideoTrack) localVideoTrack.close();
            await client.leave();
        }
    } catch (e) {
        console.error('Error leaving:', e);
    }

    clearInterval(durationTimer);

    // Tell the server to end the stream so the DB stays in sync
    if (currentStreamId) {
        try {
            await fetch(END_STREAM_URL.replace('__ID__', currentStreamId), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            });
        } catch (e) {
            console.error('Error ending stream on server:', e);
        }
    }

    window.location.href = END_URL;
});

// Update duration
function updateDuration() {
    const elapsed = Math.floor((Date.now() - broadcastStartTime) / 1000);
    const mins = String(Math.floor(elapsed / 60)).padStart(2, '0');
    const secs = String(elapsed % 60).padStart(2, '0');
    document.getElementById('broadcast-duration').textContent = `${mins}:${secs}`;
}

// Update viewer count
function updateViewerCount() {
    document.getElementById('viewer-count').textContent = viewerCount;
    if (viewerCount > maxViewers) {
        maxViewers = viewerCount;
        document.getElementById('max-viewers').textContent = maxViewers;
    }
}

// Show status message
function showStatus(msg, type) {
    const el = document.getElementById('status-msg');
    el.textContent = msg;
    el.className = `fixed bottom-6 right-6 px-5 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'
    }`;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 4000);
}
</script>
@endsection