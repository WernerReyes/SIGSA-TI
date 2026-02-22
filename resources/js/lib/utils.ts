import { EnumOption } from '@/types';
import { InertiaLinkProps } from '@inertiajs/vue3';
import type { Table, Updater } from '@tanstack/vue-table';
import type { ClassValue } from 'clsx';

import { clsx } from 'clsx';
import { format } from 'date-fns-tz';
import { ShieldQuestionIcon } from 'lucide-vue-next';
import { twMerge } from 'tailwind-merge';
import type { Ref } from 'vue';
import equal from 'fast-deep-equal';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    return toUrl(urlToCheck) === currentUrl;
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

export function valueUpdater<T extends Updater<any>>(
    updaterOrValue: T,
    ref: Ref,
) {
    ref.value =
        typeof updaterOrValue === 'function'
            ? updaterOrValue(ref.value)
            : updaterOrValue;
}

export const applyColumnFilter = <T>(table: Table<T>, id: keyof T, value?: any   ) => {
  table.setColumnFilters(prev => {
    const next = prev.filter(f => f.id !== id);
    if (value !== undefined) {
      next.push({ id, value });
    }
    return next;
  });
};


export function parseDateOnly(date: string) {
    const [y, m, d] = date.split('-').map(Number);
    return new Date(y, m - 1, d);
}

export function toZonedDate(
    date: Date | string,
    timeZone: string = 'America/Lima',
): string {
    return format(date, "yyyy-MM-dd'T'HH:mm:ssXXX", { timeZone });
}

export const getEmptyEnumOption = <T>(value: T): EnumOption<T> => {
    return {
        label: 'Desconocido',
        value: value as T,
        icon: ShieldQuestionIcon,
        bg: 'bg-gray-500',
    };
};


export const isEqual = <T>(a: T, b: T): boolean => {
    return equal(a, b);
};

export const formatMinutes = (minutes: number): string => {
    if (!Number.isFinite(minutes) || minutes === 0) return '0 min';
    const hrs = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hrs && mins) return `${hrs}h ${mins}m`;
    if (hrs) return `${hrs}h`;
    return `${mins}m`;
};


export const getImageUrl = (path: string) => {
    if (!path) return undefined;
    const baseUrl = window.location.origin; 
    return `${baseUrl}/storage/${path}`;
};





 export const  getRandomColor = (brand: string): string => {
   
    const h = hashString(brand);
    const hue = (h * 137.508) % 360; // Golden Angle

    const saturation = 65 + (h % 20); // 65–85
    const lightness = 45 + (h % 15);  // 45–60

    return `hsl(${hue} ${saturation}% ${lightness}%)`;
}


function hashString(value: string): number {
    let hash = 0;
    for (let i = 0; i < value.length; i++) {
        hash = (hash << 5) - hash + value.charCodeAt(i);
        hash |= 0;
    }
    return Math.abs(hash);
}


function hslToRgb(h: number, s: number, l: number) {
    s /= 100;
    l /= 100;

    const k = (n: number) => (n + h / 30) % 12;
    const a = s * Math.min(l, 1 - l);
    const f = (n: number) =>
        l - a * Math.max(-1, Math.min(k(n) - 3, Math.min(9 - k(n), 1)));

    return {
        r: Math.round(255 * f(0)),
        g: Math.round(255 * f(8)),
        b: Math.round(255 * f(4)),
    };
}

export const getTextColor = (bgColor: string): 'black' | 'white' => {
    const match = bgColor.match(/hsl\((\d+)\s+(\d+)%\s+(\d+)%\)/);
    if (!match) return 'black';

    const h = Number(match[1]);
    const s = Number(match[2]);
    const l = Number(match[3]);

    const { r, g, b } = hslToRgb(h, s, l);
    const lum = luminance(r, g, b);

    // WCAG contrast rule
    return lum > 0.5 ? 'black' : 'white';
}

function luminance(r: number, g: number, b: number) {
    const a = [r, g, b].map(v => {
        v /= 255;
        return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
    });
    return 0.2126 * a[0] + 0.7152 * a[1] + 0.0722 * a[2];
}



export const buildTooltipContent = (d: {
    total: number,
    data: { label: string; color: string };
    value: number;
}, thing: string): HTMLElement => {
    const wrapper = document.createElement('div');
    wrapper.className = 'min-w-[170px] rounded-xl border border-border/40 bg-background/90 p-3 shadow-2xl backdrop-blur-md';

    
    const percentValue = d.total > 0 ? Math.round((d.value / d.total) * 100) : 0;

    wrapper.innerHTML = `
        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full ring-2 ring-background" style="background:${d.data.color}"></span>
                    <span class="text-[11px] uppercase tracking-wide text-muted-foreground">${d.data.label}</span>
                </div>
                <span class="rounded-full border border-border/40 bg-background/60 px-2 py-0.5 text-[10px] font-medium text-foreground/80">${percentValue}%</span>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-semibold text-foreground tabular-nums">${d.value}</span>
                <span class="text-[11px] text-muted-foreground">
                    ${thing}
                </span>
            </div>
            <div class="h-1 w-full rounded-full" style="background:${d.data.color}"></div>
        </div>
    `;

    return wrapper;
};
