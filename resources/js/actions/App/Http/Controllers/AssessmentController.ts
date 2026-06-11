import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AssessmentController::index
* @see app/Http/Controllers/AssessmentController.php:12
* @route '/assessments'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/assessments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AssessmentController::index
* @see app/Http/Controllers/AssessmentController.php:12
* @route '/assessments'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssessmentController::index
* @see app/Http/Controllers/AssessmentController.php:12
* @route '/assessments'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AssessmentController::index
* @see app/Http/Controllers/AssessmentController.php:12
* @route '/assessments'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AssessmentController::index
* @see app/Http/Controllers/AssessmentController.php:12
* @route '/assessments'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AssessmentController::index
* @see app/Http/Controllers/AssessmentController.php:12
* @route '/assessments'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AssessmentController::index
* @see app/Http/Controllers/AssessmentController.php:12
* @route '/assessments'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\AssessmentController::kesiapan
* @see app/Http/Controllers/AssessmentController.php:26
* @route '/assessments/kesiapan'
*/
export const kesiapan = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: kesiapan.url(options),
    method: 'get',
})

kesiapan.definition = {
    methods: ["get","head"],
    url: '/assessments/kesiapan',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AssessmentController::kesiapan
* @see app/Http/Controllers/AssessmentController.php:26
* @route '/assessments/kesiapan'
*/
kesiapan.url = (options?: RouteQueryOptions) => {
    return kesiapan.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssessmentController::kesiapan
* @see app/Http/Controllers/AssessmentController.php:26
* @route '/assessments/kesiapan'
*/
kesiapan.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: kesiapan.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AssessmentController::kesiapan
* @see app/Http/Controllers/AssessmentController.php:26
* @route '/assessments/kesiapan'
*/
kesiapan.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: kesiapan.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AssessmentController::kesiapan
* @see app/Http/Controllers/AssessmentController.php:26
* @route '/assessments/kesiapan'
*/
const kesiapanForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: kesiapan.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AssessmentController::kesiapan
* @see app/Http/Controllers/AssessmentController.php:26
* @route '/assessments/kesiapan'
*/
kesiapanForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: kesiapan.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AssessmentController::kesiapan
* @see app/Http/Controllers/AssessmentController.php:26
* @route '/assessments/kesiapan'
*/
kesiapanForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: kesiapan.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

kesiapan.form = kesiapanForm

/**
* @see \App\Http\Controllers\AssessmentController::pemilihan
* @see app/Http/Controllers/AssessmentController.php:38
* @route '/assessments/pemilihan'
*/
export const pemilihan = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pemilihan.url(options),
    method: 'get',
})

pemilihan.definition = {
    methods: ["get","head"],
    url: '/assessments/pemilihan',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AssessmentController::pemilihan
* @see app/Http/Controllers/AssessmentController.php:38
* @route '/assessments/pemilihan'
*/
pemilihan.url = (options?: RouteQueryOptions) => {
    return pemilihan.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssessmentController::pemilihan
* @see app/Http/Controllers/AssessmentController.php:38
* @route '/assessments/pemilihan'
*/
pemilihan.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pemilihan.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AssessmentController::pemilihan
* @see app/Http/Controllers/AssessmentController.php:38
* @route '/assessments/pemilihan'
*/
pemilihan.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pemilihan.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AssessmentController::pemilihan
* @see app/Http/Controllers/AssessmentController.php:38
* @route '/assessments/pemilihan'
*/
const pemilihanForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pemilihan.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AssessmentController::pemilihan
* @see app/Http/Controllers/AssessmentController.php:38
* @route '/assessments/pemilihan'
*/
pemilihanForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pemilihan.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AssessmentController::pemilihan
* @see app/Http/Controllers/AssessmentController.php:38
* @route '/assessments/pemilihan'
*/
pemilihanForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pemilihan.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

pemilihan.form = pemilihanForm

/**
* @see \App\Http\Controllers\AssessmentController::store
* @see app/Http/Controllers/AssessmentController.php:69
* @route '/assessments/results'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/assessments/results',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AssessmentController::store
* @see app/Http/Controllers/AssessmentController.php:69
* @route '/assessments/results'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssessmentController::store
* @see app/Http/Controllers/AssessmentController.php:69
* @route '/assessments/results'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AssessmentController::store
* @see app/Http/Controllers/AssessmentController.php:69
* @route '/assessments/results'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AssessmentController::store
* @see app/Http/Controllers/AssessmentController.php:69
* @route '/assessments/results'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\AssessmentController::destroy
* @see app/Http/Controllers/AssessmentController.php:86
* @route '/assessments/results/{type}'
*/
export const destroy = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/assessments/results/{type}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AssessmentController::destroy
* @see app/Http/Controllers/AssessmentController.php:86
* @route '/assessments/results/{type}'
*/
destroy.url = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { type: args }
    }

    if (Array.isArray(args)) {
        args = {
            type: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        type: args.type,
    }

    return destroy.definition.url
            .replace('{type}', parsedArgs.type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssessmentController::destroy
* @see app/Http/Controllers/AssessmentController.php:86
* @route '/assessments/results/{type}'
*/
destroy.delete = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\AssessmentController::destroy
* @see app/Http/Controllers/AssessmentController.php:86
* @route '/assessments/results/{type}'
*/
const destroyForm = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AssessmentController::destroy
* @see app/Http/Controllers/AssessmentController.php:86
* @route '/assessments/results/{type}'
*/
destroyForm.delete = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const AssessmentController = { index, kesiapan, pemilihan, store, destroy }

export default AssessmentController