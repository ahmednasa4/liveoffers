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
  IonCard,
  IonCardContent,
  IonButton,
  IonIcon,
  IonSkeletonText,
} from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import { call, logoWhatsapp, location, storefront } from 'ionicons/icons';
import { PublicApiService } from '../../core/services/public-api.service';
import { Store } from '../../core/models/api.types';
import { ImageUrlPipe } from '../../core/pipes/image-url.pipe';
import { OfferCardComponent } from '../../core/components/offer-card/offer-card.component';

@Component({
  selector: 'app-store-detail',
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
    ImageUrlPipe,
    OfferCardComponent,
  ],
  templateUrl: './store-detail.page.html',
  styleUrls: ['./store-detail.page.scss'],
})
export class StoreDetailPage implements OnInit {
  loading = true;
  store?: Store;

  constructor(
    private readonly route: ActivatedRoute,
    private readonly api: PublicApiService,
  ) {
    addIcons({ call, logoWhatsapp, location, storefront });
  }

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (!id) return;
    this.api
      .store(id)
      .toPromise()
      .then((s) => (this.store = s))
      .catch(() => {})
      .finally(() => (this.loading = false));
  }

  callStore(): void {
    if (this.store?.phone) window.location.href = `tel:${this.store.phone}`;
  }

  whatsappStore(): void {
    const num = this.store?.whatsapp_number;
    if (num) window.open(`https://wa.me/${num.replace(/\D/g, '')}`, '_blank');
  }
}
