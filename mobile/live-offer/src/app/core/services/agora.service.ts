import { inject, Injectable } from '@angular/core';
import { ToastController } from '@ionic/angular/standalone';
import AgoraRTC, {
  IAgoraRTCClient,
  IRemoteAudioTrack,
  IRemoteVideoTrack,
} from 'agora-rtc-sdk-ng';

/**
 * Agora RTC viewer service.
 *
 * Joins a channel as an *audience* member (subscribe-only — no camera/mic,
 * no secure-context requirement) and renders the host's remote video into a
 * target element.
 */
@Injectable({ providedIn: 'root' })
export class AgoraService {
  private readonly toast = inject(ToastController);

  private client: IAgoraRTCClient | null = null;
  private remoteVideo: IRemoteVideoTrack | null = null;
  private remoteAudio: IRemoteAudioTrack | null = null;

  /** Join as a viewer; renders the host's video into {@link opts.videoEl}. */
  async joinChannel(opts: {
    appId: string;
    token: string;
    channel: string;
    uid: number | null;
    videoEl: HTMLElement;
  }): Promise<void> {
    // If a previous session leaked, leave it first.
    if (this.client) {
      await this.leaveChannel();
    }

    try {
      const client = AgoraRTC.createClient({ mode: 'live', codec: 'vp8' });
      await client.setClientRole('audience');
      this.client = client;

      client.on('user-published', async (user, mediaType) => {
        try {
          await client.subscribe(user, mediaType);
          if (mediaType === 'video') {
            this.remoteVideo = user.videoTrack ?? null;
            this.remoteVideo?.play(opts.videoEl);
          } else if (mediaType === 'audio') {
            this.remoteAudio = user.audioTrack ?? null;
            this.remoteAudio?.play();
          }
        } catch (e) {
          console.error('Agora subscribe failed', e);
        }
      });

      client.on('user-unpublished', (user) => {
        if (user.videoTrack === this.remoteVideo) {
          this.remoteVideo = null;
        }
        if (user.audioTrack === this.remoteAudio) {
          this.remoteAudio = null;
        }
      });

      await client.join(
        opts.appId,
        opts.channel,
        opts.token,
        opts.uid ?? null,
      );
    } catch (err) {
      await this.errorToast(err);
      await this.leaveChannel();
      throw err;
    }
  }

  /** Leave the channel and release tracks. Safe to call multiple times. */
  async leaveChannel(): Promise<void> {
    try {
      if (this.client) {
        this.remoteVideo?.stop();
        this.remoteAudio?.stop();
        await this.client.leave();
      }
    } catch (e) {
      // Swallow — best-effort cleanup on leave.
    } finally {
      this.client = null;
      this.remoteVideo = null;
      this.remoteAudio = null;
    }
  }

  private async errorToast(err: unknown): Promise<void> {
    const message =
      err instanceof Error ? err.message : 'فشل الانضمام للبث المباشر';
    const t = await this.toast.create({
      message,
      duration: 3000,
      position: 'bottom',
      color: 'danger',
    });
    await t.present();
  }
}
