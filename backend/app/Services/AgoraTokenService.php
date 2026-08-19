<?php

namespace App\Services;

use App\Services\Agora\RtcTokenBuilder2;
use Illuminate\Support\Facades\Log;

class AgoraTokenService
{
    private string $appId;

    private string $appCertificate;

    public function __construct()
    {
        $this->appId = config('services.agora.app_id');
        $this->appCertificate = config('services.agora.app_certificate');
    }

    /**
     * Generate an Agora RTC AccessToken2 (version 007) token for a channel.
     *
     * @param  string  $channelName  The channel name
     * @param  int  $uid  User ID (0 to disable uid authentication)
     * @param  int  $role  Role: 1 = Publisher (host), 2 = Subscriber (audience)
     * @param  int  $expireTime  Token & privilege validity in seconds (max 86400)
     */
    public function generateToken(string $channelName, int $uid = 0, int $role = 1, int $expireTime = 3600): string
    {
        if (empty($this->appId) || empty($this->appCertificate)) {
            Log::warning('Agora credentials not configured. Returning empty token.');

            return '';
        }

        $roleEnum = $role === 1
            ? RtcTokenBuilder2::ROLE_PUBLISHER
            : RtcTokenBuilder2::ROLE_SUBSCRIBER;

        // AccessToken2 packs uid as a string; a numeric uid is sent as-is.
        $token = RtcTokenBuilder2::buildTokenWithUid(
            $this->appId,
            $this->appCertificate,
            $channelName,
            $uid,
            $roleEnum,
            $expireTime,   // token expire (seconds from now)
            $expireTime    // privilege expire (seconds from now)
        );

        if ($token === '') {
            Log::error('Agora token generation failed (empty result). Check App ID / App Certificate format.');
        }

        return $token;
    }
}