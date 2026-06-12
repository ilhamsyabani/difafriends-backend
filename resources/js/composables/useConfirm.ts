import { useModal } from 'vue-final-modal';
import ConfirmModal from '@/components/ConfirmModal.vue';

export function useConfirm() {
    return (title: string, message: string): Promise<boolean> =>
        new Promise((resolve) => {
            const { open, close } = useModal({
                component: ConfirmModal,
                attrs: {
                    title,
                    message,
                    onConfirm() {
                        resolve(true);
                        close();
                    },
                    onCancel() {
                        resolve(false);
                        close();
                    },
                },
            });
            open();
        });
}