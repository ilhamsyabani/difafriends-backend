/**
 * Formatters yang dipakai di banyak halaman — import dari sini
 * agar perubahan format cukup di satu tempat.
 */
export function useFormatters() {
    function formatPrice(price: number): string {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    }

    function formatDate(date: string | Date): string {
        return new Date(date).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        });
    }

    function formatDateTime(date: string | Date): string {
        return new Date(date).toLocaleString('id-ID', {
            day: 'numeric',
            month: 'short',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    function formatDuration(minutes: number): string {
        if (minutes < 60) {
return `${minutes} menit`;
}

        const h = Math.floor(minutes / 60);
        const m = minutes % 60;

        return m ? `${h} jam ${m} menit` : `${h} jam`;
    }

    /**
     * Konversi path asset ke URL yang benar.
     * - Path relatif (hasil upload)   → /storage/path
     * - URL absolut (http/https//)    → dikembalikan as-is
     * - null / undefined              → null
     */
    function assetUrl(path: string | null | undefined): string | null {
        if (!path) {
return null;
}

        if (
            path.startsWith('http://') ||
            path.startsWith('https://') ||
            path.startsWith('/')
        ) {
            return path;
        }

        return `/storage/${path}`;
    }

    return {
        formatPrice,
        formatDate,
        formatDateTime,
        formatDuration,
        assetUrl,
    };
}
