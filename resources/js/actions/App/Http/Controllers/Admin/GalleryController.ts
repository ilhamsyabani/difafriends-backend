import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\GalleryController::index
* @see app/Http/Controllers/Admin/GalleryController.php:20
* @route '/admin/gallery'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/gallery',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\GalleryController::index
* @see app/Http/Controllers/Admin/GalleryController.php:20
* @route '/admin/gallery'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\GalleryController::index
* @see app/Http/Controllers/Admin/GalleryController.php:20
* @route '/admin/gallery'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\GalleryController::index
* @see app/Http/Controllers/Admin/GalleryController.php:20
* @route '/admin/gallery'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\GalleryController::index
* @see app/Http/Controllers/Admin/GalleryController.php:20
* @route '/admin/gallery'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\GalleryController::index
* @see app/Http/Controllers/Admin/GalleryController.php:20
* @route '/admin/gallery'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\GalleryController::index
* @see app/Http/Controllers/Admin/GalleryController.php:20
* @route '/admin/gallery'
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
* @see \App\Http\Controllers\Admin\GalleryController::store
* @see app/Http/Controllers/Admin/GalleryController.php:32
* @route '/admin/gallery'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/gallery',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\GalleryController::store
* @see app/Http/Controllers/Admin/GalleryController.php:32
* @route '/admin/gallery'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\GalleryController::store
* @see app/Http/Controllers/Admin/GalleryController.php:32
* @route '/admin/gallery'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\GalleryController::store
* @see app/Http/Controllers/Admin/GalleryController.php:32
* @route '/admin/gallery'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\GalleryController::store
* @see app/Http/Controllers/Admin/GalleryController.php:32
* @route '/admin/gallery'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\GalleryController::destroy
* @see app/Http/Controllers/Admin/GalleryController.php:65
* @route '/admin/gallery/{gallery}'
*/
export const destroy = (args: { gallery: number | { id: number } } | [gallery: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/gallery/{gallery}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\GalleryController::destroy
* @see app/Http/Controllers/Admin/GalleryController.php:65
* @route '/admin/gallery/{gallery}'
*/
destroy.url = (args: { gallery: number | { id: number } } | [gallery: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { gallery: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { gallery: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            gallery: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        gallery: typeof args.gallery === 'object'
        ? args.gallery.id
        : args.gallery,
    }

    return destroy.definition.url
            .replace('{gallery}', parsedArgs.gallery.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\GalleryController::destroy
* @see app/Http/Controllers/Admin/GalleryController.php:65
* @route '/admin/gallery/{gallery}'
*/
destroy.delete = (args: { gallery: number | { id: number } } | [gallery: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\GalleryController::destroy
* @see app/Http/Controllers/Admin/GalleryController.php:65
* @route '/admin/gallery/{gallery}'
*/
const destroyForm = (args: { gallery: number | { id: number } } | [gallery: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\GalleryController::destroy
* @see app/Http/Controllers/Admin/GalleryController.php:65
* @route '/admin/gallery/{gallery}'
*/
destroyForm.delete = (args: { gallery: number | { id: number } } | [gallery: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const GalleryController = { index, store, destroy }

export default GalleryController