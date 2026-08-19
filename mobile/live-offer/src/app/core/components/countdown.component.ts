import { Component, Input, OnDestroy, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TimeRemainingPipe } from '../pipes/time-remaining.pipe';

/**
 * Live-ticking countdown to an ISO end date.
 * Uses a per-second interval (no Date.now() in template → CD friendly).
 */
@Component({
  selector: 'app-countdown',
  standalone: true,
  imports: [CommonModule],
  template: `
    <span [class.expired]="expired()">{{ label() }}</span>
  `,
  styles: [
    `
      :host {
        display: inline-block;
        font-weight: 600;
        color: var(--ion-color-danger);
      }
      .expired {
        color: var(--ion-color-medium);
      }
    `,
  ],
})
export class CountdownComponent implements OnInit, OnDestroy {
  @Input({ required: true }) end!: string;
  private timer?: ReturnType<typeof setInterval>;
  readonly now = signal(Date.now());

  label(): string {
    return new TimeRemainingPipe().transform(this.end, this.now());
  }

  expired(): boolean {
    return new Date(this.end).getTime() - this.now() <= 0;
  }

  ngOnInit(): void {
    this.timer = setInterval(() => this.now.set(Date.now()), 1000);
  }

  ngOnDestroy(): void {
    if (this.timer) clearInterval(this.timer);
  }
}
