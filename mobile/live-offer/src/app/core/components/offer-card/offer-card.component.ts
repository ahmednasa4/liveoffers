import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import {
  IonCard,
  IonCardHeader,
  IonCardTitle,
  IonCardSubtitle,
  IonCardContent,
  IonImg,
  IonBadge,
  IonIcon,
} from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import { storefront, pricetag } from 'ionicons/icons';
import { Offer } from '../../../core/models/api.types';
import { DiscountPipe } from '../../../core/pipes/discount.pipe';
import { ImageUrlPipe } from '../../../core/pipes/image-url.pipe';

/** Compact offer card used in grids/lists across Home, Offers, Store detail. */
@Component({
  selector: 'app-offer-card',
  standalone: true,
  imports: [
    CommonModule,
    IonCard,
    IonCardHeader,
    IonCardTitle,
    IonCardSubtitle,
    IonCardContent,
    IonImg,
    IonBadge,
    IonIcon,
    DiscountPipe,
    ImageUrlPipe,
  ],
  templateUrl: './offer-card.component.html',
  styleUrls: ['./offer-card.component.scss'],
})
export class OfferCardComponent {
  @Input({ required: true }) offer!: Offer;

  constructor(private readonly router: Router) {
    addIcons({ storefront, pricetag });
  }

  open() {
    this.router.navigate(['/offer', this.offer.id]);
  }
}
