import { Routes } from '@angular/router';
import { TabsPage } from './tabs.page';

export const routes: Routes = [
  {
    path: 'tabs',
    component: TabsPage,
    children: [
      {
        path: 'home',
        loadComponent: () =>
          import('../pages/home/home.page').then((m) => m.HomePage),
      },
      {
        path: 'offers',
        loadComponent: () =>
          import('../pages/offers/offers.page').then((m) => m.OffersPage),
      },
      {
        path: 'categories',
        loadComponent: () =>
          import('../pages/categories/categories.page').then(
            (m) => m.CategoriesPage,
          ),
      },
      {
        path: 'stores',
        loadComponent: () =>
          import('../pages/stores/stores.page').then((m) => m.StoresPage),
      },
      {
        path: 'live',
        loadComponent: () =>
          import('../pages/live/live.page').then((m) => m.LivePage),
      },
      {
        path: '',
        redirectTo: 'home',
        pathMatch: 'full',
      },
    ],
  },
  {
    path: 'offer/:id',
    loadComponent: () =>
      import('../pages/offer-detail/offer-detail.page').then(
        (m) => m.OfferDetailPage,
      ),
  },
  {
    path: 'store/:id',
    loadComponent: () =>
      import('../pages/store-detail/store-detail.page').then(
        (m) => m.StoreDetailPage,
      ),
  },
  {
    path: 'live/:id',
    loadComponent: () =>
      import('../pages/live-detail/live-detail.page').then(
        (m) => m.LiveDetailPage,
      ),
  },
  {
    path: '',
    redirectTo: 'tabs/home',
    pathMatch: 'full',
  },
];
