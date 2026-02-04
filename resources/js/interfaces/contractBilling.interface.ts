
import { getEmptyEnumOption } from '@/lib/utils';
import { EnumOption } from '@/types';
import {
    Calendar,
    CalendarClock,
    CalendarDays,
    CalendarRange,
    Receipt,
} from 'lucide-vue-next';

export enum BillingFrequency {
    MONTHLY = 'MONTHLY',
    QUARTERLY = 'QUARTERLY',
    SEMIANNUAL = 'SEMIANNUAL',
    ANNUAL = 'ANNUAL',
    ONE_TIME = 'ONE_TIME',
}

export enum CurrencyType {
    USD = 'USD',
    PEN = 'PEN',
    EUR = 'EUR',
}

export interface ContractBilling {
    id: number;
    contract_id: number;
    frequency: BillingFrequency;
    amount: number | null;
    currency: CurrencyType;
    billing_cycle_days: number | null;
    auto_renew: boolean;
    next_billing_date: string | null;
    created_at: Date;
    updated_at: Date;
}

export const billingFrequencyOptions: Record<
    BillingFrequency,
    EnumOption<BillingFrequency>
> = {
    [BillingFrequency.MONTHLY]: {
        label: 'Mensual',
        value: BillingFrequency.MONTHLY,
        icon: Calendar,
        bg: 'bg-blue-500',
    },
    [BillingFrequency.QUARTERLY]: {
        label: 'Trimestral',
        value: BillingFrequency.QUARTERLY,
        icon: CalendarDays,
        bg: 'bg-indigo-500',
    },
    [BillingFrequency.SEMIANNUAL]: {
        label: 'Semestral',
        value: BillingFrequency.SEMIANNUAL,
        icon: CalendarRange,
        bg: 'bg-purple-500',
    },
    [BillingFrequency.ANNUAL]: {
        label: 'Anual',
        value: BillingFrequency.ANNUAL,
        icon: CalendarClock,
        bg: 'bg-green-500',
    },
    [BillingFrequency.ONE_TIME]: {
        label: 'Única vez',
        value: BillingFrequency.ONE_TIME,
        icon: Receipt,
        bg: 'bg-yellow-500',
    },
};

export const billingFrequencyDaysMap: Record<BillingFrequency, number | null> =
    {
        [BillingFrequency.MONTHLY]: 30,
        [BillingFrequency.QUARTERLY]: 90,
        [BillingFrequency.SEMIANNUAL]: 180,
        [BillingFrequency.ANNUAL]: 365,
        [BillingFrequency.ONE_TIME]: null,
    };

export const getContractBillOp = <T>(
    type: 'frequency',
    op?: T | null,
): EnumOption<T | undefined | null> => {
    if (!op) {
        return getEmptyEnumOption(op);
    }
    switch (type) {
        case 'frequency':
            return billingFrequencyOptions[
                op as BillingFrequency
            ] as EnumOption<T>;
    }
};


