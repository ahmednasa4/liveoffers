import { Component, OnInit } from '@angular/core';
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
  IonBadge,
  IonCard,
  IonCardHeader,
  IonCardTitle,
  IonCardContent,
  IonButton,
  IonIcon,
  IonSkeletonText,
} from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import {
  call,
  logoWhatsapp,
  storefront,
  location,
  pricetag,
  eye,
  share,
} from 'ionicons/icons';
import { PublicApiService } from '../../core/services/public-api.service';
import { Offer } from '../../core/models/api.types';
import { ImageUrlPipe } from '../../core/pipes/image-url.pipe';
import { DiscountPipe } from '../../core/pipes/discount.pipe';
import { CountdownComponent } from '../../core/components/countdown.component';

@Component({
  selector: 'app-offer-detail',
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
    IonBadge,
    IonCard,
    IonCardHeader,
    IonCardTitle,
    IonCardContent,
    IonButton,
    IonIcon,
    IonSkeletonText,
    ImageUrlPipe,
    DiscountPipe,
    CountdownComponent,
  ],
  templateUrl: './offer-detail.page.html',
  styleUrls: ['./offer-detail.page.scss'],
})
export class OfferDetailPage implements OnInit {
  loading = true;
  offer?: Offer;

  constructor(
    private readonly route: ActivatedRoute,
    private readonly api: PublicApiService,
  ) {
    addIcons({ call, logoWhatsapp, storefront, location, pricetag, eye, share });
  }

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (!id) return;
    this.api
      .offer(id)
      .toPromise()
      .then((o) => (this.offer = o))
      .catch(() => {})
      .finally(() => (this.loading = false));
  }

  callStore(): void {
    const phone = this.offer?.store?.phone;
    if (phone) window.location.href = `tel:${phone}`;
  }

  whatsappStore(): void {
    const num = this.offer?.store?.whatsapp_number;
    if (num) window.open(`https://wa.me/${num.replace(/\D/g, '')}`, '_blank');
  }

  async shareOffer(): Promise<void> {
    if (navigator.share) {
      try {
        await navigator.share({
          title: this.offer?.title,
          text: this.offer?.description,
        });
      } catch {
        /* user cancelled */
      }
    }
  }
}
