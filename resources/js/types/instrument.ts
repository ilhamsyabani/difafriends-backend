export interface ScoreItem {
    no: number
    text: string
}

export interface Section {
    key: string
    label: string
    max: number
    items: ScoreItem[]
}

export interface AspectResult {
    label: string
    subtotal: number
    max: number
}

export interface KesiapanScoresJson {
    aspects: Record<string, AspectResult>
    total: number
    max_total: number
    kategori: string
}

export interface SchoolResult {
    name: string
    aspects: Record<string, AspectResult>
    total: number
    max_total: number
    kategori: string
}

export interface PemilihanScoresJson {
    child_profile: {
        minat: string[]
        kebutuhan: string[]
    }
    schools: Record<string, SchoolResult>
}

export interface AssessmentResult {
    id: number
    assessment_type: 'kesiapan' | 'pemilihan'
    child_name: string
    child_age: string
    scores_json: KesiapanScoresJson | PemilihanScoresJson
    created_at: string
    updated_at: string
}

export interface KategoriRange {
    min: number
    max: number
    label: string
}

export interface ProfileOption {
    key: string
    label: string
}

export interface SchoolState {
    name: string
    answers: Record<string, number>
}