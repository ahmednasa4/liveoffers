import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import {
  IonCard,
  IonImg,
  IonBadge,
  IonIcon,
  IonLabel,
} from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import { radio, storefront } from 'ionicons/icons';
import { LiveStream } from '../../../core/models/api.types';
import { ImageUrlPipe } from '../../../core/pipes/image-url.pipe';

/** Live-stream card for the Live feed and Home strip. */
@Component({
  selector: 'app-live-card',
  standalone: true,
  imports: [
    CommonModule,
    IonCard,
    IonImg,
    IonBadge,
    IonIcon,
    IonLabel,
    ImageUrlPipe,
  ],
  templateUrl: './live-card.component.html',
  styleUrls: ['./live-card.component.scss'],
})
export class LiveCardComponent {
  @Input({ required: true }) stream!: LiveStream;

  constructor(private readonly router: Router) {
    addIcons({ radio, storefront });
  }

  open() {
    this.router.navigate(['/live', this.stream.id]);
  }
}
