import { Component, ElementRef, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import {
  IonHeader,
  IonToolbar,
  IonTitle,
  IonButtons,
  IonBackButton,
  IonContent,
  IonImg,
  IonButton,
  IonIcon,
  IonSkeletonText,
  IonBadge,
} from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import { radio, people, storefront, call, logoWhatsapp, close } from 'ionicons/icons';
import { PublicApiService } from '../../core/services/public-api.service';
import { AgoraService } from '../../core/services/agora.service';
import { LiveStream } from '../../core/models/api.types';
import { ImageUrlPipe } from '../../core/pipes/image-url.pipe';

@Component({
  selector: 'app-live-detail',
  standalone: true,
  imports: [
    CommonModule,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonButtons,
    IonBackButton,
    IonContent,
    IonImg,
    IonButton,
    IonIcon,
    IonSkeletonText,
    IonBadge,
    ImageUrlPipe,
  ],
  templateUrl: './live-detail.page.html',
  styleUrls: ['./live-detail.page.scss'],
})
export class LiveDetailPage implements OnInit, OnDestroy {
  loading = true;
  stream?: LiveStream;

  joining = false;
  joined = false;

  @ViewChild('remoteVideo') remoteVideoRef?: ElementRef<HTMLDivElement>;

  constructor(
    private readonly route: ActivatedRoute,
    private readonly api: PublicApiService,
    private readonly agora: AgoraService,
  ) {
    addIcons({ radio, people, storefront, call, logoWhatsapp, close });
  }

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (!id) return;
    this.api
      .liveStream(id)
      .toPromise()
      .then((s) => (this.stream = s))
      .catch(() => {})
      .finally(() => (this.loading = false));
  }

  async ngOnDestroy(): Promise<void> {
    await this.agora.leaveChannel();
  }

  async join(): Promise<void> {
    if (!this.stream) return;

    // Toggle: if already joined, leave instead.
    if (this.joined) {
      await this.leave();
      return;
    }

    this.joining = true;
    try {
      const vt = await this.api.viewerToken(this.stream.id).toPromise();
      if (!vt) throw new Error('تعذر الحصول على رمز الانضمام');
      const el = this.remoteVideoRef?.nativeElement;
      if (!el) throw new Error('حاوية الفيديو غير متوفرة');

      await this.agora.joinChannel({
        appId: vt.app_id,
        token: vt.token,
        channel: vt.channel_name,
        uid: vt.uid ?? null,
        videoEl: el,
      });
      this.joined = true;
    } catch {
      // Error toast is handled inside AgoraService.
    } finally {
      this.joining = false;
    }
  }

  async leave(): Promise<void> {
    await this.agora.leaveChannel();
    this.joined = false;
  }

  callStore(): void {
    const phone = this.stream?.store?.phone;
    if (phone) window.location.href = `tel:${phone}`;
  }

  whatsappStore(): void {
    const num = this.stream?.store?.whatsapp_number;
    if (num) window.open(`https://wa.me/${num.replace(/\D/g, '')}`, '_blank');
  }
}
