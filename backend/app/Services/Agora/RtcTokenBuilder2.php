<?php

namespace App\Services\Agora;

/**
 * Build Agora RTC AccessToken2 (version 007) tokens.
 *
 * Ported from AgoraIO/Tools DynamicKey/AgoraDynamicKey/php/src/RtcTokenBuilder2.php.
 */
class RtcTokenBuilder2
{
    /**
     * RECOMMENDED. Use this role for a voice/video call or a live broadcast, if
     * your scenario does not require authentication for Co-host token authentication.
     */
    const ROLE_PUBLISHER = 1;

    /**
     * Only use this role if your scenario requires authentication for Co-host.
     * In order for this role to take effect, please contact our support team
     * to enable authentication for Hosting-in for you. Otherwise, Role_Subscriber
     * still has the same privileges as Role_Publisher.
     */
    const ROLE_SUBSCRIBER = 2;

    /**
     * Build the RTC token with uid.
     *
     * @param  string  $appId             The App ID issued by Agora.
     * @param  string  $appCertificate    The App Certificate registered in the Agora Dashboard.
     * @param  string  $channelName       Unique channel name for the AgoraRTC session.
     * @param  int|string  $uid           User ID (1 to 2^32-1). 0 disables uid authentication.
     * @param  int  $role                 ROLE_PUBLISHER (host) or ROLE_SUBSCRIBER (audience).
     * @param  int  $tokenExpire          Token validity in seconds from now (max 86400).
     * @param  int  $privilegeExpire      Privilege validity in seconds from now.
     * @return string The RTC token.
     */
    public static function buildTokenWithUid($appId, $appCertificate, $channelName, $uid, $role, $tokenExpire, $privilegeExpire = 0)
    {
        return self::buildTokenWithUserAccount($appId, $appCertificate, $channelName, $uid, $role, $tokenExpire, $privilegeExpire);
    }

    /**
     * Build the RTC token with account.
     */
    public static function buildTokenWithUserAccount($appId, $appCertificate, $channelName, $account, $role, $tokenExpire, $privilegeExpire = 0)
    {
        $token = new AccessToken2($appId, $appCertificate, $tokenExpire);
        $serviceRtc = new ServiceRtc($channelName, $account);

        $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_JOIN_CHANNEL, $privilegeExpire);
        if ($role == self::ROLE_PUBLISHER) {
            $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_PUBLISH_AUDIO_STREAM, $privilegeExpire);
            $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_PUBLISH_VIDEO_STREAM, $privilegeExpire);
            $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_PUBLISH_DATA_STREAM, $privilegeExpire);
        }
        $token->addService($serviceRtc);

        return $token->build();
    }

    /**
     * Generates an RTC token with the specified privilege expirations.
     */
    public static function buildTokenWithUidAndPrivilege(
        $appId,
        $appCertificate,
        $channelName,
        $uid,
        $tokenExpire,
        $joinChannelPrivilegeExpire,
        $pubAudioPrivilegeExpire,
        $pubVideoPrivilegeExpire,
        $pubDataStreamPrivilegeExpire
    ) {
        return self::buildTokenWithUserAccountAndPrivilege(
            $appId,
            $appCertificate,
            $channelName,
            $uid,
            $tokenExpire,
            $joinChannelPrivilegeExpire,
            $pubAudioPrivilegeExpire,
            $pubVideoPrivilegeExpire,
            $pubDataStreamPrivilegeExpire
        );
    }

    /**
     * Generates an RTC token with the specified privilege expirations (account variant).
     */
    public static function buildTokenWithUserAccountAndPrivilege(
        $appId,
        $appCertificate,
        $channelName,
        $account,
        $tokenExpire,
        $joinChannelPrivilegeExpire,
        $pubAudioPrivilegeExpire,
        $pubVideoPrivilegeExpire,
        $pubDataStreamPrivilegeExpire
    ) {
        $token = new AccessToken2($appId, $appCertificate, $tokenExpire);
        $serviceRtc = new ServiceRtc($channelName, $account);

        $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_JOIN_CHANNEL, $joinChannelPrivilegeExpire);
        $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_PUBLISH_AUDIO_STREAM, $pubAudioPrivilegeExpire);
        $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_PUBLISH_VIDEO_STREAM, $pubVideoPrivilegeExpire);
        $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_PUBLISH_DATA_STREAM, $pubDataStreamPrivilegeExpire);
        $token->addService($serviceRtc);

        return $token->build();
    }

    /**
     * Build the RTC and RTM token with account.
     */
    public static function buildTokenWithRtm($appId, $appCertificate, $channelName, $account, $role, $tokenExpire, $privilegeExpire = 0)
    {
        $token = new AccessToken2($appId, $appCertificate, $tokenExpire);
        $serviceRtc = new ServiceRtc($channelName, $account);

        $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_JOIN_CHANNEL, $privilegeExpire);
        if ($role == self::ROLE_PUBLISHER) {
            $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_PUBLISH_AUDIO_STREAM, $privilegeExpire);
            $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_PUBLISH_VIDEO_STREAM, $privilegeExpire);
            $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_PUBLISH_DATA_STREAM, $privilegeExpire);
        }
        $token->addService($serviceRtc);

        $serviceRtm = new ServiceRtm($account);
        $serviceRtm->addPrivilege($serviceRtm::PRIVILEGE_LOGIN, $tokenExpire);
        $token->addService($serviceRtm);

        return $token->build();
    }

    /**
     * Build the RTC and RTM token with separate RTC account and RTM user ID.
     */
    public static function buildTokenWithRtm2(
        $appId,
        $appCertificate,
        $channelName,
        $rtcAccount,
        $rtcRole,
        $rtcTokenExpire,
        $joinChannelPrivilegeExpire,
        $pubAudioPrivilegeExpire,
        $pubVideoPrivilegeExpire,
        $pubDataStreamPrivilegeExpire,
        $rtmUserId,
        $rtmTokenExpire
    ) {
        $token = new AccessToken2($appId, $appCertificate, $rtcTokenExpire);
        $serviceRtc = new ServiceRtc($channelName, $rtcAccount);

        $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_JOIN_CHANNEL, $joinChannelPrivilegeExpire);
        if ($rtcRole == self::ROLE_PUBLISHER) {
            $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_PUBLISH_AUDIO_STREAM, $pubAudioPrivilegeExpire);
            $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_PUBLISH_VIDEO_STREAM, $pubVideoPrivilegeExpire);
            $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_PUBLISH_DATA_STREAM, $pubDataStreamPrivilegeExpire);
        }
        $token->addService($serviceRtc);

        $serviceRtm = new ServiceRtm($rtmUserId);
        $serviceRtm->addPrivilege($serviceRtm::PRIVILEGE_LOGIN, $rtmTokenExpire);
        $token->addService($serviceRtm);

        return $token->build();
    }
}
