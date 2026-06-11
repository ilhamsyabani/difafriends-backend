import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\CertificateController::verify
* @see app/Http/Controllers/CertificateController.php:34
* @route '/verify/{number}'
*/
export const verify = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

verify.definition = {
    methods: ["get","head"],
    url: '/verify/{number}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CertificateController::verify
* @see app/Http/Controllers/CertificateController.php:34
* @route '/verify/{number}'
*/
verify.url = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { number: args }
    }

    if (Array.isArray(args)) {
        args = {
            number: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        number: args.number,
    }

    return verify.definition.url
            .replace('{number}', parsedArgs.number.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CertificateController::verify
* @see app/Http/Controllers/CertificateController.php:34
* @route '/verify/{number}'
*/
verify.get = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CertificateController::verify
* @see app/Http/Controllers/CertificateController.php:34
* @route '/verify/{number}'
*/
verify.head = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: verify.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CertificateController::verify
* @see app/Http/Controllers/CertificateController.php:34
* @route '/verify/{number}'
*/
const verifyForm = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CertificateController::verify
* @see app/Http/Controllers/CertificateController.php:34
* @route '/verify/{number}'
*/
verifyForm.get = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CertificateController::verify
* @see app/Http/Controllers/CertificateController.php:34
* @route '/verify/{number}'
*/
verifyForm.head = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

verify.form = verifyForm

/**
* @see \App\Http\Controllers\CertificateController::index
* @see app/Http/Controllers/CertificateController.php:47
* @route '/certificates'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/certificates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CertificateController::index
* @see app/Http/Controllers/CertificateController.php:47
* @route '/certificates'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CertificateController::index
* @see app/Http/Controllers/CertificateController.php:47
* @route '/certificates'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CertificateController::index
* @see app/Http/Controllers/CertificateController.php:47
* @route '/certificates'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CertificateController::index
* @see app/Http/Controllers/CertificateController.php:47
* @route '/certificates'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CertificateController::index
* @see app/Http/Controllers/CertificateController.php:47
* @route '/certificates'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CertificateController::index
* @see app/Http/Controllers/CertificateController.php:47
* @route '/certificates'
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
* @see \App\Http\Controllers\CertificateController::download
* @see app/Http/Controllers/CertificateController.php:14
* @route '/certificates/{number}/download'
*/
export const download = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

download.definition = {
    methods: ["get","head"],
    url: '/certificates/{number}/download',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CertificateController::download
* @see app/Http/Controllers/CertificateController.php:14
* @route '/certificates/{number}/download'
*/
download.url = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { number: args }
    }

    if (Array.isArray(args)) {
        args = {
            number: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        number: args.number,
    }

    return download.definition.url
            .replace('{number}', parsedArgs.number.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CertificateController::download
* @see app/Http/Controllers/CertificateController.php:14
* @route '/certificates/{number}/download'
*/
download.get = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CertificateController::download
* @see app/Http/Controllers/CertificateController.php:14
* @route '/certificates/{number}/download'
*/
download.head = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: download.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CertificateController::download
* @see app/Http/Controllers/CertificateController.php:14
* @route '/certificates/{number}/download'
*/
const downloadForm = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CertificateController::download
* @see app/Http/Controllers/CertificateController.php:14
* @route '/certificates/{number}/download'
*/
downloadForm.get = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CertificateController::download
* @see app/Http/Controllers/CertificateController.php:14
* @route '/certificates/{number}/download'
*/
downloadForm.head = (args: { number: string | number } | [number: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

download.form = downloadForm

const certificates = {
    verify: Object.assign(verify, verify),
    index: Object.assign(index, index),
    download: Object.assign(download, download),
}

export default certificates