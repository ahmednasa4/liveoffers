import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import {
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonCard,
  IonCardHeader,
  IonCardTitle,
  IonCardContent,
  IonChip,
  IonLabel,
  IonIcon,
  IonSkeletonText,
} from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import { grid, chevronBack } from 'ionicons/icons';
import { PublicApiService } from '../../core/services/public-api.service';
import { Category } from '../../core/models/api.types';

@Component({
  selector: 'app-categories',
  standalone: true,
  imports: [
    CommonModule,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonCard,
    IonCardHeader,
    IonCardTitle,
    IonCardContent,
    IonChip,
    IonLabel,
    IonIcon,
    IonSkeletonText,
  ],
  templateUrl: './categories.page.html',
  styleUrls: ['./categories.page.scss'],
})
export class CategoriesPage implements OnInit {
  loading = true;
  categories: Category[] = [];

  constructor(
    private readonly api: PublicApiService,
    private readonly router: Router,
  ) {
    addIcons({ grid, chevronBack });
  }

  ngOnInit(): void {
    this.api
      .categories()
      .toPromise()
      .then((c) => (this.categories = c ?? []))
      .catch(() => {})
      .finally(() => (this.loading = false));
  }

  openCategory(c: Category) {
    this.router.navigate(['/tabs/offers'], {
      queryParams: { category_id: c.id, title: c.name },
    });
  }

  openSubcategory(c: Category, subId: number, subName: string) {
    this.router.navigate(['/tabs/offers'], {
      queryParams: { category_id: c.id, subcategory_id: subId, title: subName },
    });
  }
}
