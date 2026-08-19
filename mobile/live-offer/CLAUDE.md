# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Live Offer — an Ionic 8 / Angular 20 mobile app (Arabic, RTL, light-mode only) that surfaces local store offers and live shopping streams. Wrapped for iOS/Android via Capacitor 8. Consumes a public (no-auth) Laravel backend. UI strings and date/time formatting are in Arabic.

## Commands

```bash
npm start              # ng serve — local dev server (Angular dev-server)
npm run build          # ng build — production build (default config), output → www/
npm run watch          # ng build --watch --configuration development
npm test               # ng test — Karma + Jasmine in Chrome (autoWatch, singleRun=false)
npm run lint           # ng lint — @angular-eslint over src/**/*.ts and src/**/*.html
```

Running a single test: Karma has no CLI filter wired up here. Use `fdescribe` / `fit` in the target spec, run `npm test`, then remove the focus. (The default theme variables use the Ionic "Cairo" font via CDN — no font files to vendor.)

The Capacitor `webDir` is `www`, so build (`npm run build`) before `npx cap sync`/`npx cap open`. No native platform (`ios`/`android`) has been added yet. The `appId` in `capacitor.config.ts` is still the starter default `io.ionic.starter` — change it before any native build.

## Architecture

### Bootstrapping (standalone, no NgModule)
`src/main.ts` bootstraps `AppComponent` with `provideIonicAngular({ mode: 'md' })`, `provideRouter(routes, withPreloading(PreloadAllModules))`, and `provideHttpClient(withInterceptors([apiBaseUrlInterceptor]))`. Ionic is forced to Material design mode (not iOS) platform-wide. Everything is standalone components — no NgModules exist.

### Routing (lazy `loadComponent`)
`app.routes.ts` → `tabs.routes.ts`. The tabs shell (`TabsPage`) hosts four tab children: `home`, `offers`, `categories`, `live`. Non-tab detail routes sit as siblings of the `tabs` path in `tabs.routes.ts`: `offer/:id`, `store/:id`, `live/:id`. Each page is a standalone component loaded via `loadComponent`. Default route redirects to `tabs/home`.

### Directory layout
- `src/app/pages/` — routed pages (one folder per page: `.page.ts/.html/.scss`).
- `src/app/core/` — shared, non-routed code:
  - `services/` — `PublicApiService` (HTTP wrapper), `AgoraService` (live-stream viewer).
  - `interceptors/` — `apiBaseUrlInterceptor`.
  - `models/api.types.ts` — the **API contract types**, kept snake_case to match Laravel exactly (commented "See CONTEXT.md §7.2"). Decimal columns arrive as strings.
  - `pipes/` — `imageUrl`, `discount`, `timeRemaining`.
  - `components/` — reusable UI: `offer-card`, `live-card`, `countdown`.
- `src/environments/` — `environment.ts` (dev) is replaced by `environment.prod.ts` via `fileReplacements` in production build. Both define `apiUrl` + `mediaUrl`.

### Backend contract
The backend wraps every public endpoint in an `ApiEnvelope<T> = { success, data, message? }`; paginated endpoints return a Laravel `Paginated<T>` (with `data[]`, `current_page`, `last_page`, `next_page_url`, …) as the envelope `data`. `PublicApiService.unwrap()` centralizes the `res.data` extraction for GETs. Endpoints (all under `/public`):
- `GET public/categories`, `public/offers` (paginated, filterable by `category_id`/`subcategory_id`/`store_id`/`featured`/`page`), `public/offers/{id}`, `public/stores`, `public/stores/{id}`, `public/live-streams`, `public/live-streams/{id}`
- `POST public/live-streams/{id}/viewer-token` → returns a fresh `{ token, app_id, channel_name, uid }` per join.

Service methods use relative paths only; `apiBaseUrlInterceptor` prepends `environment.apiUrl` (absolute URLs pass through untouched). `environment.apiUrl` in dev points at a LAN dev server (`http://192.168.1.25:8000/api`) — set this to your backend's address.

### Media URLs
Storage paths (offer `image`, store `logo`, stream `preview_image`) are bare paths from the API. Resolve them with the `imageUrl` pipe: `| imageUrl`. It prefixes `environment.mediaUrl + "/storage/"`, passes absolute/data:/`assets/` URLs through, and returns `''` for null so templates can show a placeholder. It returns a plain string (not `SafeUrl`) so it binds to `ion-img`'s shadow-DOM `<img>`.

### Live streaming (Agora)
`agora-rtc-sdk-ng` is the viewer client. `AgoraService` joins a channel in **audience role only** (subscribe-only: no camera/mic, no secure-context requirement) and renders the host's remote video into a target element. `LiveDetailPage` fetches a fresh viewer token via `PublicApiService.viewerToken()`, then calls `agora.joinChannel({ appId, token, channel, uid, videoEl })`. `leaveChannel()` is idempotent and called from `ngOnDestroy`; `AgoraService` also self-cleans if `joinChannel` is called with a leaked prior session. Errors surface via an Ionic toast (Arabic message) thrown from inside the service — pages catch and swallow, relying on the toast.

### Conventions for new code
- **Standalone components**, `inject()`/constructor DI, `signals` for local UI state (see `CountdownComponent`).
- **Icons**: import from `ionicons/icons` and register via `addIcons({...})` in the component constructor — never rely on global icon registration.
- **Ionic imports**: pull individual components from `@ionic/angular/standalone`.
- **Schematics**: `@ionic/angular-toolkit:page` generates standalone SCSS pages by default (`angular.json` schematics config). Use `ng generate page <name>` (or the component equivalent).
- **RTL/Arabic**: `index.html` sets `<html lang="ar" dir="rtl">` and `color-scheme: light`. Keep user-facing strings Arabic. Avoid hard-coded LTR layouts.
- **Async pattern**: pages currently lean on `.toPromise()` + `.then/.catch/.finally` over the service's `Observable`s (Angular 20 has RxJS 7.8). Match the surrounding page's style rather than mixing.
