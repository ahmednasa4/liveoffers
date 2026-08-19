import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { Subscription } from 'rxjs';
import {
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonBackButton,
  IonInfiniteScroll,
  IonInfiniteScrollContent,
  IonSkeletonText,
  IonChip,
  IonLabel,
  IonSearchbar,
} from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import { close, pricetags, search } from 'ionicons/icons';
import { PublicApiService } from '../../core/services/public-api.service';
import { Category, Offer, Paginated } from '../../core/models/api.types';
import { OfferCardComponent } from '../../core/components/offer-card/offer-card.component';

@Component({
  selector: 'app-offers',
  standalone: true,
  imports: [
    CommonModule,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonButtons,
    IonBackButton,
    IonInfiniteScroll,
    IonInfiniteScrollContent,
    IonSkeletonText,
    IonChip,
    IonLabel,
    IonSearchbar,
    OfferCardComponent,
  ],
  templateUrl: './offers.page.html',
  styleUrls: ['./offers.page.scss'],
})
export class OffersPage implements OnInit, OnDestroy {
  loading = true;
  offers: Offer[] = [];
  categories: Category[] = [];
  activeCategoryId?: number;
  activeSubcategoryId?: number;
  title = 'كل العروض';
  searchTerm = '';

  page = 1;
  lastPage = 1;
  private routeSub?: Subscription;

  constructor(
    private readonly api: PublicApiService,
    private readonly route: ActivatedRoute,
  ) {
    addIcons({ close, pricetags, search });
  }

  ngOnInit(): void {
    this.routeSub = this.route.queryParams.subscribe((p) => {
      this.activeCategoryId = p['category_id'] ? Number(p['category_id']) : undefined;
      this.activeSubcategoryId = p['subcategory_id'] ? Number(p['subcategory_id']) : undefined;
      this.title = p['title'] || 'كل العروض';
      this.reset();
    });
    this.api.categories().toPromise().then((c) => (this.categories = c ?? []));
  }

  ngOnDestroy(): void {
    this.routeSub?.unsubscribe();
  }

  private reset(): void {
    this.page = 1;
    this.lastPage = 1;
    this.offers = [];
    this.loading = true;
    this.loadPage();
  }

  private loadPage(done?: () => void): void {
    this.api
      .offers({
        category_id: this.activeCategoryId,
        subcategory_id: this.activeSubcategoryId,
        search: this.searchTerm || undefined,
        page: this.page,
      })
      .toPromise()
      .then((res: Paginated<Offer> | undefined) => {
        if (res) {
          this.offers = [...this.offers, ...res.data];
          this.lastPage = res.last_page;
        }
      })
      .catch(() => {})
      .finally(() => {
        this.loading = false;
        done?.();
      });
  }

  loadMore(event: CustomEvent): void {
    if (this.page < this.lastPage) {
      this.page++;
      this.loadPage(() =>
        (event.target as HTMLIonInfiniteScrollElement).complete(),
      );
    } else {
      (event.target as HTMLIonInfiniteScrollElement).complete();
    }
  }

  filterByCategory(c: Category): void {
    this.activeCategoryId = c.id;
    this.title = c.name;
    this.reset();
  }

  clearFilter(): void {
    this.activeCategoryId = undefined;
    this.title = 'كل العروض';
    this.reset();
  }

  /** Search input — debounced by the ion-searchbar's [debounce]. */
  onSearch(event: CustomEvent): void {
    this.searchTerm = (event.detail?.value ?? '').toString().trim();
    this.reset();
  }
}
