import { router } from '@inertiajs/vue3'
import type { FormDataConvertible } from '@inertiajs/core'
import { reactive, computed } from 'vue'
import type { KategoriRange, ProfileOption, SchoolState, PemilihanScoresJson } from '@/types/instrument'

interface Aspect {
    key: string
    label: string
    max: number
    items: string[]
}

const ASPECTS: Aspect[] = [
    {
        key: 'b1', label: 'Kesesuaian dengan Kebutuhan Anak', max: 20,
        items: [
            'Sekolah memahami kebutuhan perkembangan anak',
            'Program belajar sesuai usia anak',
            'Sekolah mendukung minat dan bakat anak',
            'Anak tampak nyaman saat berkunjung ke sekolah',
            'Sekolah mendukung perkembangan karakter anak',
        ],
    },
    {
        key: 'b2', label: 'Kurikulum dan Pembelajaran', max: 20,
        items: [
            'Kurikulum sesuai harapan keluarga',
            'Pembelajaran berpusat pada anak',
            'Ada keseimbangan antara bermain dan belajar',
            'Mengembangkan kreativitas dan berpikir kritis',
            'Tidak hanya berorientasi pada nilai akademik',
        ],
    },
    {
        key: 'b3', label: 'Guru dan Tenaga Pendidik', max: 20,
        items: [
            'Guru ramah dan menghargai anak',
            'Guru memiliki kompetensi yang baik',
            'Guru memahami perkembangan anak',
            'Guru mudah diajak berkomunikasi',
            'Jumlah guru memadai dibanding jumlah siswa',
        ],
    },
    {
        key: 'b4', label: 'Lingkungan dan Keamanan', max: 20,
        items: [
            'Lingkungan sekolah bersih dan sehat',
            'Ruang belajar aman dan nyaman',
            'Area bermain memadai',
            'Sistem keamanan sekolah baik',
            'Anak merasa senang berada di sekolah',
        ],
    },
    {
        key: 'b5', label: 'Kemitraan dengan Orang Tua', max: 20,
        items: [
            'Sekolah terbuka terhadap masukan orang tua',
            'Komunikasi sekolah dan orang tua berjalan baik',
            'Ada program parenting atau keterlibatan keluarga',
            'Informasi perkembangan anak diberikan secara berkala',
            'Orang tua dilibatkan dalam kegiatan sekolah',
        ],
    },
    {
        key: 'b6', label: 'Orientasi Masa Depan Anak', max: 20,
        items: [
            'Sekolah membantu anak mengenali potensinya',
            'Sekolah mengembangkan karakter dan kemandirian',
            'Sekolah mendorong kreativitas dan inovasi',
            'Sekolah membantu anak membangun kepercayaan diri',
            'Sekolah mempersiapkan anak menghadapi tantangan masa depan',
        ],
    },
    {
        key: 'b7', label: 'Pertimbangan Praktis', max: 20,
        items: [
            'Lokasi sekolah mudah dijangkau',
            'Biaya sesuai kemampuan keluarga',
            'Jadwal sekolah sesuai kebutuhan keluarga',
            'Transportasi aman dan mudah',
            'Fasilitas sebanding dengan biaya yang dikeluarkan',
        ],
    },
]

const MINAT_OPTIONS: ProfileOption[] = [
    { key: 'suka_membaca',        label: 'Suka membaca dan belajar hal baru' },
    { key: 'aktif_bergerak',      label: 'Aktif bergerak dan menyukai aktivitas fisik' },
    { key: 'suka_kreatif',        label: 'Suka menggambar, musik, atau kegiatan kreatif' },
    { key: 'mudah_bergaul',       label: 'Mudah bergaul dengan teman' },
    { key: 'suka_eksplorasi',     label: 'Menyukai kegiatan eksplorasi dan praktik langsung' },
    { key: 'belajar_terstruktur', label: 'Nyaman belajar secara terstruktur' },
]

const KEBUTUHAN_OPTIONS: ProfileOption[] = [
    { key: 'lingkungan_tenang',   label: 'Membutuhkan lingkungan belajar yang tenang' },
    { key: 'aktivitas_bermain',   label: 'Membutuhkan banyak aktivitas bermain' },
    { key: 'sosial_emosional',    label: 'Membutuhkan penguatan sosial-emosional' },
    { key: 'tantangan_akademik',  label: 'Membutuhkan tantangan akademik' },
    { key: 'pendampingan_khusus', label: 'Membutuhkan pendampingan khusus' },
    { key: 'berbasis_agama',      label: 'Membutuhkan lingkungan berbasis agama/nilai tertentu' },
]

const KATEGORI: KategoriRange[] = [
    { min: 120, max: 140, label: 'Sangat Direkomendasikan' },
    { min: 100, max: 119, label: 'Direkomendasikan' },
    { min: 80,  max: 99,  label: 'Cukup Direkomendasikan' },
    { min: 60,  max: 79,  label: 'Perlu Dipertimbangkan Kembali' },
    { min: 0,   max: 59,  label: 'Kurang Direkomendasikan' },
]

interface PemilihanForm {
    child_name: string
    child_age: string
    child_profile: {
        minat: string[]
        kebutuhan: string[]
    }
    schools: SchoolState[]
}

export function usePemilihan() {
    const form = reactive<PemilihanForm>({
        child_name: '',
        child_age:  '',
        child_profile: {
            minat:     [],
            kebutuhan: [],
        },
        schools: [
            { name: '', answers: {} },
            { name: '', answers: {} },
            { name: '', answers: {} },
        ],
    })

    function getSubtotal(schoolIndex: number, aspectKey: string): number {
        const aspect  = ASPECTS.find(a => a.key === aspectKey)
        if (!aspect) return 0
        return aspect.items.reduce((sum, _, i) => {
            return sum + (form.schools[schoolIndex].answers[`${aspectKey}_${i}`] ?? 0)
        }, 0)
    }

    function getTotal(schoolIndex: number): number {
        return ASPECTS.reduce((sum, aspect) => {
            return sum + getSubtotal(schoolIndex, aspect.key)
        }, 0)
    }

    function getKategori(schoolIndex: number): string {
        const t = getTotal(schoolIndex)
        return KATEGORI.find(k => t >= k.min && t <= k.max)?.label ?? '-'
    }

    const bestSchoolIndex = computed<number>(() => {
        const totals = form.schools.map((_, i) => getTotal(i))
        return totals.indexOf(Math.max(...totals))
    })

    const isComplete = computed<boolean>(() => {
        const totalItems = ASPECTS.flatMap(a => a.items).length
        return form.schools.every(school => {
            return Object.keys(school.answers).length === totalItems
        })
    })

    const progress = computed<number>(() => {
        const totalItems  = ASPECTS.flatMap(a => a.items).length * 3
        const totalFilled = form.schools.reduce((sum, school) => {
            return sum + Object.keys(school.answers).length
        }, 0)
        return Math.round((totalFilled / totalItems) * 100)
    })

    function buildScoresJson(): PemilihanScoresJson {
        return {
            child_profile: form.child_profile,
            schools: form.schools.reduce((acc, school, i) => {
                const key = `school_${i + 1}`
                acc[key] = {
                    name: school.name,
                    aspects: ASPECTS.reduce((aAcc, aspect) => {
                        aAcc[aspect.key] = {
                            label:    aspect.label,
                            subtotal: getSubtotal(i, aspect.key),
                            max:      aspect.max,
                        }
                        return aAcc
                    }, {} as Record<string, { label: string; subtotal: number; max: number }>),
                    total:     getTotal(i),
                    max_total: 140,
                    kategori:  getKategori(i),
                }
                return acc
            }, {} as PemilihanScoresJson['schools']),
        }
    }

    function submit(): void {
        router.post('/assessments/results', {
            assessment_type: 'pemilihan',
            child_name:      form.child_name,
            child_age:       form.child_age,
            scores_json:     buildScoresJson() as unknown as FormDataConvertible,
        })
    }

    function reset(): void {
        router.delete('/assessments/results/pemilihan')
    }

    return {
        form,
        ASPECTS,
        MINAT_OPTIONS,
        KEBUTUHAN_OPTIONS,
        bestSchoolIndex,
        isComplete,
        progress,
        getSubtotal,
        getTotal,
        getKategori,
        submit,
        reset,
    }
}