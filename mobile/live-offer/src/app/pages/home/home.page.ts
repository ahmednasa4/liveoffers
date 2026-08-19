import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import {
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonSkeletonText,
  IonChip,
  IonLabel,
  IonIcon,
} from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import { grid, videocam, star } from 'ionicons/icons';
import { PublicApiService } from '../../core/services/public-api.service';
import { Category, LiveStream, Offer } from '../../core/models/api.types';
import { OfferCardComponent } from '../../core/components/offer-card/offer-card.component';
import { LiveCardComponent } from '../../core/components/live-card/live-card.component';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [
    CommonModule,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonSkeletonText,
    IonChip,
    IonLabel,
    IonIcon,
    OfferCardComponent,
    LiveCardComponent,
  ],
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
})
export class HomePage implements OnInit {
  loading = true;
  featured: Offer[] = [];
  lives: LiveStream[] = [];
  categories: Category[] = [];

  constructor(
    private readonly api: PublicApiService,
    private readonly router: Router,
  ) {
    addIcons({ grid, videocam, star });
  }

  ngOnInit(): void {
    Promise.all([
      this.api.offers({ featured: true }).toPromise(),
      this.api.liveStreams().toPromise(),
      this.api.categories().toPromise(),
    ])
      .then(([featured, lives, categories]) => {
        this.featured = featured?.data ?? [];
        this.lives = lives ?? [];
        this.categories = categories ?? [];
      })
      .catch(() => {})
      .finally(() => (this.loading = false));
  }

  openCategory(c: Category) {
    this.router.navigate(['/tabs/offers'], {
      queryParams: { category_id: c.id, title: c.name },
    });
  }

  seeAllOffers() {
    this.router.navigate(['/tabs/offers']);
  }

  openLive(id: number) {
    this.router.navigate(['/live', id]);
  }
}
