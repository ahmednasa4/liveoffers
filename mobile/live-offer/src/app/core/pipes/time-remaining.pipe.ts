import { Pipe, PipeTransform } from '@angular/core';

/**
 * Pure formatter: ISO end date → Arabic remaining-time string.
 * Pure (does not tick on its own). For live ticking use CountdownComponent.
 * Usage: {{ offer.end_date | timeRemaining }}
 */
@Pipe({ name: 'timeRemaining' })
export class TimeRemainingPipe implements PipeTransform {
  transform(end?: string | null, now: number = Date.now()): string {
    if (!end) return '';
    const endMs = new Date(end).getTime();
    const diff = endMs - now;
    if (diff <= 0) return 'انتهى العرض';

    const sec = Math.floor(diff / 1000);
    const days = Math.floor(sec / 86400);
    const hours = Math.floor((sec % 86400) / 3600);
    const mins = Math.floor((sec % 3600) / 60);
    const secs = sec % 60;

    const parts: string[] = [];
    if (days > 0) parts.push(`${days} يوم`);
    if (hours > 0) parts.push(`${hours} س`);
    parts.push(`${mins} د`);
    parts.push(`${secs} ث`);
    return `متبقي ${parts.join(' ')}`;
  }
}
