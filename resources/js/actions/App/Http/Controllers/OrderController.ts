import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\OrderController::webhook
* @see app/Http/Controllers/OrderController.php:92
* @route '/webhook/midtrans'
*/
export const webhook = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

webhook.definition = {
    methods: ["post"],
    url: '/webhook/midtrans',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\OrderController::webhook
* @see app/Http/Controllers/OrderController.php:92
* @route '/webhook/midtrans'
*/
webhook.url = (options?: RouteQueryOptions) => {
    return webhook.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrderController::webhook
* @see app/Http/Controllers/OrderController.php:92
* @route '/webhook/midtrans'
*/
webhook.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrderController::webhook
* @see app/Http/Controllers/OrderController.php:92
* @route '/webhook/midtrans'
*/
const webhookForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: webhook.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrderController::webhook
* @see app/Http/Controllers/OrderController.php:92
* @route '/webhook/midtrans'
*/
webhookForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: webhook.url(options),
    method: 'post',
})

webhook.form = webhookForm

/**
* @see \App\Http\Controllers\OrderController::store
* @see app/Http/Controllers/OrderController.php:30
* @route '/orders'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/orders',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\OrderController::store
* @see app/Http/Controllers/OrderController.php:30
* @route '/orders'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrderController::store
* @see app/Http/Controllers/OrderController.php:30
* @route '/orders'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrderController::store
* @see app/Http/Controllers/OrderController.php:30
* @route '/orders'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrderController::store
* @see app/Http/Controllers/OrderController.php:30
* @route '/orders'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\OrderController::index
* @see app/Http/Controllers/OrderController.php:79
* @route '/orders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OrderController::index
* @see app/Http/Controllers/OrderController.php:79
* @route '/orders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrderController::index
* @see app/Http/Controllers/OrderController.php:79
* @route '/orders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrderController::index
* @see app/Http/Controllers/OrderController.php:79
* @route '/orders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OrderController::index
* @see app/Http/Controllers/OrderController.php:79
* @route '/orders'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrderController::index
* @see app/Http/Controllers/OrderController.php:79
* @route '/orders'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrderController::index
* @see app/Http/Controllers/OrderController.php:79
* @route '/orders'
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

const OrderController = { webhook, store, index }

export default OrderController