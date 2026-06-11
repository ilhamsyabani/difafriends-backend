import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CompanionController::index
* @see app/Http/Controllers/Admin/CompanionController.php:15
* @route '/admin/companions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/companions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CompanionController::index
* @see app/Http/Controllers/Admin/CompanionController.php:15
* @route '/admin/companions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CompanionController::index
* @see app/Http/Controllers/Admin/CompanionController.php:15
* @route '/admin/companions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CompanionController::index
* @see app/Http/Controllers/Admin/CompanionController.php:15
* @route '/admin/companions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CompanionController::index
* @see app/Http/Controllers/Admin/CompanionController.php:15
* @route '/admin/companions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CompanionController::index
* @see app/Http/Controllers/Admin/CompanionController.php:15
* @route '/admin/companions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CompanionController::index
* @see app/Http/Controllers/Admin/CompanionController.php:15
* @route '/admin/companions'
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
* @see \App\Http\Controllers\Admin\CompanionController::schedules
* @see app/Http/Controllers/Admin/CompanionController.php:27
* @route '/admin/companions/{user}/schedules'
*/
export const schedules = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: schedules.url(args, options),
    method: 'get',
})

schedules.definition = {
    methods: ["get","head"],
    url: '/admin/companions/{user}/schedules',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CompanionController::schedules
* @see app/Http/Controllers/Admin/CompanionController.php:27
* @route '/admin/companions/{user}/schedules'
*/
schedules.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return schedules.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CompanionController::schedules
* @see app/Http/Controllers/Admin/CompanionController.php:27
* @route '/admin/companions/{user}/schedules'
*/
schedules.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: schedules.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CompanionController::schedules
* @see app/Http/Controllers/Admin/CompanionController.php:27
* @route '/admin/companions/{user}/schedules'
*/
schedules.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: schedules.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CompanionController::schedules
* @see app/Http/Controllers/Admin/CompanionController.php:27
* @route '/admin/companions/{user}/schedules'
*/
const schedulesForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: schedules.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CompanionController::schedules
* @see app/Http/Controllers/Admin/CompanionController.php:27
* @route '/admin/companions/{user}/schedules'
*/
schedulesForm.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: schedules.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CompanionController::schedules
* @see app/Http/Controllers/Admin/CompanionController.php:27
* @route '/admin/companions/{user}/schedules'
*/
schedulesForm.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: schedules.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

schedules.form = schedulesForm

const companions = {
    index: Object.assign(index, index),
    schedules: Object.assign(schedules, schedules),
}

export default companions