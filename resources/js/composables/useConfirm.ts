import { ref } from 'vue';

interface ConfirmOptions {
    title?: string;
    description?: string;
    confirmLabel?: string;
    cancelLabel?: string;
    variant?: 'default' | 'destructive';
}

// State global — satu dialog per aplikasi
const isOpen = ref(false);
const options = ref<ConfirmOptions>({});
let resolveFn: ((value: boolean) => void) | null = null;

export function useConfirm() {
    function confirm(opts: ConfirmOptions = {}): Promise<boolean> {
        options.value = {
            title: opts.title ?? 'Konfirmasi',
            description: opts.description ?? 'Apakah Anda yakin?',
            confirmLabel: opts.confirmLabel ?? 'Ya, Lanjutkan',
            cancelLabel: opts.cancelLabel ?? 'Batal',
            variant: opts.variant ?? 'default',
        };
        isOpen.value = true;

        return new Promise((resolve) => {
            resolveFn = resolve;
        });
    }

    function handleConfirm() {
        isOpen.value = false;
        resolveFn?.(true);
        resolveFn = null;
    }

    function handleCancel() {
        isOpen.value = false;
        resolveFn?.(false);
        resolveFn = null;
    }

    return { confirm, isOpen, options, handleConfirm, handleCancel };
}
