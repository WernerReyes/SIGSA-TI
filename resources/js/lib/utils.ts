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

export const formatMinutes = (minutes: number) => {
    if (!minutes || minutes <= 0) return 'Sin definir';
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hours && mins) return `${hours} h ${mins} min`;
    if (hours) return `${hours} h`;
    return `${mins} min`;
};
