import { router } from '@inertiajs/vue3'
import type { FormDataConvertible } from '@inertiajs/core'
import { reactive, computed } from 'vue'
import type { Section, KategoriRange, KesiapanScoresJson } from '@/types/instrument'

const SECTIONS: Section[] = [
    {
        key: 'a',
        label: 'Kelekatan dan Kemandirian',
        max: 15,
        items: [
            { no: 1,  text: 'Mampu berpisah dengan orangtua tanpa menangis berlebihan' },
            { no: 2,  text: 'Merasa nyaman berada bersama guru/orang dewasa lain' },
            { no: 3,  text: 'Percaya diri mencoba aktivitas baru' },
            { no: 4,  text: 'Mampu bermain tanpa selalu ditemani orangtua' },
            { no: 5,  text: 'Menunjukkan rasa aman di lingkungan baru' },
        ],
    },
    {
        key: 'b',
        label: 'Kesiapan Mengikuti Kegiatan Sekolah',
        max: 15,
        items: [
            { no: 6,  text: 'Datang tepat waktu sesuai jadwal' },
            { no: 7,  text: 'Dapat duduk mengikuti kegiatan selama 10-15 menit' },
            { no: 8,  text: 'Mampu mengikuti rutinitas harian' },
            { no: 9,  text: 'Membawa dan menjaga barang miliknya' },
            { no: 10, text: 'Mengikuti aturan sederhana di kelas' },
        ],
    },
    {
        key: 'c',
        label: 'Keterampilan Dasar Berpartisipasi',
        max: 21,
        items: [
            { no: 11, text: 'Menyapa guru dan teman' },
            { no: 12, text: 'Memperkenalkan diri saat diminta' },
            { no: 13, text: 'Memahami instruksi sederhana' },
            { no: 14, text: 'Meminta bantuan dengan cara yang tepat' },
            { no: 15, text: 'Menunggu giliran saat bermain' },
            { no: 16, text: 'Mau bergabung dalam aktivitas kelompok' },
            { no: 17, text: 'Berbagi alat bermain dengan teman' },
        ],
    },
    {
        key: 'd',
        label: 'Keterampilan Sosial',
        max: 15,
        items: [
            { no: 18, text: 'Bermain bersama teman dengan baik' },
            { no: 19, text: 'Menunjukkan empati kepada teman' },
            { no: 20, text: 'Mengelola konflik sederhana' },
            { no: 21, text: 'Mengungkapkan perasaan secara wajar' },
            { no: 22, text: 'Menghormati aturan saat bermain' },
        ],
    },
    {
        key: 'e',
        label: 'Kemandirian dan Motivasi Belajar',
        max: 15,
        items: [
            { no: 23, text: 'Berusaha menyelesaikan tugas sendiri' },
            { no: 24, text: 'Tidak mudah menyerah ketika mengalami kesulitan' },
            { no: 25, text: 'Mampu merapikan alat bermain sendiri' },
            { no: 26, text: 'Menunjukkan rasa ingin tahu yang tinggi' },
            { no: 27, text: 'Menyelesaikan aktivitas yang dimulai' },
        ],
    },
    {
        key: 'f',
        label: 'Kesiapan Akademik Dasar',
        max: 18,
        items: [
            { no: 28, text: 'Mengenali warna dasar' },
            { no: 29, text: 'Mengenali bentuk sederhana' },
            { no: 30, text: 'Mengenali sebagian huruf' },
            { no: 31, text: 'Mampu menghitung benda sederhana' },
            { no: 32, text: 'Mendengarkan cerita sampai selesai' },
            { no: 33, text: 'Menggunakan pensil atau krayon dengan baik' },
        ],
    },
]

const KATEGORI: KategoriRange[] = [
    { min: 80, max: 99, label: 'Sangat Siap Sekolah' },
    { min: 60, max: 79, label: 'Siap Sekolah dengan sedikit dukungan' },
    { min: 40, max: 59, label: 'Perlu Penguatan Beberapa Aspek' },
    { min: 0,  max: 39, label: 'Memerlukan Stimulasi Intensif sebelum masuk sekolah' },
]

interface KesiapanForm {
    child_name: string
    child_age: string
    answers: Record<number, number>
}

export function useKesiapan() {
    const form = reactive<KesiapanForm>({
        child_name: '',
        child_age:  '',
        answers:    {},
    })

    const subtotals = computed<Record<string, number>>(() => {
        return SECTIONS.reduce((acc, section) => {
            acc[section.key] = section.items.reduce((sum, item) => {
                return sum + (form.answers[item.no] ?? 0)
            }, 0)
            return acc
        }, {} as Record<string, number>)
    })

    const total = computed<number>(() => {
        return Object.values(subtotals.value).reduce((a, b) => a + b, 0)
    })

    const maxTotal = computed<number>(() => {
        return SECTIONS.reduce((sum, s) => sum + s.max, 0)
    })

    const kategori = computed<string>(() => {
        return KATEGORI.find(k => total.value >= k.min && total.value <= k.max)?.label ?? '-'
    })

    const isComplete = computed<boolean>(() => {
        const totalItems = SECTIONS.flatMap(s => s.items).length
        return Object.keys(form.answers).length === totalItems
    })

    const progress = computed<number>(() => {
        const totalItems = SECTIONS.flatMap(s => s.items).length
        return Math.round((Object.keys(form.answers).length / totalItems) * 100)
    })

    function buildScoresJson(): KesiapanScoresJson {
        return {
            aspects: SECTIONS.reduce((acc, section) => {
                acc[section.key] = {
                    label:    section.label,
                    subtotal: subtotals.value[section.key],
                    max:      section.max,
                }
                return acc
            }, {} as KesiapanScoresJson['aspects']),
            total:     total.value,
            max_total: maxTotal.value,
            kategori:  kategori.value,
        }
    }

    function submit(): void {
        router.post('/assessments/results', {
            assessment_type: 'kesiapan',
            child_name:      form.child_name,
            child_age:       form.child_age,
            scores_json:     buildScoresJson() as unknown as FormDataConvertible,
        })
    }

    function reset(): void {
        router.delete('/assessments/results/kesiapan')
    }

    function loadFromResult(result: KesiapanScoresJson | null): void {
        if (!result) return
        // hanya load metadata, jawaban tidak disimpan
    }

    return {
        form,
        SECTIONS,
        subtotals,
        total,
        maxTotal,
        kategori,
        isComplete,
        progress,
        submit,
        reset,
        loadFromResult,
    }
}