import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\UserOrderController::orders
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
export const orders = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: orders.url(options),
    method: 'get',
})

orders.definition = {
    methods: ["get","head"],
    url: '/user/orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserOrderController::orders
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
orders.url = (options?: RouteQueryOptions) => {
    return orders.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserOrderController::orders
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
orders.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: orders.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserOrderController::orders
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
orders.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: orders.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserOrderController::orders
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
const ordersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orders.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserOrderController::orders
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
ordersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orders.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserOrderController::orders
* @see app/Http/Controllers/UserOrderController.php:13
* @route '/user/orders'
*/
ordersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orders.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

orders.form = ordersForm

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

const user = {
    orders: Object.assign(orders, orders),
    enrollments: Object.assign(enrollments, enrollments),
}

export default user