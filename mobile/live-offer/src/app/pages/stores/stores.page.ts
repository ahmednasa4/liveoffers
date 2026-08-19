import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import {
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonImg,
  IonIcon,
  IonSkeletonText,
  IonChip,
  IonLabel,
  IonSearchbar,
} from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import { storefront, location, pricetags, search } from 'ionicons/icons';
import { PublicApiService } from '../../core/services/public-api.service';
import { Store } from '../../core/models/api.types';
import { ImageUrlPipe } from '../../core/pipes/image-url.pipe';

@Component({
  selector: 'app-stores',
  standalone: true,
  imports: [
    CommonModule,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonImg,
    IonIcon,
    IonSkeletonText,
    IonChip,
    IonLabel,
    IonSearchbar,
    ImageUrlPipe,
  ],
  templateUrl: './stores.page.html',
  styleUrls: ['./stores.page.scss'],
})
export class StoresPage implements OnInit {
  loading = true;
  private allStores: Store[] = [];
  stores: Store[] = [];
  searchTerm = '';

  constructor(
    private readonly api: PublicApiService,
    private readonly router: Router,
  ) {
    addIcons({ storefront, location, pricetags, search });
  }

  ngOnInit(): void {
    this.api
      .stores()
      .toPromise()
      .then((list: Store[] | undefined) => {
        // Keep only stores that have at least one active offer.
        this.allStores = (list ?? []).filter((s) => (s.offers_count ?? 0) > 0);
        this.applySearch();
      })
      .catch(() => {})
      .finally(() => (this.loading = false));
  }

  storeOffersCount(s: Store): number {
    return s.offers_count ?? 0;
  }

  openStore(s: Store): void {
    this.router.navigate(['/store', s.id]);
  }

  /** Search input — debounced by the ion-searchbar's [debounce]. */
  onSearch(event: CustomEvent): void {
    this.searchTerm = (event.detail?.value ?? '').toString().trim();
    this.applySearch();
  }

  private applySearch(): void {
    const term = this.searchTerm.toLowerCase();
    this.stores = term
      ? this.allStores.filter((s) => s.name.toLowerCase().includes(term))
      : [...this.allStores];
  }
}
