import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\UserOrderController::index
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/user/orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserOrderController::index
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserOrderController::index
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserOrderController::index
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserOrderController::index
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserOrderController::index
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserOrderController::index
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
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
* @see \App\Http\Controllers\UserOrderController::enrollments
* @see app/Http/Controllers/UserOrderController.php:30
* @route '/user/enrollments'
*/
export const enrollments = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: enrollments.url(options),
    method: 'get',
})

enrollments.definition = {
    methods: ["get","head"],
    url: '/user/enrollments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserOrderController::enrollments
* @see app/Http/Controllers/UserOrderController.php:30
* @route '/user/enrollments'
*/
enrollments.url = (options?: RouteQueryOptions) => {
    return enrollments.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserOrderController::enrollments
* @see app/Http/Controllers/UserOrderController.php:30
* @route '/user/enrollments'
*/
enrollments.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: enrollments.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserOrderController::enrollments
* @see app/Http/Controllers/UserOrderController.php:30
* @route '/user/enrollments'
*/
enrollments.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: enrollments.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserOrderController::enrollments
* @see app/Http/Controllers/UserOrderController.php:30
* @route '/user/enrollments'
*/
const enrollmentsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: enrollments.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserOrderController::enrollments
* @see app/Http/Controllers/UserOrderController.php:30
* @route '/user/enrollments'
*/
enrollmentsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: enrollments.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserOrderController::enrollments
* @see app/Http/Controllers/UserOrderController.php:30
* @route '/user/enrollments'
*/
enrollmentsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: enrollments.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

enrollments.form = enrollmentsForm

const UserOrderController = { index, enrollments }

export default UserOrderController