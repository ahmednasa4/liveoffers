import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonSkeletonText,
} from '@ionic/angular/standalone';
import { PublicApiService } from '../../core/services/public-api.service';
import { LiveStream } from '../../core/models/api.types';
import { LiveCardComponent } from '../../core/components/live-card/live-card.component';

@Component({
  selector: 'app-live',
  standalone: true,
  imports: [
    CommonModule,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonSkeletonText,
    LiveCardComponent,
  ],
  templateUrl: './live.page.html',
  styleUrls: ['./live.page.scss'],
})
export class LivePage implements OnInit {
  loading = true;
  streams: LiveStream[] = [];

  constructor(private readonly api: PublicApiService) {}

  ngOnInit(): void {
    this.api
      .liveStreams()
      .toPromise()
      .then((s) => (this.streams = s ?? []))
      .catch(() => {})
      .finally(() => (this.loading = false));
  }
}
